<?php

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

Route::group(['prefix' => 'v1', 'middleware' => ['api']], function() {
    Route::get('/', 'Api\v1\ImportController@index');
    Route::post('product', 'Api\v1\ImportController@product');
    Route::post('category', 'Api\v1\ImportController@category');
});