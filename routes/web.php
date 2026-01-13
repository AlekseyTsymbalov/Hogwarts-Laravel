<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'show']);
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);