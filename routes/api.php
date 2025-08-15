<?php

use Illuminate\Support\Facades\Route;
use Towoju\One2OneCalls\Http\Controllers\CallController;
use Towoju\One2OneCalls\Http\Middleware\EnsureCanInitiateCall;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::post('/one2one/availability', [CallController::class, 'setAvailability']);
    Route::get('/one2one/availability/{userId}', [CallController::class, 'getAvailability']);

    Route::post('/one2one/calls', [CallController::class, 'initiate'])->middleware(EnsureCanInitiateCall::class);
    Route::post('/one2one/calls/{uuid}/accept', [CallController::class, 'accept']);
    Route::post('/one2one/calls/{uuid}/decline', [CallController::class, 'decline']);
    Route::post('/one2one/calls/{uuid}/end', [CallController::class, 'end']);
});
