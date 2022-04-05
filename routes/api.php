<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OmetriaBISController;

use App\Http\Controllers\ShopifyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
| 
|                SIMON REMINDER - Loads at ____/api/ROUTE - Dictated by Providors\RouteServiceProvidor.php 
|
|                                 Route::prefix('api')
|                                   ->middleware('api')
|                                   ->namespace($this->namespace)
|                       vs
|                                 Route::middleware('api')
|                                   ->namespace($this->namespace)
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('webhook.')->middleware('shopify')->group(function() {
    Route::post('/product/{country}/', [ShopifyController::class, 'product'])->name('product');
});

Route::match(array('GET','POST'),'bis-signup', 'App\Http\Controllers\BisSignupController@signupEvent');

Route::match(array('GET','POST'),'segment-signup', 'App\Http\Controllers\SegSignupController@signupEvent');

Route::match(array('GET','POST'),'typeform', 'App\Http\Controllers\TypeformController@signupEvent');
