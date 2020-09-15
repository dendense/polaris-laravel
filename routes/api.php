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

/**
 * Group for non protected route
 */
Route::group([
    'prefix' => 'v1'], function() {

    Route::post('/user/login', [
        'uses' => 'AuthController@login'
    ]);

    Route::post('/user/register', [
        'uses' => 'AuthController@register'
    ]);

    Route::post('/user/logout', [
        'uses' => 'AuthController@logout'
    ]);

    Route::post('/user/refresh', [
        'uses' => 'AuthController@refresh'
    ]);

    Route::get('/user/user-profile', [
        'uses' => 'AuthController@userProfile'
    ]);

    Route::resource('/user', 'UserController', [
        'only' => ['update', 'show']
    ]);

    Route::post('/user/s', [
        'uses' => 'UserController@search'
    ]);

    Route::resource('/post/register', 'PostController', [
        'only' => ['store', 'destroy']
    ]);

    Route::resource('post', 'PostController' , [
        'except' => ['create', 'edit']
    ]);

    Route::post('/post/s', [
        'uses' => 'PostController@search'
    ]);

    Route::post('user/report', [
        'uses' => 'ReportController@reportUser'
    ]);

    Route::post('/post/report', [
        'uses' => 'ReportController@reportPost'
    ]);

    Route::get('user/chat/contact', [
        'uses' => 'MessagesController@get'
    ]);

    Route::post('user/conversation', [
        'uses' => 'MessagesController@send'
    ]);

    Route::get('user/conversation/{id}', [
        'uses' => 'MessagesController@getMessageFor'
    ]);

    Route::delete('user/conversation/{id}', [
        'uses' => 'MessagesController@deleteChat'
    ]);

    Route::delete('user/conversation/d/{id}', [
        'uses' => 'MessagesController@deleteMessages'
    ]);

    Route::resource('user/follow', 'FollowerController', [
        'except' => ['create', 'edit', 'update', 'show']
    ]);
});