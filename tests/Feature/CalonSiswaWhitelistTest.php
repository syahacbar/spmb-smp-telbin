<?php

namespace Tests\Feature;

use App\Models\CalonSiswa;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CalonSiswaWhitelistTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_lulusan_lama_yang_diaktifkan_dapat_melewati_pengecekan_nisn(): void
    {
        CalonSiswa::create($this->studentData(
            nisn: '0012345678',
            tahunLulus: '2024',
            active: true,
        ));

        $this->postJson('/daftar/cek-nisn', ['nisn' => '0012345678'])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('student.nisn', '0012345678');
    }

    public function test_siswa_lulusan_lama_yang_nonaktif_tidak_dapat_mendaftar(): void
    {
        CalonSiswa::create($this->studentData(
            nisn: '0012345678',
            tahunLulus: '2024',
            active: false,
        ));

        $this->postJson('/daftar/cek-nisn', ['nisn' => '0012345678'])
            ->assertNotFound()
            ->assertJsonPath('ok', false);
    }

    public function test_import_cohort_baru_mempertahankan_data_lama_dalam_status_nonaktif(): void
    {
        CalonSiswa::create($this->studentData(
            nisn: '0012345678',
            tahunLulus: '2025',
            active: true,
        ));

        $admin = Pengguna::create([
            'id_pengguna' => 'admin',
            'nama_pengguna' => 'Admin Dinas',
            'alamat' => null,
            'telpon' => '',
            'email' => null,
            'username' => 'admin',
            'password' => 'secret',
            'level' => 'Administrator',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $admin->roles()->attach(DB::table('roles')->where('kode', 'admin_dinas')->value('id'));

        $csv = implode("\n", [
            'NISN,Nama Siswa,Tempat Lahir,Tanggal Lahir,Asal Sekolah,Nilai Matematika,Nilai Bahasa Indonesia',
            '0098765432,Siswa Baru,Bintuni,2013-01-02,SD Negeri 1,85,90',
        ]);

        $this->withSession(['pengguna_id' => $admin->id_pengguna])
            ->post(route('admin.pengaturan.whitelist.import'), [
                'tahun_lulus' => '2026',
                'calon_siswa_file' => UploadedFile::fake()->createWithContent('whitelist.csv', $csv),
                'deactivate_missing_in_year' => '1',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tb_calon_siswa', [
            'nisn' => '0012345678',
            'tahun_lulus' => '2025',
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('tb_calon_siswa', [
            'nisn' => '0098765432',
            'tahun_lulus' => '2026',
            'is_active' => true,
        ]);
    }

    private function studentData(string $nisn, string $tahunLulus, bool $active): array
    {
        return [
            'nisn' => $nisn,
            'nama' => 'Calon Siswa',
            'tempat_lahir' => 'Bintuni',
            'tanggal_lahir' => '2013-01-01',
            'asal_sekolah' => 'SD Negeri',
            'nilai_tka_matematika' => 80,
            'nilai_tka_bahasa_indonesia' => 85,
            'tahun_lulus' => $tahunLulus,
            'is_active' => $active,
        ];
    }
}
