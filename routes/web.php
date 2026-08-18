<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserLookupController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArsipController;

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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/dashboard/superadmin', [DashboardController::class, 'superadmin'])->name('dashboard.superadmin');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/user', [DashboardController::class, 'user'])->name('dashboard.user');

    Route::get('/users/search', [UserLookupController::class, 'search'])->name('users.search');

    Route::get('/template-surat', [TemplateSuratController::class, 'index'])->name('template-surat.index');
    Route::get('/template-surat/create', [TemplateSuratController::class, 'create'])->name('template-surat.create');
    Route::post('/template-surat', [TemplateSuratController::class, 'store'])->name('template-surat.store');
    Route::get('/template-surat/{templateSurat}/edit', [TemplateSuratController::class, 'edit'])->name('template-surat.edit');
    Route::put('/template-surat/{templateSurat}', [TemplateSuratController::class, 'update'])->name('template-surat.update');
    Route::delete('/template-surat/{templateSurat}', [TemplateSuratController::class, 'destroy'])->name('template-surat.destroy');
    Route::post('/template-surat/parse-preview', [TemplateSuratController::class, 'parsePreview'])->name('template-surat.parse-preview');

    Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
    Route::get('/surat/create/{template}', [SuratController::class, 'form'])->name('surat.form');
    Route::post('/surat/create/{template}', [SuratController::class, 'store'])->name('surat.store');
    Route::get('/surat/{surat}', [SuratController::class, 'show'])->name('surat.show');
    Route::get('/surat/{surat}/cetak', [SuratController::class, 'cetak'])->name('surat.cetak');
    Route::post('/surat/{surat}/upload-arsip', [SuratController::class, 'uploadArsip'])->name('surat.upload-arsip');

    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});