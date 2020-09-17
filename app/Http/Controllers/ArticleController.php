<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $req = $request->input();
        $data = Redis::get('list'.$req['page']);
        $data = unserialize($data);
        if(!$data){
            $data = Article::with(['user'])->cacheFor(now()->addHours(24))->cacheTags(['latest:articles'])->when($req,function($query,$req){
                if(array_key_exists('tag',$req)){
                    $query->where('tag','like',$req['tag']);
                }
            })->orderBy('id','desc')->paginate($req['pagesize'])->toArray();
            Redis::set('list'.$req['page'],serialize($data));
        }
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param =  $request->input();
        $article = New Article;

        if(array_key_exists('id',$param)){
            $article = Article::find($param['id']);
        }
        $message = [
            'required' => '请填写:attribute',
        ];

        $validator = Validator::make($param, [
            'title' => 'required|max:255',
            'description' => 'required',
            'tag' => 'required',
            'content' => 'required',
        ],$message);

        DB::beginTransaction();
        try{
            if($validator->fails()){
                $errors = $validator->errors();
                throw new \Exception($errors->first());
            }

            $article->title = $param['title'];
            $article->description = $param['description'];
            $article->tag = $param['tag'];
            $article->pic = $param['pic']?json_encode($param['pic']):'';
            $article->content = $param['content'];
            $status =  $article->save();
            if(!$status){
                throw new \Exception('网络开小差了');
            }
            Db::commit();
        } catch(\Exception $e) {
            DB::rollBack();
            return response()->json(['code' =>0,'message'=>$e->getMessage()]);
        }

        if(array_key_exists('id',$param) && $status){
            return response()->json([
                'code' =>1,
                'message' => '修改成功'
            ], 200);
        }else{
            return response()->json([
                'code' =>1,
                'message' => '添加成功'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Article::where('id',$id)->first();
        $data['tag'] = explode(",",$data['tag']);
        $data['pic'] = json_decode($data['pic'],true);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Article::destroy($id);
        return response()->json([
            'message' => '删除成功'
        ], 200);
    }
}
