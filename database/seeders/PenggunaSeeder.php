<?php

namespace Database\Seeders;

use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::updateOrCreate(
            ['id_pengguna' => '0812111122'],
            [
                'nama_pengguna' => 'Administrator',
                'alamat' => 'Jakarta',
                'telpon' => '081211112231',
                'email' => 'jasakoding.id@gmail.com',
                'username' => 'admin',
                'password' => Hash::make('123'),
                'level' => 'Administrator',
                'is_verified' => true,
                'verified_at' => now(),
            ],
        );

        Pengguna::updateOrCreate(
            ['id_pengguna' => '0000000001'],
            [
                'nama_pengguna' => 'Developer SPMB',
                'alamat' => 'Lingkungan pengembangan aplikasi',
                'telpon' => '6281111110001',
                'email' => 'developer@spmb.test',
                'username' => 'developer',
                'password' => Hash::make('developer123'),
                'level' => 'Administrator',
                'is_verified' => true,
                'verified_at' => now(),
            ],
        );

        Pengguna::updateOrCreate(
            ['id_pengguna' => '0000000002'],
            [
                'nama_pengguna' => 'Petugas SPMB',
                'alamat' => 'SMK Negeri 1 Bintuni',
                'telpon' => '6281111110002',
                'email' => 'petugas@spmb.test',
                'username' => 'petugas',
                'password' => Hash::make('admin123'),
                'level' => 'Administrator',
                'is_verified' => true,
                'verified_at' => now(),
            ],
        );

        $siswa = [
            [
                'id_pengguna' => '0098765432',
                'nama_pengguna' => 'Budi Santoso',
                'alamat' => 'Jl. Merdeka, Bintuni',
                'telpon' => '6282211110001',
                'email' => 'budi.santoso@spmb.test',
                'is_verified' => false,
                'verified_at' => null,
            ],
            [
                'id_pengguna' => '0098765433',
                'nama_pengguna' => 'Citra Lestari',
                'alamat' => 'Jl. Pendidikan, Bintuni',
                'telpon' => '6282211110002',
                'email' => 'citra.lestari@spmb.test',
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'id_pengguna' => '0098765434',
                'nama_pengguna' => 'Dimas Pratama',
                'alamat' => 'Jl. Raya Bintuni',
                'telpon' => '6282211110003',
                'email' => 'dimas.pratama@spmb.test',
                'is_verified' => true,
                'verified_at' => now(),
            ],
        ];

        foreach ($siswa as $data) {
            Pengguna::updateOrCreate(
                ['id_pengguna' => $data['id_pengguna']],
                [
                    'nama_pengguna' => $data['nama_pengguna'],
                    'alamat' => $data['alamat'],
                    'telpon' => $data['telpon'],
                    'email' => $data['email'],
                    'username' => $data['id_pengguna'],
                    'password' => Hash::make('siswa123'),
                    'level' => 'User',
                    'is_verified' => $data['is_verified'],
                    'verified_at' => $data['verified_at'],
                ],
            );
        }

        Formulir::updateOrCreate(
            ['nisn' => '0098765433'],
            [
                'nama' => 'Citra Lestari',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2010-04-12',
                'nik' => '9206015204100001',
                'jenis_kelamin' => 'Perempuan',
                'agama' => 'Kristen Protestan',
                'hp' => '6282211110002',
                'asal_sekolah' => 'SMP Negeri 2 Bintuni',
                'alamat' => 'Jl. Pendidikan, Bintuni',
                'nama_ayah' => 'Yohanis Lestari',
                'pekerjaan_ayah' => 'Pegawai Swasta',
                'nama_ibu' => 'Martha Lestari',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'hp_ortu' => '6282211110102',
                'alamat_ortu' => 'Jl. Pendidikan, Bintuni',
                'alamat_ortu_sama_dengan_siswa' => true,
                'alamat_ortu_provinsi' => 'Papua Barat',
                'alamat_ortu_kabupaten' => 'Teluk Bintuni',
                'alamat_ortu_kecamatan' => 'Bintuni',
                'alamat_ortu_kelurahan' => 'Bintuni Timur',
                'program_keahlian_1' => 'Desain Komunikasi Visual',
                'program_keahlian_2' => 'Akuntansi dan Keuangan Lembaga',
                'surat_keterangan_lulus' => 'images/laki.jpg',
                'kartu_keluarga' => 'images/kop.jpg',
                'foto_selfie' => 'images/jilbab.jpg',
                'status' => 'draft',
                'submitted_at' => null,
            ],
        );

        Formulir::updateOrCreate(
            ['nisn' => '0098765434'],
            [
                'nama' => 'Dimas Pratama',
                'tempat_lahir' => 'Bintuni',
                'tanggal_lahir' => '2009-09-21',
                'nik' => '9206012109090002',
                'jenis_kelamin' => 'Laki-laki',
                'agama' => 'Islam',
                'hp' => '6282211110003',
                'asal_sekolah' => 'SMP Negeri 1 Bintuni',
                'alamat' => 'Jl. Raya Bintuni',
                'nama_ayah' => 'Agus Pratama',
                'pekerjaan_ayah' => 'Wiraswasta',
                'nama_ibu' => 'Siti Aminah',
                'pekerjaan_ibu' => 'Pedagang',
                'hp_ortu' => '6282211110103',
                'alamat_ortu' => 'Jl. Raya Bintuni',
                'alamat_ortu_sama_dengan_siswa' => true,
                'alamat_ortu_provinsi' => 'Papua Barat',
                'alamat_ortu_kabupaten' => 'Teluk Bintuni',
                'alamat_ortu_kecamatan' => 'Bintuni',
                'alamat_ortu_kelurahan' => 'Bintuni Barat',
                'program_keahlian_1' => 'Teknik Kendaraan Ringan',
                'program_keahlian_2' => 'Teknik Jaringan dan Telekomunikasi',
                'surat_keterangan_lulus' => 'images/laki.jpg',
                'kartu_keluarga' => 'images/kop.jpg',
                'foto_selfie' => 'images/laki.jpg',
                'status' => 'submitted',
                'submitted_at' => now(),
            ],
        );
    }
}
