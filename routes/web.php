<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\KopSuratController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TemplateSuratController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\RetensiKriteriaController;
use App\Http\Controllers\EvaluasiRetensiController;
use App\Http\Controllers\SuratMasukController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'submitRequest'])->name('password.request.submit');

Route::middleware(['auth', 'active'])->group(function () {

    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/superadmin', [DashboardController::class, 'superAdmin'])->name('dashboard.super-admin');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');
    Route::get('/dashboard/user', [DashboardController::class, 'user'])->name('dashboard.user');
    Route::get('/users/search', [UserLookupController::class, 'search'])->name('users.search');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    Route::post('/arsip/surat-masuk', [SuratMasukController::class, 'store'])->name('arsip.surat-masuk.store');

    Route::get('/evaluasi-retensi', [EvaluasiRetensiController::class, 'index'])->name('evaluasi-retensi.index');
    Route::post('/evaluasi-retensi/{surat}/nilai', [EvaluasiRetensiController::class, 'simpanPenilaian'])->name('retensi.simpan-penilaian');
    Route::post('/evaluasi-retensi/{surat}/validasi', [EvaluasiRetensiController::class, 'validasi'])->name('retensi.validasi');

    Route::middleware('permission:kopsurat.manage')->group(function () {
        Route::get('/kop-surat', [KopSuratController::class, 'index'])->name('kop-surat.index');
        Route::get('/kop-surat/create', [KopSuratController::class, 'create'])->name('kop-surat.create');
        Route::post('/kop-surat', [KopSuratController::class, 'store'])->name('kop-surat.store');
        Route::get('/kop-surat/{pengaturanKopSurat}/preview', [KopSuratController::class, 'preview'])->name('kop-surat.preview');
        Route::get('/kop-surat/{pengaturanKopSurat}/edit', [KopSuratController::class, 'edit'])->name('kop-surat.edit');
        Route::put('/kop-surat/{pengaturanKopSurat}', [KopSuratController::class, 'update'])->name('kop-surat.update');
        Route::delete('/kop-surat/{pengaturanKopSurat}', [KopSuratController::class, 'destroy'])->name('kop-surat.destroy');
    });

    Route::match(['get', 'post'], '/surat/{surat}/ttd-digital', [SuratController::class, 'ttdDigital'])->name('surat.ttd-digital');
    Route::delete('/surat/{surat}', [SuratController::class, 'destroy'])->name('surat.destroy');
    Route::get('/surat/{surat}/edit', [SuratController::class, 'edit'])->name('surat.edit');
    Route::put('/surat/{surat}', [SuratController::class, 'update'])->name('surat.update');

    Route::middleware('permission:user.manage')->group(function () {
        Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('/pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
        Route::patch('/pengguna/{pengguna}/aktifkan', [PenggunaController::class, 'aktifkan'])->name('pengguna.aktifkan');
        Route::delete('/pengguna/{pengguna}/hapus-permanen', [PenggunaController::class, 'hapusPermanen'])->name('pengguna.hapus-permanen');
    });

    Route::middleware('permission:template.manage')->group(function () {
        Route::get('/template-surat', [TemplateSuratController::class, 'index'])->name('template-surat.index');
        Route::get('/template-surat/create', [TemplateSuratController::class, 'create'])->name('template-surat.create');
        Route::post('/template-surat', [TemplateSuratController::class, 'store'])->name('template-surat.store');
        Route::get('/template-surat/{templateSurat}/edit', [TemplateSuratController::class, 'edit'])->name('template-surat.edit');
        Route::put('/template-surat/{templateSurat}', [TemplateSuratController::class, 'update'])->name('template-surat.update');
        Route::delete('/template-surat/{templateSurat}', [TemplateSuratController::class, 'destroy'])->name('template-surat.destroy');
        Route::post('/template-surat/parse-preview', [TemplateSuratController::class, 'parsePreview'])->name('template-surat.parse-preview');
    });

    Route::middleware('permission:surat.create')->group(function () {
        Route::get('/surat/create', [SuratController::class, 'create'])->name('surat.create');
        Route::get('/surat/riwayat', [SuratController::class, 'riwayat'])->name('surat.riwayat');
        Route::get('/surat/create/{template}', [SuratController::class, 'form'])->name('surat.form');
        Route::post('/surat/create/{template}', [SuratController::class, 'store'])->name('surat.store');
    });

    Route::middleware('permission:surat.print')->group(function () {
        Route::get('/surat/{surat}', [SuratController::class, 'show'])->name('surat.show');
        Route::get('/surat/{surat}/cetak', [SuratController::class, 'cetak'])->name('surat.cetak');
        Route::post('/surat/{surat}/upload-arsip', [SuratController::class, 'uploadArsip'])->name('surat.upload-arsip');
    });

    Route::middleware('permission:role.manage')->group(function () {
        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
        Route::get('/role/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/role', [RoleController::class, 'store'])->name('role.store');
        Route::get('/role/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::put('/role/{role}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/role/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
    });

    Route::middleware('permission:log.view')->group(function () {
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
    });

    Route::middleware('permission:retensi.manage')->group(function () {
        Route::get('/retensi-kriteria', [RetensiKriteriaController::class, 'index'])->name('retensi-kriteria.index');
        Route::put('/retensi-kriteria/{retensiKriterion}', [RetensiKriteriaController::class, 'update'])->name('retensi-kriteria.update');
    });

});
