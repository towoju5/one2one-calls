<?php

use Illuminate\Support\Facades\Route;
use Towoju\One2OneCalls\Http\Controllers\SuperAdmin\CallPermissionController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/one2one/permissions', [CallPermissionController::class, 'index'])->name('one2one.calls.permissions.index');
    Route::patch('/one2one/permissions/{user}', [CallPermissionController::class, 'toggle'])->name('one2one.calls.permissions.toggle');
});
