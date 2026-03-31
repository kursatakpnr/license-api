<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserLicenseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::get('/users', [UserController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/users/{user}/licenses', [UserLicenseController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);