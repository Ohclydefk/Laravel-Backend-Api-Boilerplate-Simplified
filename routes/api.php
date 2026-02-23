<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
