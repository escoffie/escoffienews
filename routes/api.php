<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/logs', [NotificationController::class, 'index']);
Route::post('/notifications', [NotificationController::class, 'store']);
