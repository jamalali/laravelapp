<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OmetriaBISController;
use App\Http\Controllers\VariantsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('bisnotify',[OmetriaBISController::class, 'signupEvent']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::name('products.')->prefix('products/{country}')->group(function() {
        Route::get('/', [ProductsController::class, 'index'])->name('index');

        Route::get('sync', [ProductsController::class, 'sync'])->name('sync');
        Route::get('ometria-up', [ProductsController::class, 'ometriaUp'])->name('ometria_up');
        Route::get('{product}', [ProductsController::class, 'show'])->name('show');

        Route::name('variants.')->group(function() {
            Route::get('{product}/variants/{variant}', [VariantsController::class, 'show'])->name('show');
            Route::get('{product}/variants/{variant}/ometria-up', [VariantsController::class, 'ometriaUp'])->name('ometria_up');
        });
    });
});


