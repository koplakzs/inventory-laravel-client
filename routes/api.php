<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::post('/auth', [AuthController::class, 'auth']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', [MainController::class, 'index']);
    Route::apiResource('category', CategoryController::class);
    Route::get('categories/all/paginated', [CategoryController::class, 'getAllPaginated']);
    Route::apiResource('product', ProductController::class);
    Route::get('products/all/paginated', [ProductController::class, 'getAllPaginated']);
});
