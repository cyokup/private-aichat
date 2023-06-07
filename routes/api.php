<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


// 小程序登陆
Route::get("mobile", [\App\Http\Controllers\UserController::class, "mobile"]);
Route::get("update_form", [\App\Http\Controllers\UserController::class, "updateForm"]);

Route::get("test1", [\App\Http\Controllers\UserController::class, "test1"]);


Route::get("test", [\App\Http\Controllers\UserController::class, "test"]);

// 消息列表
Route::any("msg_list", [\App\Http\Controllers\UserController::class, "msgList"]);
// 发送消息
Route::any("send_msg", [\App\Http\Controllers\UserController::class, "sendMsg"]);
