<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PreferenceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\WarrantyController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\WarrantyController as AdminWarrantyController;
use App\Http\Controllers\Api\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Api\Admin\StoreController as AdminStoreController;
use App\Http\Controllers\Api\Admin\PreferenceController as AdminPreferenceController;


Route::prefix('auth')->group(function () {
    Route::post('/register',                [AuthController::class, 'register']);
    Route::post('/login',                   [AuthController::class, 'login']);
    Route::post('/forgot-password',         [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',          [AuthController::class, 'resetPassword']);
    Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail']);
});


Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout',              [AuthController::class, 'logout']);
    Route::get('/me',                   [AuthController::class, 'me']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
});


Route::prefix('categories')->group(function () {
    Route::get('/',       [CategoryController::class, 'index']);
    Route::get('/{slug}', [CategoryController::class, 'show']);
});


Route::prefix('products')->group(function () {
    Route::get('/',               [ProductController::class, 'index']);
    Route::get('/featured',       [ProductController::class, 'featured']);
    Route::get('/{slug}',         [ProductController::class, 'show']);
    Route::get('/{slug}/related', [ProductController::class, 'related']);
});

Route::prefix('blogs')->group(function () {
    Route::get('/',           [BlogController::class, 'index']);
    Route::get('/recent',     [BlogController::class, 'recent']);
    Route::get('/categories', [BlogController::class, 'categories']);
    Route::get('/{slug}',     [BlogController::class, 'show']);
});


Route::prefix('stores')->group(function () {
    Route::get('/',       [StoreController::class, 'index']);
    Route::get('/search', [StoreController::class, 'search']);
    Route::get('/cities', [StoreController::class, 'cities']);
    Route::get('/states', [StoreController::class, 'states']);
    Route::get('/{id}',   [StoreController::class, 'show']);
});


Route::get('/reviews/{slug}', [ReviewController::class, 'index']);


Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe',   [NewsletterController::class, 'subscribe']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
});


Route::get('/preferences/recommendations', [PreferenceController::class, 'recommendations']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/cart',         [CartController::class, 'index']);
    Route::post('/cart',        [CartController::class, 'store']);
    Route::patch('/cart/{id}',  [CartController::class, 'update']);
    Route::delete('/cart',      [CartController::class, 'clear']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    Route::get('/orders',                       [OrderController::class, 'index']);
    Route::post('/orders',                      [OrderController::class, 'store']);
    Route::get('/orders/{orderNumber}',         [OrderController::class, 'show']);
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel']);

    Route::get('/account',          [AccountController::class, 'show']);
    Route::put('/account',          [AccountController::class, 'update']);
    Route::put('/account/password', [AccountController::class, 'changePassword']);
    Route::delete('/account',       [AccountController::class, 'destroy']);

    Route::get('/warranties',          [WarrantyController::class, 'index']);
    Route::post('/warranties',         [WarrantyController::class, 'store']);
    Route::get('/warranties/{number}', [WarrantyController::class, 'show']);

    Route::post('/reviews',              [ReviewController::class, 'store']);
    Route::post('/reviews/{id}/helpful', [ReviewController::class, 'helpful']);

    Route::post('/preferences', [PreferenceController::class, 'store']);
});


Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/products',         [AdminProductController::class, 'index']);
    Route::post('/products',        [AdminProductController::class, 'store']);
    Route::put('/products/{id}',    [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    Route::get('/orders',        [AdminOrderController::class, 'index']);
    Route::patch('/orders/{id}', [AdminOrderController::class, 'update']);

    Route::get('/users',        [AdminUserController::class, 'index']);
    Route::patch('/users/{id}', [AdminUserController::class, 'update']);

    Route::get('/warranties',        [AdminWarrantyController::class, 'index']);
    Route::patch('/warranties/{id}', [AdminWarrantyController::class, 'update']);

    Route::get('/blogs',         [AdminBlogController::class, 'index']);
    Route::post('/blogs',        [AdminBlogController::class, 'store']);
    Route::put('/blogs/{id}',    [AdminBlogController::class, 'update']);
    Route::delete('/blogs/{id}', [AdminBlogController::class, 'destroy']);

    Route::get('/reviews',         [AdminReviewController::class, 'index']);
    Route::patch('/reviews/{id}',  [AdminReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy']);

    Route::get('/stores',               [AdminStoreController::class, 'index']);
    Route::post('/stores',              [AdminStoreController::class, 'store']);
    Route::put('/stores/{id}',          [AdminStoreController::class, 'update']);
    Route::delete('/stores/{id}',       [AdminStoreController::class, 'destroy']);
    Route::patch('/stores/{id}/toggle', [AdminStoreController::class, 'toggle']);

    Route::get('/preferences',         [AdminPreferenceController::class, 'index']);
    Route::delete('/preferences/{id}', [AdminPreferenceController::class, 'destroy']);
});