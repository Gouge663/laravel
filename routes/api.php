<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', function (Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first(['id','name','password']);

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response([
            'message' => ['邮箱或者密码错误']
        ], 201);
    }

    $token = $user->createToken('my-app-token')->plainTextToken;

    $response = [
        'user' => $user,
        'token' => $token
    ];

    return response($response, 201);
});
Route::get('/article', 'ArticleController@index');
Route::get('/article/{id}', 'ArticleController@show');
Route::post('/article/{id}', 'ArticleController@destroy');
Route::post('/article', 'ArticleController@store');
Route::post('/tag', 'TagController@store');
Route::get('/tag', 'TagController@index');
Route::get('/upload',  'UploadController@index');
Route::post('/upload', 'UploadController@uploadFile');
Route::post('/register', 'UserController@register');
Route::get('/logout', 'UserController@logout');
