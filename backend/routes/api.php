<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Aethel Read
|--------------------------------------------------------------------------
| Base URL : /api/v1
| Auth     : JWT Bearer Token
| Consumer : Android Application
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Authentication Routes
    |----------------------------------------------------------------------
    */
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');

        Route::middleware(['auth:api', 'active'])->group(function () {
            Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout');
            Route::post('refresh', [\App\Http\Controllers\Api\AuthController::class, 'refresh'])->name('refresh');
            Route::get('me', [\App\Http\Controllers\Api\AuthController::class, 'me'])->name('me');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Novel Routes (Android)
    |----------------------------------------------------------------------
    */
    // Will be added in Step 24

    /*
    |----------------------------------------------------------------------
    | Entity Routes (Android)
    |----------------------------------------------------------------------
    */
    // Will be added in Step 25–26

});