<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TiketController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user/events', [EventsController::class, 'userAllEvents']);
 // User routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events/{slug}/detail', [EventsController::class, 'DetailEvents']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::delete('/orders/{id}', [OrderController::class, 'delete']);
    Route::get('/user/order', [OrderController::class, 'userOrders']);
    Route::get('/orders/{id}/ticket', [OrderController::class, 'printTicket']);
    Route::post('/orders/{id}/upload-proof', [OrderController::class, 'uploadProof']);
    Route::get('/download-tiket/{id}', [TiketController::class, 'download'])->name('tiket.download');


    });
// admin routes
    Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::apiResource('/admin/events', EventsController::class);
    Route::get('/admin/orders', [OrderController::class, 'index']);
    Route::put('/orders/{id}/verification', [OrderController::class, 'verify']);
    });
});

