<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register',          [AuthController::class, 'register']);
    Route::post('/login',             [AuthController::class, 'login']);
    Route::post('/forgot-password',   [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',    [AuthController::class, 'resetPassword']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);
});

Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout',                [AuthController::class, 'logout']);
    Route::get('/me',                     [AuthController::class, 'me']);
    Route::post('/resend-verification',   [AuthController::class, 'resendVerification']);
});

Route::prefix('categories')->group(function () {
    Route::get('/',        [CategoryController::class, 'index']);
    Route::get('/{slug}',  [CategoryController::class, 'show']);
});

Route::prefix('products')->group(function () {
    Route::get('/',                    [ProductController::class, 'index']);
    Route::get('/featured',            [ProductController::class, 'featured']);
    Route::get('/{slug}',              [ProductController::class, 'show']);
    Route::get('/{slug}/related',      [ProductController::class, 'related']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart',        [CartController::class, 'index']);
    Route::post('/cart',       [CartController::class, 'store']);
    Route::patch('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart',     [CartController::class, 'clear']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    Route::get('/orders',                          [OrderController::class, 'index']);
    Route::post('/orders',                         [OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}',            [OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel',    [OrderController::class, 'cancel']);

    Route::get('/account',           [AccountController::class, 'show']);
    Route::put('/account',           [AccountController::class, 'update']);
    Route::put('/account/password',  [AccountController::class, 'changePassword']);
    Route::delete('/account',        [AccountController::class, 'destroy']);
});
