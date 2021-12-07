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
    Route::post('/register/account', 'AuthController@registerAccount');
    Route::post('/register/profile', 'AuthController@registerProfile');
    Route::post('/logout', 'AuthController@logout');
    Route::get('/email/verify/{id}', 'AuthController@verifyEmail')->name('verification.verify');
    Route::post('/email/resend', 'AuthController@resendEmail')->name('verification.resend');
    Route::post('/password/send-email', 'AuthController@sendResetPassword');
    Route::post('/password/reset', 'AuthController@resetPassword');
});

Route::prefix('notify')->group(function () {
    Route::post('/order', 'XenditController@orderNotify');
    Route::post('/withdraw', 'XenditController@withdrawNotify');
});

Route::post('/orders/history', 'OrderController@indexArray');

Route::prefix('/restaurants/{restaurant}')->group(function () {
    Route::get('/dashboard', 'RestaurantController@showDashboard');
    Route::post('/withdraw', 'BankAccountController@withdraw');
    Route::post('/change-password', 'RestaurantController@changePassword');
    Route::get('/menu-categories/menus', 'MenuCategoryController@indexWithMenu');
    Route::get('/orders/history', 'OrderController@indexAll');
    Route::get('/tables/{table}/downloadQRCode', 'RestaurantTableController@getQRCode');
});

Route::apiResources([
    'restaurants' => 'RestaurantController',
    'restaurants.menu-categories' => 'MenuCategoryController',
    'restaurants.menus' => 'MenuController',
    'restaurants.orders' => 'OrderController',
    'restaurants.tables' => 'RestaurantTableController'
]);
