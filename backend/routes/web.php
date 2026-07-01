<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Novels
        Route::prefix('novels')->name('novels.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\NovelController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Admin\NovelController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\NovelController::class, 'store'])->name('store');
            Route::get('{novel}/edit', [\App\Http\Controllers\Admin\NovelController::class, 'edit'])->name('edit');
            Route::put('{novel}', [\App\Http\Controllers\Admin\NovelController::class, 'update'])->name('update');
            Route::delete('{novel}', [\App\Http\Controllers\Admin\NovelController::class, 'destroy'])->name('destroy');
            Route::patch('{novel}/toggle', [\App\Http\Controllers\Admin\NovelController::class, 'toggle'])->name('toggle');

            // Entity via Novel
            Route::get('{novel}/entities', [\App\Http\Controllers\Admin\NovelController::class, 'entities'])->name('entities.index');
            Route::get('{novel}/entities/create', [\App\Http\Controllers\Admin\EntityController::class, 'create'])->name('entities.create');
            Route::post('{novel}/entities', [\App\Http\Controllers\Admin\EntityController::class, 'store'])->name('entities.store');
            Route::get('{novel}/entities/{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'show'])->name('entities.show');
            Route::get('{novel}/entities/{entity}/edit', [\App\Http\Controllers\Admin\EntityController::class, 'edit'])->name('entities.edit');
            Route::put('{novel}/entities/{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'update'])->name('entities.update');
            Route::delete('{novel}/entities/{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'destroy'])->name('entities.destroy');
            Route::patch('{novel}/entities/{entity}/toggle', [\App\Http\Controllers\Admin\EntityController::class, 'toggle'])->name('entities.toggle');
            Route::delete('{novel}/entities/{entity}/image', [\App\Http\Controllers\Admin\EntityController::class, 'destroyImage'])->name('entities.image.destroy');
        });

        // Entities
        Route::prefix('entities')->name('entities.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\EntityController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Admin\EntityController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\EntityController::class, 'store'])->name('store');
            Route::get('{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'show'])->name('show');
            Route::get('{entity}/edit', [\App\Http\Controllers\Admin\EntityController::class, 'edit'])->name('edit');
            Route::put('{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'update'])->name('update');
            Route::delete('{entity}', [\App\Http\Controllers\Admin\EntityController::class, 'destroy'])->name('destroy');
            Route::patch('{entity}/toggle', [\App\Http\Controllers\Admin\EntityController::class, 'toggle'])->name('toggle');
            Route::delete('{entity}/image', [\App\Http\Controllers\Admin\EntityController::class, 'destroyImage'])->name('image.destroy');
        });

        // Users — future
        Route::get('users', fn() => redirect()->route('admin.dashboard'))->name('users.index');
    });

});

Route::get('/', fn() => redirect()->route('admin.login'));