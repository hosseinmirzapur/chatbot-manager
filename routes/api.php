<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/corporates')->group(function() {
    Route::post('/{corporate:slug}/chat', [CorporateController::class, 'chat']);
    Route::get('/{corporate:slug}/chats', [CorporateController::class, 'chatHistory']);
});

Route::prefix('/chats')->group(function () {
    Route::get('/{chat:slug}/messages', [ChatController::class, 'messages']);
    Route::post('/{chat:slug}/messages', [ChatController::class, 'sendMessage']);
});
