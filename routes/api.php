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

    Route::resource('/post/register', 'PostController', [
        'only' => ['store', 'destroy']
    ]);

    Route::resource('post', 'PostController' , [
        'except' => ['create', 'edit']
    ]);
});