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
    Route::group(['prefix'=>'index','middleware'=>'throttle:100,1'],function () {
        Route::get('/', 'IndexController@index'); //首页
    });

	Route::group(['prefix'=>'order','middleware'=>'throttle:100,1'],function () {
		Route::get('/', 'OrderController@index'); //首页
		Route::get('add', 'OrderController@addOrder');
		Route::get('pay', 'OrderController@pay');
	});

    Route::any('git','GitController@index');

    Route::any('wechat','WechatController@serve');

    Route::get('users','UsersController@users');

    Route::get('delay','IndexController@delay');
    Route::get('tt','IndexController@tt');
    Route::get('t1','IndexController@t1');
    Route::get('t2','IndexController@t2');
    Route::get('search','IndexController@search');

    Route::get('github', 'GithubController@redirectToProvider');
    Route::get('github/callback', 'GithubController@handleProviderCallback');



});


