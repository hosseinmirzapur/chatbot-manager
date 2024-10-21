<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/corporates')->group(function() {
    Route::get('/', [CorporateController::class, 'index']);
    Route::post('/', [CorporateController::class, 'store']);
    Route::post('/{corporate}/update', [CorporateController::class, 'update']);
    Route::post('/{corporate}/delete', [CorporateController::class, 'destroy']);
    Route::post('/{corporate}/chat', [CorporateController::class, 'chat'])
        ->middleware('auth:corp');
});

Route::prefix('/chats')->group(function () {
    Route::get('/{chat}/messages', [ChatController::class, 'messages']);
    Route::post('/{chat}/messages', [ChatController::class, 'sendMessage']);
});
