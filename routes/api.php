<?php

use Illuminate\Http\Request;

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
Route::prefix('v1')->namespace('Api\V1')->group(function () {
    //首页
    Route::prefix('index')->group(function () {
        Route::get('/', 'IndexController@index'); //首页
    });

    Route::any('git','GitController@index');

    Route::any('wechat','WechatController@serve');

    Route::get('users','UsersController@users');

    Route::get('delay','IndexController@delay');

    Route::get('login/github', 'GithubController@redirectToProvider');
    Route::get('login/github/callback', 'GithubController@handleProviderCallback');



});


