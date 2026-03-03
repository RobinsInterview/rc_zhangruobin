<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::get('/notifications/{notificationId}', [NotificationController::class, 'show']);
});
