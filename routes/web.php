<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserLookupController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::middleware(['auth', 'role:Super Admin'])->group(function () {
        Route::get('/dashboard/super-admin', fn() => view('dashboard.super-admin'))->name('dashboard.superadmin');
    });

    Route::middleware(['auth', 'role:Super Admin,Admin'])->group(function () {
        Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
    });

    Route::middleware(['auth', 'role:Super Admin,Admin,User'])->group(function () {
        Route::get('/dashboard/user', fn() => view('dashboard.user'))->name('dashboard.user');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/users/search', [UserLookupController::class, 'search'])->name('users.search');
    });
    
    Route::get('/', function () {
        return view('home');
    });
});