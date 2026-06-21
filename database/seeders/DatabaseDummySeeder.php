<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Run the FormReferenceSeeder
        $this->call(FormReferenceSeeder::class);

        // 2. Fetch or create active period
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id')
            ?? DB::table('tb_periode_spmb')->insertGetId([
                'nama' => 'SPMB SMP Kabupaten Teluk Bintuni 2026/2027',
                'tahun_pendaftaran' => '2026',
                'tahun_pelajaran' => '2026/2027',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        // 3. Fetch kecamatan and kelurahan IDs
        $bintuniId = DB::table('ref_kecamatan')->where('nama', 'Bintuni')->value('id');
        $bintuniTimurId = DB::table('ref_kelurahan')->where('kecamatan_id', $bintuniId)->where('nama', 'Bintuni Timur')->value('id');
        $bintuniBaratId = DB::table('ref_kelurahan')->where('kecamatan_id', $bintuniId)->where('nama', 'Bintuni Barat')->value('id');

        // 4. Insert schools
        $sekolah1 = DB::table('tb_sekolah')->insertGetId([
            'npsn' => '10101001',
            'nama' => 'SMP Negeri 1 Bintuni',
            'status' => 'negeri',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniTimurId,
            'alamat' => 'Jl. Pendidikan No. 1, Bintuni Timur',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sekolah2 = DB::table('tb_sekolah')->insertGetId([
            'npsn' => '10101002',
            'nama' => 'SMP Negeri 2 Bintuni',
            'status' => 'negeri',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniBaratId,
            'alamat' => 'Jl. Bintuni Raya Km 4, Bintuni Barat',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sekolah3 = DB::table('tb_sekolah')->insertGetId([
            'npsn' => '10101003',
            'nama' => 'SMP Swasta Santo Yosep Bintuni',
            'status' => 'swasta',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniTimurId,
            'alamat' => 'Jl. Santo Yosep, Bintuni Timur',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Insert zonasi mapping
        DB::table('tb_zonasi_sekolah')->insert([
            [
                'periode_id' => $periodeId,
                'sekolah_id' => $sekolah1,
                'kelurahan_id' => $bintuniTimurId,
                'prioritas' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'periode_id' => $periodeId,
                'sekolah_id' => $sekolah3,
                'kelurahan_id' => $bintuniTimurId,
                'prioritas' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'periode_id' => $periodeId,
                'sekolah_id' => $sekolah2,
                'kelurahan_id' => $bintuniBaratId,
                'prioritas' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 6. Insert school pathway kuotas
        $jalurDomisili = DB::table('tb_jalur_pendaftaran')->where('kode', 'domisili')->value('id');
        $jalurPrestasi = DB::table('tb_jalur_pendaftaran')->where('kode', 'prestasi')->value('id');
        $jalurAfirmasi = DB::table('tb_jalur_pendaftaran')->where('kode', 'afirmasi')->value('id');
        $jalurMutasi = DB::table('tb_jalur_pendaftaran')->where('kode', 'mutasi')->value('id');

        $schools = [$sekolah1, $sekolah2, $sekolah3];
        $jalurs = [$jalurDomisili, $jalurPrestasi, $jalurAfirmasi, $jalurMutasi];

        foreach ($schools as $sch) {
            foreach ($jalurs as $jl) {
                if ($jl) {
                    DB::table('tb_kuota_sekolah_jalur')->insert([
                        'periode_id' => $periodeId,
                        'sekolah_id' => $sch,
                        'jalur_id' => $jl,
                        'kuota' => rand(20, 50),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 7. Insert whitelist students
        DB::table('tb_calon_siswa')->insert([
            [
                'nisn' => '1111111111',
                'nama' => 'Budi Santoso',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2013-05-12',
                'asal_sekolah' => 'SD Negeri 1 Bintuni',
                'nilai_tka_matematika' => null,
                'nilai_tka_bahasa_indonesia' => null,
                'tahun_lulus' => '2026',
                'is_active' => true,
            ],
            [
                'nisn' => '2222222222',
                'nama' => 'Siti Aminah',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2013-09-22',
                'asal_sekolah' => 'SD Negeri 2 Bintuni',
                'nilai_tka_matematika' => 85.50,
                'nilai_tka_bahasa_indonesia' => 90.00,
                'tahun_lulus' => '2026',
                'is_active' => true,
            ],
            [
                'nisn' => '3333333333',
                'nama' => 'Gideon Wenda',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2013-11-05',
                'asal_sekolah' => 'SD Inpres Bintuni',
                'nilai_tka_matematika' => null,
                'nilai_tka_bahasa_indonesia' => null,
                'tahun_lulus' => '2026',
                'is_active' => true,
            ],
            [
                'nisn' => '4444444444',
                'nama' => 'Rudi Hartono',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2013-02-14',
                'asal_sekolah' => 'SD Negeri 1 Bintuni',
                'nilai_tka_matematika' => 70.00,
                'nilai_tka_bahasa_indonesia' => 75.00,
                'tahun_lulus' => '2026',
                'is_active' => true,
            ],
            [
                'nisn' => '5555555555',
                'nama' => 'Dewi Sartika',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2013-08-08',
                'asal_sekolah' => 'SD Negeri 2 Bintuni',
                'nilai_tka_matematika' => 80.00,
                'nilai_tka_bahasa_indonesia' => 80.00,
                'tahun_lulus' => '2026',
                'is_active' => true,
            ],
        ]);

        // 8. Insert Admin Dinas user
        $adminId = 'admin';
        DB::table('tb_pengguna')->insert([
            'id_pengguna' => $adminId,
            'nama_pengguna' => 'Admin Dinas Kabupaten',
            'alamat' => 'Kantor Dinas Pendidikan, Bintuni',
            'telpon' => '6281111110001',
            'email' => 'admin@dinas-bintuni.go.id',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'level' => 'Administrator',
            'is_active' => true,
        ]);

        $roleAdminDinas = DB::table('roles')->where('kode', 'admin_dinas')->value('id');
        if ($roleAdminDinas) {
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $adminId,
                'role_id'     => $roleAdminDinas,
            ]);
        }

        // 9a. Insert Admin Sekolah user for SMP Negeri 1 Bintuni
        $adminSekolahId = 'SCH00000001';
        DB::table('tb_pengguna')->insert([
            'id_pengguna'   => $adminSekolahId,
            'nama_pengguna' => 'Admin SMP Negeri 1 Bintuni',
            'alamat'        => 'Jl. Pendidikan No. 1, Bintuni Timur',
            'telpon'        => '6281111110002',
            'email'         => 'admin@smpn1bintuni.sch.id',
            'username'      => 'adminsekolah',
            'password'      => Hash::make('password'),
            'level'         => 'Administrator',
            'is_verified'   => true,
            'is_active'     => true,
        ]);

        $roleAdminSekolah = DB::table('roles')->where('kode', 'admin_sekolah')->value('id');
        if ($roleAdminSekolah) {
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $adminSekolahId,
                'role_id'     => $roleAdminSekolah,
            ]);
        }

        // Link admin sekolah to sekolah1
        DB::table('pengguna_sekolah')->insert([
            'pengguna_id' => $adminSekolahId,
            'sekolah_id'  => $sekolah1,
        ]);

        // 9. Seed Registered Test Accounts
        $roleCalonMurid = DB::table('roles')->where('kode', 'calon_murid')->value('id');

        // Account: Siti Aminah (terverifikasi)
        $sitiId = '2222222222';
        DB::table('tb_pengguna')->insert([
            'id_pengguna' => $sitiId,
            'nama_pengguna' => 'Siti Aminah',
            'alamat' => 'Jl. Bintuni Raya Km 2, Bintuni Timur',
            'telpon' => '6281234567891',
            'email' => null,
            'username' => $sitiId,
            'password' => Hash::make('password'),
            'level' => 'User',
            'is_verified' => true,
            'verified_at' => now()->subDay(),
            'is_active' => true,
        ]);

        if ($roleCalonMurid) {
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $sitiId,
                'role_id' => $roleCalonMurid,
            ]);
        }

        DB::table('tb_registrasi_akun')->insert([
            'nisn' => $sitiId,
            'periode_id' => $periodeId,
            'kabupaten' => 'Teluk Bintuni',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniTimurId,
            'detail_alamat' => 'Jl. Bintuni Raya Km 2, Bintuni Timur',
            'kartu_keluarga_path' => 'registrasi/kk/dummy_kk_siti.pdf',
            'status' => 'terverifikasi',
            'submitted_at' => now()->subDays(2),
            'verified_at' => now()->subDay(),
            'verified_by' => $adminId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::disk('local')->put('registrasi/kk/dummy_kk_siti.pdf', 'dummy content siti');

        // Account: Rudi Hartono (menunggu_verifikasi)
        $rudiId = '4444444444';
        DB::table('tb_pengguna')->insert([
            'id_pengguna' => $rudiId,
            'nama_pengguna' => 'Rudi Hartono',
            'alamat' => 'Jl. Pendidikan No. 5, Bintuni Timur',
            'telpon' => '6281234567892',
            'email' => null,
            'username' => $rudiId,
            'password' => Hash::make('password'),
            'level' => 'User',
            'is_verified' => false,
            'is_active' => true,
        ]);

        if ($roleCalonMurid) {
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $rudiId,
                'role_id' => $roleCalonMurid,
            ]);
        }

        DB::table('tb_registrasi_akun')->insert([
            'nisn' => $rudiId,
            'periode_id' => $periodeId,
            'kabupaten' => 'Teluk Bintuni',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniTimurId,
            'detail_alamat' => 'Jl. Pendidikan No. 5, Bintuni Timur',
            'kartu_keluarga_path' => 'registrasi/kk/dummy_kk_rudi.pdf',
            'status' => 'menunggu_verifikasi',
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::disk('local')->put('registrasi/kk/dummy_kk_rudi.pdf', 'dummy content rudi');

        // Account: Dewi Sartika (perlu_perbaikan)
        $dewiId = '5555555555';
        DB::table('tb_pengguna')->insert([
            'id_pengguna' => $dewiId,
            'nama_pengguna' => 'Dewi Sartika',
            'alamat' => 'Jl. Bintuni Raya Km 4, Bintuni Barat',
            'telpon' => '6281234567893',
            'email' => null,
            'username' => $dewiId,
            'password' => Hash::make('password'),
            'level' => 'User',
            'is_verified' => false,
            'is_active' => true,
        ]);

        if ($roleCalonMurid) {
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $dewiId,
                'role_id' => $roleCalonMurid,
            ]);
        }

        DB::table('tb_registrasi_akun')->insert([
            'nisn' => $dewiId,
            'periode_id' => $periodeId,
            'kabupaten' => 'Teluk Bintuni',
            'kecamatan_id' => $bintuniId,
            'kelurahan_id' => $bintuniBaratId,
            'detail_alamat' => 'Jl. Bintuni Raya Km 4, Bintuni Barat',
            'kartu_keluarga_path' => 'registrasi/kk/dummy_kk_dewi.pdf',
            'status' => 'perlu_perbaikan',
            'catatan_verifikasi' => 'Unggah KK yang lebih jelas dan terbaca.',
            'submitted_at' => now()->subDays(3),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Storage::disk('local')->put('registrasi/kk/dummy_kk_dewi.pdf', 'dummy content dewi');

        // 10. Insert submitted formulir for test students into sekolah1
        $jalurPrestasiId = DB::table('tb_jalur_pendaftaran')->where('kode', 'prestasi')->value('id');
        $jalurDomisiliId = DB::table('tb_jalur_pendaftaran')->where('kode', 'domisili')->value('id');

        DB::table('tb_formulir')->insert([
            [
                'nisn'               => $sitiId,
                'nama'               => 'Siti Aminah',
                'tempat_lahir'       => 'Bintuni',
                'tanggal_lahir'      => '2013-09-22',
                'nik'                => '9100000000001',
                'jenis_kelamin'      => 'Perempuan',
                'agama'              => 'Islam',
                'hp'                 => '6281234567891',
                'asal_sekolah'       => 'SD Negeri 2 Bintuni',
                'alamat'             => 'Jl. Bintuni Raya Km 2, Bintuni Timur',
                'alamat_kabupaten'   => 'Teluk Bintuni',
                'alamat_kecamatan'   => 'Bintuni',
                'alamat_kelurahan'   => 'Bintuni Timur',
                'nama_ayah'          => 'Ayah Siti',
                'pekerjaan_ayah'     => 'Wiraswasta',
                'nama_ibu'           => 'Ibu Siti',
                'pekerjaan_ibu'      => 'Ibu Rumah Tangga',
                'hp_ortu'            => '6281234567891',
                'alamat_ortu'        => '',
                'alamat_ortu_sama_dengan_siswa' => true,
                'jalur_id'           => $jalurPrestasiId,
                'sekolah_id'         => $sekolah1,
                'surat_keterangan_lulus' => 'dokumen/dummy_skl.pdf',
                'kartu_keluarga'     => 'dokumen/dummy_kk.pdf',
                'foto_selfie'        => 'dokumen/dummy_foto.jpg',
                'status'             => 'submitted',
                'submitted_at'       => now()->subDays(1),
                'created_at'         => now()->subDays(2),
            ],
            [
                'nisn'               => $rudiId,
                'nama'               => 'Rudi Hartono',
                'tempat_lahir'       => 'Bintuni',
                'tanggal_lahir'      => '2013-02-14',
                'nik'                => '9100000000002',
                'jenis_kelamin'      => 'Laki-laki',
                'agama'              => 'Kristen',
                'hp'                 => '6281234567892',
                'asal_sekolah'       => 'SD Negeri 1 Bintuni',
                'alamat'             => 'Jl. Pendidikan No. 5, Bintuni Timur',
                'alamat_kabupaten'   => 'Teluk Bintuni',
                'alamat_kecamatan'   => 'Bintuni',
                'alamat_kelurahan'   => 'Bintuni Timur',
                'nama_ayah'          => 'Ayah Rudi',
                'pekerjaan_ayah'     => 'Pegawai Negeri',
                'nama_ibu'           => 'Ibu Rudi',
                'pekerjaan_ibu'      => 'Ibu Rumah Tangga',
                'hp_ortu'            => '6281234567892',
                'alamat_ortu'        => '',
                'alamat_ortu_sama_dengan_siswa' => true,
                'jalur_id'           => $jalurDomisiliId,
                'sekolah_id'         => $sekolah1,
                'surat_keterangan_lulus' => 'dokumen/dummy_skl.pdf',
                'kartu_keluarga'     => 'dokumen/dummy_kk.pdf',
                'foto_selfie'        => 'dokumen/dummy_foto.jpg',
                'status'             => 'submitted',
                'submitted_at'       => now()->subDays(3),
                'created_at'         => now()->subDays(4),
            ],
        ]);
    }
}
