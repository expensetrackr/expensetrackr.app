<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('workspaces.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create')->middleware('accounts.create.wizard');
    Route::post('/accounts/create/{step}', [AccountController::class, 'store'])
        ->where('step', 'details|balance-and-currency|review')
        ->name('accounts.store');

    Route::prefix('settings')->group(function () {
        Route::get('/', function () {
            return Inertia::render('settings/show');
        })->name('settings.show');

        Route::get('/connected-accounts', function () {
            return Inertia::render('settings/connected-accounts/show');
        })->name('settings.connected-accounts.show');
    });
});

Route::post('/language', LanguageController::class)->name('language.store');

require __DIR__.'/socialstream.php';
require __DIR__.'/workspaces.php';
