<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserLookupController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt'); // Name ditambahkan di sini!
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () { 

    Route::get('/users/search', [UserLookupController::class, 'search'])->name('users.search');

    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/dashboard/super-admin', fn() => view('dashboard.super-admin'))->name('dashboard.superadmin');
    });

    Route::middleware('role:Super Admin,Admin')->group(function () {
        Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
    });

    Route::middleware('role:Super Admin,Admin,User')->group(function () {
        Route::get('/dashboard/user', fn() => view('dashboard.user'))->name('dashboard.user');
    });

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password'); // Adjust nama file blade kamu di sini
    })->name('password.request');

});