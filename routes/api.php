<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\TaskController;

Route::prefix('v1')->group(function () {
// User Management
Route::post('/users', [UserController::class, 'store']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);

// Task Management
Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/tasks', [TaskController::class, 'index']);
Route::get('/tasks/{id}', [TaskController::class, 'show']);
Route::put('/tasks/{id}', [TaskController::class, 'update']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});