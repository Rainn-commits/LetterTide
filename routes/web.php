<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserLookupController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\SuratController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt'); 
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::middleware('auth')->group(function () { 

    Route::get('/users/search', [UserLookupController::class, 'search'])->name('users.search');

    Route::get('/template-surat', [TemplateSuratController::class, 'index'])->name('template-surat.index');
    Route::get('/template-surat/create', [TemplateSuratController::class, 'create'])->name('template-surat.create');
    Route::post('/template-surat', [TemplateSuratController::class, 'store'])->name('template-surat.store');
    Route::get('/template-surat/{templateSurat}/edit', [TemplateSuratController::class, 'edit'])->name('template-surat.edit');
    Route::put('/template-surat/{templateSurat}', [TemplateSuratController::class, 'update'])->name('template-surat.update');
    Route::delete('/template-surat/{templateSurat}', [TemplateSuratController::class, 'destroy'])->name('template-surat.destroy');

    Route::post('/template-surat/parse-preview', [TemplateSuratController::class, 'parsePreview'])
        ->name('template-surat.parse-preview');
    
    Route::middleware(['auth'])->group(function () {
        Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
        Route::get('/surat/create/{template}', [SuratController::class, 'form'])->name('surat.form');
    });

    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/dashboard/super-admin', fn() => view('dashboard.super-admin'))->name('dashboard.superadmin');
    });

    Route::middleware('role:Super Admin,Admin')->group(function () {
        Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
    });

    Route::middleware('role:Super Admin,Admin,User')->group(function () {
        Route::get('/dashboard/user', fn() => view('dashboard.user'))->name('dashboard.user');
    });

});

