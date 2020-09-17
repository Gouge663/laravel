<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller {
    /**
     * 展示文件上传表单页面
     */
    public function index() {
        return view("test");
    }

    /**
     * 文件上传
     */
    public function uploadFile(Request $request) {
        $file =$request->file('file');
        // 此时 $this->upload如果成功就返回文件名不成功返回false
        $fileName = $this->upload($file);
        if ($fileName){
            return $fileName;
        }
        return '上传失败';
    }

    /**
     * 验证文件是否合法
     */
    public function upload($file, $disk='public') {
        // 1.是否上传成功
        if (! $file->isValid()) {
            return false;
        }

        // 2.是否符合文件类型 getClientOriginalExtension 获得文件后缀名
        $fileExtension = $file->getClientOriginalExtension();

        $name = $file->getClientOriginalName();
        // 3.判断大小是否符合 2M
        $tmpFile = $file->getRealPath();
        if (filesize($tmpFile) >= 2048000) {
            return false;
        }

        // 4.是否是通过http请求表单提交的文件
        if (! is_uploaded_file($tmpFile)) {
            return false;
        }

        // 5.每天一个文件夹,分开存储, 生成一个随机文件名
        $fileName = date('Y_m_d').'/'.md5($name).'.'. $fileExtension;
        if (Storage::disk($disk)->put($fileName, file_get_contents($tmpFile)) ){
            return Storage::url($fileName);
        }
    }
}
