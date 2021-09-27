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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/menu-category', 'MenuCategoryController@index');
Route::get('/menu-category/{id}', 'MenuCategoryController@show');
Route::post('/menu-category', 'MenuCategoryController@store');
Route::put('/menu-category', 'MenuCategoryController@update');
Route::delete('/menu-category', 'MenuCategoryController@destroy');

Route::get('/menu', 'MenuController@index');

// Route::get('/mobile/homepage', 'MenuCategoryController@');