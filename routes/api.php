<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/corporates')->group(function () {
    Route::get('/{corporate:slug}', [CorporateController::class, 'show']);
    Route::post('/{corporate:slug}/chat/{botType}', [CorporateController::class, 'chat'])
        ->middleware('corp');
    Route::get('/{corporate:slug}/chats', [CorporateController::class, 'chatHistory']);
});

Route::prefix('/chats')->group(function () {
    Route::get('/{chat:slug}/messages', [ChatController::class, 'messages']);
    Route::post('/{chat:slug}/messages', [ChatController::class, 'sendMessage'])
        ->middleware('corp');
});
