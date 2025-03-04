<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    // Router for managing events
    Route::apiResource('events', EventsController::class);
    // User routes
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders/user/{id}', [OrderController::class, 'userOrders']);
    Route::get('/orders/{id}/ticket', [OrderController::class, 'printTicket']);

    });

    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    //    admin routes
    });
});

