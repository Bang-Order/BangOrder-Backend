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

//AuthController
Route::prefix('auth')->group(function () {
    Route::post('/', 'AuthController@auth');
    Route::post('/login', 'AuthController@login');
    Route::post('/logout', 'AuthController@logout');
});

Route::get('/restaurants/{restaurant}/menu-categories/menus', 'MenuCategoryController@indexWithMenu');

Route::get('/restaurants/{restaurant}/orders/history', 'OrderController@indexAll');

Route::apiResources([
    'restaurants' => 'RestaurantController',
    'restaurants.menu-categories' => 'MenuCategoryController',
    'restaurants.menus' => 'MenuController',
    'restaurants.orders' => 'OrderController',
    'restaurants.tables' => 'RestaurantTableController'
]);
