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
    Route::prefix('novels')->name('novels.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NovelController::class, 'index'])->name('index');
        Route::get('{slug}', [\App\Http\Controllers\Api\NovelController::class, 'show'])->name('show');
    });

    /*
    |----------------------------------------------------------------------
    | Entity Routes (Android)
    |----------------------------------------------------------------------
    */
    Route::prefix('novels/{novelSlug}')->name('novels.')->group(function () {
        Route::get('entities', [\App\Http\Controllers\Api\EntityController::class, 'index'])->name('entities.index');
        Route::post('entities/sync', [\App\Http\Controllers\Api\EntityController::class, 'sync'])->name('entities.sync');
        Route::get('entities/{entitySlug}', [\App\Http\Controllers\Api\EntityController::class, 'show'])->name('entities.show');
    });

});