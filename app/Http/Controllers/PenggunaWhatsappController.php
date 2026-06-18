<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;

class PenggunaWhatsappController extends Controller
{
    public function __invoke(Pengguna $pengguna): RedirectResponse
    {
        if (! $pengguna->isCalonMurid()) {
            abort(403);
        }

        $phone = $this->normalizePhone((string) $pengguna->telpon);

        if (! preg_match('/^62[0-9]{8,13}$/', $phone)) {
            return back()->with('warning', 'Nomor WhatsApp calon siswa tidak valid atau belum tersedia.');
        }

        $pengguna->loadMissing(['calonSiswa', 'registrasiAkun']);

        $nama = trim((string) ($pengguna->calonSiswa?->nama ?: $pengguna->nama_pengguna));

        if ($nama === '') {
            $nama = 'Calon Siswa';
        }

        $status = $pengguna->registrasiAkun?->status;
        $statusMessage = match ($status) {
            'terverifikasi' => [
                "Akun SPMB Anda dengan NISN {$pengguna->id_pengguna} telah diverifikasi dan aktif.",
                'Silakan login ke portal SPMB SMP Kabupaten Teluk Bintuni untuk melanjutkan pendaftaran.',
            ],
            'perlu_perbaikan' => [
                "Registrasi akun SPMB dengan NISN {$pengguna->id_pengguna} perlu diperbaiki.",
                'Catatan: '.($pengguna->registrasiAkun?->catatan_verifikasi ?: '-'),
                'Silakan login untuk memperbaiki alamat atau Kartu Keluarga.',
            ],
            'ditolak' => [
                "Registrasi akun SPMB dengan NISN {$pengguna->id_pengguna} belum dapat disetujui.",
                'Catatan: '.($pengguna->registrasiAkun?->catatan_verifikasi ?: '-'),
                'Silakan menghubungi Admin Dinas jika membutuhkan penjelasan.',
            ],
            default => [
                "Registrasi akun SPMB dengan NISN {$pengguna->id_pengguna} sedang menunggu verifikasi Dinas Pendidikan.",
            ],
        };

        $message = implode("\n", [
            "Halo, {$nama}",
            '',
            ...$statusMessage,
            '',
            'Dinas Pendidikan Kabupaten Teluk Bintuni',
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
