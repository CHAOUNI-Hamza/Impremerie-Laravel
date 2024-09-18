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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('auth/facebook', 'App\Http\Controllers\AuthController@redirectToFacebook');
Route::get('auth/facebook/callback', 'App\Http\Controllers\AuthController@handleFacebookCallback');

Route::get('auth/google', 'App\Http\Controllers\AuthController@redirectToGoogle');
Route::get('auth/google/callback', 'App\Http\Controllers\AuthController@handleGoogleCallback');






Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('store', 'App\Http\Controllers\AuthController@store');
    Route::post('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('me', 'App\Http\Controllers\AuthController@me');
    Route::post('forgot-password', 'App\Http\Controllers\AuthController@forgotpassword'); 
    Route::post('reset-password', 'App\Http\Controllers\AuthController@resetpassword');

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'users'

], function ($router) {

    Route::get('/', 'App\Http\Controllers\UserController@index');
Route::post('/', 'App\Http\Controllers\UserController@store');
Route::get('/{user}', 'App\Http\Controllers\UserController@show');
Route::put('/{user}', 'App\Http\Controllers\UserController@update');
Route::delete('/{user}', 'App\Http\Controllers\UserController@destroy');
Route::patch('/restore/{id}', 'App\Http\Controllers\UserController@restore');

});


Route::group([

    'middleware' => 'api',
    'prefix' => 'products'

], function ($router) {

    Route::get('', 'App\Http\Controllers\ProductController@index');
    Route::post('', 'App\Http\Controllers\ProductController@store');
    Route::get('/top-selling-products', 'App\Http\Controllers\ProductController@topSellingProducts');
    Route::get('/{product}', 'App\Http\Controllers\ProductController@show');
    Route::post('/{product}', 'App\Http\Controllers\ProductController@update');
    Route::delete('/{product}', 'App\Http\Controllers\ProductController@destroy');
    Route::patch('/restore/{id}', 'App\Http\Controllers\ProductController@restore');

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'categories'

], function ($router) {

    Route::get('', 'App\Http\Controllers\CategoryController@index');
    Route::post('', 'App\Http\Controllers\CategoryController@store');
    Route::get('/{category}', 'App\Http\Controllers\CategoryController@show');
    Route::post('/{category}', 'App\Http\Controllers\CategoryController@update');
    Route::delete('/{category}', 'App\Http\Controllers\CategoryController@destroy');
    Route::patch('/restore/{id}', 'App\Http\Controllers\CategoryController@restore');

});


Route::group([

    'middleware' => 'api',
    'prefix' => 'subscribes'

], function ($router) {

    Route::get('', 'App\Http\Controllers\SubscribeController@index');
    Route::post('', 'App\Http\Controllers\SubscribeController@store');
    Route::post('/{category}', 'App\Http\Controllers\SubscribeController@update');
    Route::delete('/{category}', 'App\Http\Controllers\SubscribeController@destroy');

});



Route::group([

    'middleware' => 'api',
    'prefix' => 'commandes'

], function ($router) {

    Route::get('', 'App\Http\Controllers\CommandeController@index');
    Route::post('', 'App\Http\Controllers\CommandeController@store');
    Route::get('/{commande}', 'App\Http\Controllers\CommandeController@show');
    Route::post('/{commande}', 'App\Http\Controllers\CommandeController@update');
    Route::delete('/{commande}', 'App\Http\Controllers\CommandeController@destroy');
    Route::patch('/restore/{id}', 'App\Http\Controllers\CommandeController@restore');

});


Route::group([

    'middleware' => 'api',
    'prefix' => 'commande-details'

], function ($router) {

    Route::get('', 'App\Http\Controllers\CommandeDetailsController@index');
    Route::get('/details-command-id/{id}', 'App\Http\Controllers\CommandeDetailsController@getDetailsByCommandId');
    Route::post('', 'App\Http\Controllers\CommandeDetailsController@store');
    Route::get('/{commandeDetails}', 'App\Http\Controllers\CommandeDetailsController@show');
    Route::post('/{commandeDetails}', 'App\Http\Controllers\CommandeDetailsController@update');
    Route::delete('/{commandeDetails}', 'App\Http\Controllers\CommandeDetailsController@destroy');
    Route::patch('/restore/{id}', 'App\Http\Controllers\CommandeDetailsController@restore');

});

