<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormulirBerkasController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PenggunaWhatsappController;
use App\Http\Controllers\RegistrasiAkunBerkasController;
use App\Http\Controllers\RegistrasiAkunController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/cek-status', [LandingController::class, 'checkStatus'])
    ->middleware('throttle:spmb-status')
    ->name('status.check');
Route::get('/cek-status', fn () => redirect('/#cek-status'));
Route::get('/akun/status', [RegistrasiAkunController::class, 'show'])->name('akun.status');
Route::put('/akun/perbaikan', [RegistrasiAkunController::class, 'update'])->name('akun.perbaikan');
Route::post('/akun/logout', [AuthController::class, 'logout'])->name('akun.logout');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])
        ->middleware('throttle:spmb-login')
        ->name('login.store');
    Route::get('/daftar', [AuthController::class, 'register'])->name('register');
    Route::post('/daftar/cek-nisn', [AuthController::class, 'checkRegisterNisn'])
        ->middleware('throttle:spmb-register-check')
        ->name('register.check-nisn');
    Route::post('/daftar', [AuthController::class, 'storeRegistration'])
        ->middleware('throttle:spmb-register')
        ->name('register.store');
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
    Route::get('/formulir/{formulir}/berkas/{field}', [FormulirBerkasController::class, 'show'])->name('formulir.berkas.show');
    Route::get('/formulir/{formulir}/tanda-tangan', [FormulirBerkasController::class, 'signature'])->name('formulir.signature.show');
    Route::post('/formulir/{formulir}/kirim', [FormulirController::class, 'kirim'])->name('formulir.kirim');
    Route::get('/formulir/{formulir}/cetak', [FormulirController::class, 'cetak'])->name('formulir.cetak');

    Route::middleware('spmb.admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/pendaftar', [AdminController::class, 'pendaftar'])->name('pendaftar');
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::get('/pengaturan/tanda-tangan', [AdminController::class, 'showSignature'])->name('pengaturan.signature.show');
        Route::post('/pengaturan/identitas', [AdminController::class, 'updateIdentitas'])->name('pengaturan.identitas');
        Route::post('/pengaturan/whitelist-calon-siswa/import', [AdminController::class, 'importCalonSiswa'])->name('pengaturan.whitelist.import');
        Route::post('/pengaturan/whitelist-calon-siswa/aktifkan', [AdminController::class, 'activateCalonSiswaWhitelist'])->name('pengaturan.whitelist.activate');
        Route::post('/pengaturan/whitelist-calon-siswa/nonaktifkan', [AdminController::class, 'deactivateCalonSiswaWhitelist'])->name('pengaturan.whitelist.deactivate');
        Route::post('/pengaturan/kontak-panitia', [AdminController::class, 'storeKontakPanitia'])->name('pengaturan.kontak.store');
        Route::put('/pengaturan/kontak-panitia/{kontak}', [AdminController::class, 'updateKontakPanitia'])->name('pengaturan.kontak.update');
        Route::post('/pengaturan/kontak-panitia/{kontak}/utama', [AdminController::class, 'setKontakPanitiaUtama'])->name('pengaturan.kontak.primary');
        Route::delete('/pengaturan/kontak-panitia/{kontak}', [AdminController::class, 'destroyKontakPanitia'])->name('pengaturan.kontak.destroy');
        Route::post('/pengguna/{pengguna}/verifikasi', [AdminController::class, 'verifikasiPengguna'])->name('pengguna.verifikasi');
        Route::post('/pengguna/{pengguna}/status-verifikasi', [AdminController::class, 'updateStatusVerifikasiPengguna'])->name('pengguna.status-verifikasi');
        Route::get('/registrasi-akun/{registrasi}/kartu-keluarga', RegistrasiAkunBerkasController::class)->name('registrasi.kk');
        Route::get('/pengguna/{pengguna}/notifikasi-whatsapp', PenggunaWhatsappController::class)->name('pengguna.notifikasi-whatsapp');
        Route::post('/pengguna/{pengguna}/toggle-active', [AdminController::class, 'togglePenggunaAktif'])->name('pengguna.toggle-active');
        Route::delete('/pengguna/{pengguna}', [AdminController::class, 'destroyPengguna'])->name('pengguna.destroy');
        Route::get('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminCreate'])->name('pengguna.formulir.create');
        Route::post('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminStore'])->name('pengguna.formulir.store');
    });
});
