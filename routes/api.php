<?php
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventsController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::apiResource('events', EventsController::class);
});