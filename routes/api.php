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

Route::middleware('auth:api')->get('/restaurant', function (Request $request) {
    return $request->restaurant();
});

Route::get('/restaurants/{restaurant}/orders/history', 'OrderController@indexAll');

Route::apiResources([
    'restaurants.menu-categories' => 'MenuCategoryController',
    'restaurants.menus' => 'MenuController',
    'restaurants.orders' => 'OrderController'
]);

//Route::apiResource('restaurants.orders', 'OrderController');

//Route::get('/menu', 'MenuController@index');
