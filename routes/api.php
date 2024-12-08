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

Route::prefix('food')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'showById']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
});

Route::prefix('category')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}', [CategoryController::class, 'showById']);
    Route::get('/', [CategoryController::class, 'showBySearch']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{id}', [CategoryController::class, 'update']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});

Route::prefix('size')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [SizeController::class, 'index']);
    Route::get('/{id}', [SizeController::class, 'showById']);
    Route::post('/', [SizeController::class, 'store']);
    Route::put('/{id}', [SizeController::class, 'update']);
    Route::delete('/{id}', [SizeController::class, 'destroy']);
});
