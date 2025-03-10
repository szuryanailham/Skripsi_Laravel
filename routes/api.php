<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('user/events', [EventsController::class, 'userAllEvents']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
 // User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/{slug}/detail', [EventsController::class, 'DetailEvents']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::delete('/orders/{id}', [OrderController::class, 'delete']);
    Route::get('/user/order', [OrderController::class, 'userOrders']);
    Route::get('/orders/{id}/ticket', [OrderController::class, 'printTicket']);

    });
// admin routes
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::apiResource('/admin/events', EventsController::class);
    Route::get('/admin/orders', [OrderController::class, 'allOrderAdmin']);
    Route::put('/orders/{id}/verification', [OrderController::class, 'verify']);
    });
});

