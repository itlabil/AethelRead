<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Aethel Read Admin Panel
|--------------------------------------------------------------------------
| Auth     : Laravel Session
| Consumer : Admin Panel (Blade)
|
*/

Route::prefix('admin')->name('admin.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | Guest Routes (belum login)
    |----------------------------------------------------------------------
    */
    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    });

    /*
    |----------------------------------------------------------------------
    | Authenticated Routes (sudah login)
    |----------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Novel, Entity, dll akan ditambahkan di Step 30–34
    });

});

// Redirect root ke admin login
Route::get('/', fn() => redirect()->route('admin.login'));