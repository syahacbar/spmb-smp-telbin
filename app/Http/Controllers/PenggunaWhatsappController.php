<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;

class PenggunaWhatsappController extends Controller
{
    public function __invoke(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }

        if (! $pengguna->is_verified || ! $pengguna->is_active) {
            return back()->with('warning', 'Notifikasi WhatsApp hanya dapat dikirim untuk akun yang sudah terverifikasi dan aktif.');
        }

        $phone = $this->normalizePhone((string) $pengguna->telpon);

        if (! preg_match('/^62[0-9]{8,13}$/', $phone)) {
            return back()->with('warning', 'Nomor WhatsApp calon siswa tidak valid atau belum tersedia.');
        }

        $pengguna->loadMissing('calonSiswa');

        $nama = trim((string) ($pengguna->calonSiswa?->nama ?: $pengguna->nama_pengguna));

        if ($nama === '') {
            $nama = 'Calon Siswa';
        }

        $message = implode("\n", [
            "Halo, {$nama}",
            '',
            "Akun SPMB Anda dengan NISN {$pengguna->id_pengguna} telah aktif.",
            'Silahkan login di spmb.smkn1bintuni.sch.id/login untuk mengisi biodata dan melengkapi berkas persyaratan.',
            '',
            'Panitia SPMB SMK Negeri 1 Bintuni',
        ]);

        return redirect()->away('https://wa.me/'.$phone.'?text='.rawurlencode($message));
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
