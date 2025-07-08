<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::controller(AuthController::class)->prefix('auth')->group(function () {
        Route::post('register', 'register')->name('auth.register');
        Route::post('login', 'login')->name('auth.login');
    });

    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('/', 'index')->name('products.index');
        Route::get('{id}', 'show')->name('products.show');
    });

    // Protected routes
    Route::middleware('auth')->group(function () {
        Route::controller(AuthController::class)->prefix('auth')->group(function () {
            Route::post('logout', 'logout')->name('auth.logout');
            Route::get('user', 'user')->name('auth.user');
        });

        Route::controller(WishlistController::class)->prefix('wishlist')->group(function () {
            Route::get('/', 'index')->name('wishlist.index');
            Route::post('/', 'store')->name('wishlist.store');
            Route::delete('/', 'destroy')->name('wishlist.destroy');
            Route::post('toggle', 'toggle')->name('wishlist.toggle');
            Route::get('check/{productId}', 'check')->name('wishlist.check');
            Route::get('count', 'count')->name('wishlist.count');
            Route::delete('clear', 'clear')->name('wishlist.clear');
        });
    });
});
