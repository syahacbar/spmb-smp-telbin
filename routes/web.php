<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/cek-status', [LandingController::class, 'checkStatus'])->name('status.check');
Route::get('/cek-status', fn () => redirect('/#cek-status'));

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
    Route::get('/daftar', [AuthController::class, 'register'])->name('register');
    Route::post('/daftar/cek-nisn', [AuthController::class, 'checkRegisterNisn'])->name('register.check-nisn');
    Route::post('/daftar', [AuthController::class, 'storeRegistration'])->name('register.store');
});

Route::middleware('spmb.auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/formulir', [FormulirController::class, 'create'])->name('formulir.create');
    Route::post('/formulir', [FormulirController::class, 'store'])->name('formulir.store');
    Route::get('/riwayat', [FormulirController::class, 'riwayat'])->name('formulir.riwayat');
    Route::get('/formulir/{formulir}/edit', [FormulirController::class, 'edit'])->name('formulir.edit');
    Route::put('/formulir/{formulir}', [FormulirController::class, 'update'])->name('formulir.update');
    Route::get('/formulir/{formulir}/periksa', [FormulirController::class, 'periksa'])->name('formulir.periksa');
    Route::post('/formulir/{formulir}/kirim', [FormulirController::class, 'kirim'])->name('formulir.kirim');
    Route::get('/formulir/{formulir}/cetak', [FormulirController::class, 'cetak'])->name('formulir.cetak');

    Route::middleware('spmb.admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/pendaftar', [AdminController::class, 'pendaftar'])->name('pendaftar');
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
        Route::post('/pengguna', [AdminController::class, 'storePengguna'])->name('pengguna.store');
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengguna/{pengguna}/verifikasi', [AdminController::class, 'verifikasiPengguna'])->name('pengguna.verifikasi');
        Route::post('/pengguna/{pengguna}/toggle-active', [AdminController::class, 'togglePenggunaAktif'])->name('pengguna.toggle-active');
        Route::post('/pengguna/{pengguna}/reset-password', [AdminController::class, 'resetPasswordPengguna'])->name('pengguna.reset-password');
        Route::delete('/pengguna/{pengguna}', [AdminController::class, 'destroyPengguna'])->name('pengguna.destroy');
        Route::get('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminCreate'])->name('pengguna.formulir.create');
        Route::post('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminStore'])->name('pengguna.formulir.store');
    });
});
