<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/logs', [NotificationController::class, 'index']);
Route::delete('/logs', [NotificationController::class, 'destroyAll']);
Route::post('/notifications', [NotificationController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
