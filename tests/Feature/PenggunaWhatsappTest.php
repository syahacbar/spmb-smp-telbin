<?php

namespace Tests\Feature;

use App\Http\Controllers\PenggunaWhatsappController;
use App\Models\CalonSiswa;
use App\Models\Pengguna;
use App\Models\RegistrasiAkun;
use Illuminate\Http\Request;
use Tests\TestCase;

class PenggunaWhatsappTest extends TestCase
{
    public function test_admin_dapat_membuka_pesan_whatsapp_untuk_akun_aktif(): void
    {
        $calonSiswa = new CalonSiswa([
            'nisn' => '1234567890',
            'nama' => 'Budi Santoso',
            'tempat_lahir' => 'Bintuni',
            'tanggal_lahir' => '2010-01-01',
            'asal_sekolah' => 'SD Negeri 1 Bintuni',
            'tahun_pendaftaran' => '2026',
            'is_active' => true,
        ]);

        $siswa = $this->buatPengguna([
            'id_pengguna' => '1234567890',
            'nama_pengguna' => '',
            'telpon' => '081234567890',
            'username' => '1234567890',
        ]);
        $siswa->setRelation('calonSiswa', $calonSiswa);
        $siswa->setRelation('registrasiAkun', new RegistrasiAkun(['status' => 'terverifikasi']));

        $message = implode("\n", [
            'Halo, Budi Santoso',
            '',
            'Akun SPMB Anda dengan NISN 1234567890 telah diverifikasi dan aktif.',
            'Silakan login ke portal SPMB SMP Kabupaten Teluk Bintuni untuk melanjutkan pendaftaran.',
            '',
            'Dinas Pendidikan Kabupaten Teluk Bintuni',
        ]);

        $response = app(PenggunaWhatsappController::class)($siswa);

        $this->assertSame(
            'https://wa.me/6281234567890?text='.rawurlencode($message),
            $response->getTargetUrl(),
        );
    }

    public function test_admin_dapat_membuka_pesan_whatsapp_perlu_perbaikan(): void
    {
        $siswa = $this->buatPengguna([
            'id_pengguna' => '1234567890',
            'telpon' => '6281234567890',
            'username' => '1234567890',
            'is_verified' => false,
            'verified_at' => null,
        ]);
        $siswa->setRelation('registrasiAkun', new RegistrasiAkun([
            'status' => 'perlu_perbaikan',
            'catatan_verifikasi' => 'Alamat pada KK belum sesuai.',
        ]));
        $siswa->setRelation('calonSiswa', new CalonSiswa([
            'nisn' => '1234567890',
            'nama' => 'Calon Siswa',
        ]));

        $request = Request::create('/admin/pengguna/1234567890/notifikasi-whatsapp', 'GET');
        $request->headers->set('referer', 'http://localhost/admin/pengguna');
        $request->setLaravelSession(app('session')->driver());
        app()->instance('request', $request);

        $response = app(PenggunaWhatsappController::class)($siswa);

        $this->assertStringStartsWith('https://wa.me/6281234567890?text=', $response->getTargetUrl());
        $this->assertStringContainsString(rawurlencode('perlu diperbaiki'), $response->getTargetUrl());
        $this->assertStringContainsString(rawurlencode('Alamat pada KK belum sesuai.'), $response->getTargetUrl());
    }

    private function buatPengguna(array $attributes): Pengguna
    {
        return new Pengguna(array_merge([
            'id_pengguna' => '1234567890',
            'nama_pengguna' => 'Calon Siswa',
            'alamat' => null,
            'telpon' => '6281234567890',
            'email' => null,
            'username' => '1234567890',
            'password' => 'password',
            'level' => 'User',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ], $attributes));
    }
}
