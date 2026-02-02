<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/signin', [AuthController::class, 'signin'])->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/signout', [AuthController::class, 'signout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);

    Route::prefix('basket')->group(function () {
        Route::get('/add', [BasketController::class, 'add']);
        Route::get('/{id}', [BasketController::class, 'update']);
        Route::get('/{id}', [BasketController::class, 'delete']);
        Route::get('/', [BasketController::class, 'list']);
        Route::get('/', [BasketController::class, 'clear']);
    });

    Route::prefix('wishlist')->group(function () {
        Route::post('/add', [WishlistController::class, 'add']);
        Route::delete('/{id}', [WishlistController::class, 'delete']);
        Route::get('/', [WishlistController::class, 'list']);
        Route::delete('/', [WishlistController::class, 'clear']);
    });
});

Route::prefix('sections')->group(function () {
    Route::get('/', [SectionsController::class, 'list']);
    Route::get('/{id}', [SectionsController::class, 'detail']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'list']);
    Route::get('/{id}', [ProductsController::class, 'detail']);
});