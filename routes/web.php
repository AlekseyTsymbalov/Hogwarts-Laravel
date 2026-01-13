<?php

use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/register',
    [RegisterController::class, 'show']
)->name('register.show');
Route::post(
    '/register',
    [RegisterController::class, 'store']
)->name('register.store');