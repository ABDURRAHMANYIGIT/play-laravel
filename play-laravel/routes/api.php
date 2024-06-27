<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/get-user', [AuthController::class, 'getAuthUser']);
    Route::get('/user/get-all', [UserController::class, 'index']);
    Route::get('/user/{id}', [UserController::class, 'show']);
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/{id}/messages', [ChatController::class, 'getChatMessages']);
    Route::post('/message/send', [MessageController::class, 'store']);
});
