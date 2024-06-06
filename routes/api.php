<?php

use App\Http\Controllers\AuthController;
//use App\Http\Controllers\DiaryController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/diaries', [DiaryController::class, 'index']);
    Route::post('/diaries', [DiaryController::class, 'store']);
    Route::get('/diaries/{id}', [DiaryController::class, 'show']);
    Route::put('/diaries/{id}', [DiaryController::class, 'update']);
    Route::delete('/diaries/{id}', [DiaryController::class, 'destroy']);
});
