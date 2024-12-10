<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SizeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::prefix('food')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'showById']);
    Route::post('/', [ProductController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/{id}', [ProductController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'showById']);
    Route::get('/', [CategoryController::class, 'showBySearch']);
    Route::post('/', [CategoryController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('size')->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::get('/{id}', [SizeController::class, 'showById']);
    Route::post('/', [SizeController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/{id}', [SizeController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [SizeController::class, 'destroy'])->middleware('auth:sanctum');
});
