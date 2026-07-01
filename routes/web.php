<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSekolahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomisiliSchoolController;
use App\Http\Controllers\FormulirBerkasController;
use App\Http\Controllers\FormulirController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PenggunaWhatsappController;
use App\Http\Controllers\PrestasiSchoolController;
use App\Http\Controllers\RegistrasiAkunBerkasController;
use App\Http\Controllers\RegistrasiAkunController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'))->name('landing');
Route::get('/juknis-spmb/download', function () {
    return response()->download(
        public_path('juknis_spmb_tahun_2026_teluk_bintuni.pdf'),
        'juknis-spmb-smp-teluk-bintuni-2026.pdf',
        ['Content-Type' => 'application/pdf']
    );
})->name('juknis.download');
Route::post('/cek-status', [LandingController::class, 'checkStatus'])
    ->middleware('throttle:spmb-status')
    ->name('status.check');
Route::get('/cek-status', fn () => redirect('/#cek-status'));
Route::get('/akun/status', [RegistrasiAkunController::class, 'show'])->name('akun.status');
Route::post('/akun/status/lanjut', [RegistrasiAkunController::class, 'continueToDashboard'])->name('akun.status.continue');
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
    Route::put('/akun/password', [AuthController::class, 'updatePassword'])->name('akun.password.update');

    Route::get('/formulir', [FormulirController::class, 'create'])->name('formulir.create');
    Route::post('/formulir', [FormulirController::class, 'store'])->name('formulir.store');
    Route::get('/riwayat', [FormulirController::class, 'riwayat'])->name('formulir.riwayat');
    Route::get('/formulir/{formulir}/edit', [FormulirController::class, 'edit'])->name('formulir.edit');
    Route::put('/formulir/{formulir}', [FormulirController::class, 'update'])->name('formulir.update');
    Route::get('/formulir/{formulir}/periksa', [FormulirController::class, 'periksa'])->name('formulir.periksa');
    Route::get('/formulir/{formulir}/berkas/{field}', [FormulirBerkasController::class, 'show'])->name('formulir.berkas.show');
    Route::get('/formulir/{formulir}/tanda-tangan', [FormulirBerkasController::class, 'signature'])->name('formulir.signature.show');
    Route::get('/formulir/pilihan-domisili/{pengguna}', DomisiliSchoolController::class)->name('formulir.domisili-schools');
    Route::get('/formulir/pilihan-prestasi/{pengguna}', PrestasiSchoolController::class)->name('formulir.prestasi-schools');
    Route::get('/registrasi-akun/{registrasi}/kartu-keluarga', RegistrasiAkunBerkasController::class)->name('registrasi.kk');
    Route::post('/formulir/{formulir}/kirim', [FormulirController::class, 'kirim'])->name('formulir.kirim');
    Route::get('/formulir/{formulir}/cetak', [FormulirController::class, 'cetak'])->name('formulir.cetak');

    Route::middleware('spmb.admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/pendaftar', [AdminController::class, 'pendaftar'])->name('pendaftar');
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('pengguna');
        Route::get('/verifikasi-akun/{registrasi}', [AdminController::class, 'verifikasiAkun'])->name('verifikasi-akun.show');
        Route::put('/verifikasi-akun/{registrasi}/alamat', [AdminController::class, 'updateRegistrasiAlamat'])->name('verifikasi-akun.alamat');
        Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
        Route::get('/reset-akun', [AdminController::class, 'resetAkun'])->name('reset-akun');
        Route::post('/reset-akun/reset', [AdminController::class, 'resetAkunPassword'])->name('reset-akun.proses');
        Route::get('/sekolah-zonasi', [AdminController::class, 'sekolahZonasi'])->name('sekolah-zonasi');
        Route::post('/sekolah-zonasi/sekolah', [AdminController::class, 'storeSekolah'])->name('sekolah.store');
        Route::put('/sekolah-zonasi/sekolah/{sekolah}', [AdminController::class, 'updateSekolah'])->name('sekolah.update');
        Route::delete('/sekolah-zonasi/sekolah/{sekolah}', [AdminController::class, 'destroySekolah'])->name('sekolah.destroy');
        Route::post('/sekolah-zonasi/sekolah/{sekolah}/toggle-active', [AdminController::class, 'toggleSekolahAktif'])->name('sekolah.toggle-active');
        Route::post('/sekolah-zonasi/sekolah/{sekolah}/zonasi', [AdminController::class, 'syncZonasiSekolah'])->name('sekolah.zonasi');
        Route::post('/sekolah-zonasi/import', [AdminController::class, 'importSekolahZonasi'])->name('sekolah-zonasi.import');
        Route::get('/pengaturan/tanda-tangan', [AdminController::class, 'showSignature'])->name('pengaturan.signature.show');
        Route::post('/pengaturan/identitas', [AdminController::class, 'updateIdentitas'])->name('pengaturan.identitas');
        Route::post('/pengaturan/jam-pelayanan', [AdminController::class, 'updateJamPelayanan'])->name('pengaturan.jam-pelayanan');
        Route::post('/pengaturan/whitelist-calon-siswa/import', [AdminController::class, 'importCalonSiswa'])->name('pengaturan.whitelist.import');
        Route::get('/pengaturan/whitelist-calon-siswa/download-format', [AdminController::class, 'downloadWhitelistFormat'])->name('pengaturan.whitelist.download-format');
        Route::post('/pengaturan/whitelist-calon-siswa', [AdminController::class, 'storeCalonSiswa'])->name('pengaturan.whitelist.store');
        Route::post('/pengaturan/whitelist-calon-siswa/nonaktifkan', [AdminController::class, 'deactivateCalonSiswaWhitelist'])->name('pengaturan.whitelist.deactivate');
        Route::post('/pengaturan/whitelist-calon-siswa/{calonSiswa}/toggle', [AdminController::class, 'toggleCalonSiswaWhitelist'])->name('pengaturan.whitelist.toggle');
        Route::post('/pengaturan/kontak-panitia', [AdminController::class, 'storeKontakPanitia'])->name('pengaturan.kontak.store');
        Route::put('/pengaturan/kontak-panitia/{kontak}', [AdminController::class, 'updateKontakPanitia'])->name('pengaturan.kontak.update');
        Route::post('/pengaturan/kontak-panitia/{kontak}/utama', [AdminController::class, 'setKontakPanitiaUtama'])->name('pengaturan.kontak.primary');
        Route::delete('/pengaturan/kontak-panitia/{kontak}', [AdminController::class, 'destroyKontakPanitia'])->name('pengaturan.kontak.destroy');
        Route::post('/pengaturan/akses-sekolah', [AdminController::class, 'updateAksesSekolah'])->name('pengaturan.akses-sekolah');
        Route::post('/pengguna/{pengguna}/verifikasi', [AdminController::class, 'verifikasiPengguna'])->name('pengguna.verifikasi');
        Route::post('/pengguna/{pengguna}/status-verifikasi', [AdminController::class, 'updateStatusVerifikasiPengguna'])->name('pengguna.status-verifikasi');
        Route::get('/registrasi-akun/{registrasi}/kartu-keluarga', RegistrasiAkunBerkasController::class)->name('registrasi.kk');
        Route::get('/pengguna/{pengguna}/notifikasi-whatsapp', PenggunaWhatsappController::class)->name('pengguna.notifikasi-whatsapp');
        Route::post('/pengguna/{pengguna}/toggle-active', [AdminController::class, 'togglePenggunaAktif'])->name('pengguna.toggle-active');
        Route::delete('/pengguna/{pengguna}', [AdminController::class, 'destroyPengguna'])->name('pengguna.destroy');
        Route::get('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminCreate'])->name('pengguna.formulir.create');
        Route::post('/pengguna/{pengguna}/formulir', [FormulirController::class, 'adminStore'])->name('pengguna.formulir.store');
    });

    // Admin Sekolah – only for users with role admin_sekolah
    Route::middleware('spmb.auth')->prefix('sekolah-admin')->name('sekolah.admin.')->group(function (): void {
        Route::get('/profil', [AdminSekolahController::class, 'profil'])->name('profil');
        Route::put('/profil', [AdminSekolahController::class, 'updateProfil'])->name('profil.update');
        Route::delete('/profil/foto', [AdminSekolahController::class, 'destroyFoto'])->name('profil.foto.destroy');
        Route::get('/kuota', [AdminSekolahController::class, 'kuota'])->name('kuota');
        Route::put('/kuota', [AdminSekolahController::class, 'updateKuota'])->name('kuota.update');
        Route::get('/pendaftar', [AdminSekolahController::class, 'pendaftar'])->name('pendaftar');
        Route::get('/pendaftar/ekspor', [AdminSekolahController::class, 'eksporPendaftar'])->name('pendaftar.ekspor');
        Route::get('/pendaftar/pdf', [AdminSekolahController::class, 'pdfPendaftar'])->name('pendaftar.pdf');
        Route::put('/pendaftar/{formulir}/terima', [AdminSekolahController::class, 'terimaPendaftar'])->name('pendaftar.terima');
        Route::put('/pendaftar/{formulir}/tolak', [AdminSekolahController::class, 'tolakPendaftar'])->name('pendaftar.tolak');
        Route::put('/pendaftar/{formulir}/reset', [AdminSekolahController::class, 'resetPendaftar'])->name('pendaftar.reset');
    });
});
