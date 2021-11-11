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
    Route::post('/register', 'AuthController@register');
    Route::post('/logout', 'AuthController@logout');
});

Route::post('/order/notify', 'XenditController@notify');

Route::get('/restaurants/{restaurant}/menu-categories/menus', 'MenuCategoryController@indexWithMenu');
Route::get('/restaurants/{restaurant}/orders/history', 'OrderController@indexAll');
Route::get('/restaurants/{restaurant}/tables/{table}/downloadQRCode', 'RestaurantTableController@getQRCode');

Route::apiResources([
    'restaurants' => 'RestaurantController',
    'restaurants.menu-categories' => 'MenuCategoryController',
    'restaurants.menus' => 'MenuController',
    'restaurants.orders' => 'OrderController',
    'restaurants.tables' => 'RestaurantTableController'
]);
