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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//リアルチャット　詳細ページ　参考になったを押したかどうか取得する
Route::get('realchat_flag', 'RealChatDetailController@getFlag');

//リアルチャット　詳細ページ　カウント操作
Route::post('realchat_count', 'RealChatDetailController@updateCount');
Route::get('/diamond_simulation', 'Diamond_simulation_Controller@make_tool')->name('index');
Route::get('/diamond_simulationVer02', 'Diamond_simulationVer02_Controller@make_tool')->name('index');