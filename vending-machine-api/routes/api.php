<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendingController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/user',  [UserController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

// Public product listing
Route::get('/products',      [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',     [AuthController::class, 'logout']);
    Route::post('/logout/all', [AuthController::class, 'logoutAll']);

    Route::get('/user/{id}',    [UserController::class, 'show']);
    Route::put('/user/{id}',    [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);

    // Seller-only product management
    Route::middleware('role:seller')->group(function () {
        Route::post('/products',        [ProductController::class, 'store']);
        Route::put('/products/{id}',    [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // Buyer-only vending operations
    Route::middleware('role:buyer')->group(function () {
        Route::post('/deposit', [VendingController::class, 'deposit']);
        Route::post('/buy',     [VendingController::class, 'buy']);
        Route::post('/reset',   [VendingController::class, 'reset']);
    });
});
