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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix' => 'v1'], function($router) {

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
});