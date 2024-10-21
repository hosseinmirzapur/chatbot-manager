<?php

use App\Http\Controllers\CorporateController;
use Illuminate\Support\Facades\Route;

Route::prefix('/corporates')->group(function() {
    Route::get('/', [CorporateController::class, 'index']);
    Route::post('/', [CorporateController::class, 'store']);
    Route::post('/{slug}/update', [CorporateController::class, 'update']);
    Route::post('/{slug}/delete', [CorporateController::class, 'destroy']);
});
