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
// middleware('throttle:10,10')


Route::get('delete', 'Merchant\RequestController@deleterecord');

Route::post('information', 'Merchant\RequestController@information');

Route::get('status', 'Merchant\RequestController@checkridestatus');


Route::post('request', 'Merchant\RequestController@apirequest');

Route::get('checkdriver', 'Merchant\RequestController@checkdriver');

Route::post('drupp', 'Merchant\RequestController@drupp');
// Route::get('customer/checkout/{param}', 'Merchant\RequestController@apiCheckoutUrl')->name('customer.api.checkout');
// Route::get('customer/checkout/view', 'Merchant\RequestController@apiCheckoutView')->name('customer.api.checkout.view');