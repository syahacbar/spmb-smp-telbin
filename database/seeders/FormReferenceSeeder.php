<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = [
            'Bintuni' => [
                'Bintuni Timur',
                'Bintuni Barat',
                'Tuasai',
                'Argosigemerai',
                'Beimes',
                'Iguriji',
                'Masina',
                'Wesiri',
            ],

            'Merdey' => [
                'Merdey',
                'Meryeb',
                'Meyom',
                'Menggerba',
                'Anajero',
                'Mogromus',
                'Morombuy',
                'Mekiesefeb',
                'Meyetga',
            ],

            'Babo' => [
                'Irarutu III',
                'Amutu',
                'Nusei',
                'Kasira',
            ],

            'Aranday' => [
                'Aranday',
                'Kampung Baru',
                'Kecap',
                'Manunggal Karya',
            ],

            'Moskona Selatan' => [
                'Jagiro',
                'Meyenda',
                'Rawara',
                'Barma Barat',
                'Inggof',
            ],

            'Moskona Utara' => [
                'Moyeba',
                'Merestim',
                'Inofina',
                'Mosum',
            ],

            'Wamesa' => [
                'Wamesa I/Idoor',
                'Wamesa II/Yakati',
                'Yansei',
                'Mamuranu',
            ],

            'Fafurwar' => [
                'Fruata/Irowutu II',
                'Meryedi',
                'Riendo',
            ],

            'Tembuni' => [
                'Tembuni',
                'Mogoi Baru',
                'Araisum',
                'Bangun Mulya',
            ],

            'Kuri' => [
                'Sarbe',
                'Wagura',
                'Refideso',
                'Naramasa',
                'Obo',
            ],

            'Manimeri' => [
                'Bumi Saniari',
                'Banjar Ausoy',
                'Waraitama',
                'Atibo Manimeri',
                'Pasamai',
                'Korano Jaya',
            ],

            'Tuhiba' => [
                'Tuhiba',
                'Tisaida',
                'Kucir',
                'Sibena Raya',
                'Sibena Permai',
            ],

            'Dataran Beimes' => [
                'Horna',
                'Cumnaji',
                'Menci',
                'Sir',
                'Huss',
                'Ugdohop',
            ],

            'Sumuri' => [
                'Tofoi',
                'Tanah Merah',
                'Saengga',
                'Forada',
                'Materabu Jaya',
            ],

            'Kaitaro' => [
                'Sara',
                'Warga Nusa I',
                'Warga Nusa II',
                'Tugarama',
                'Suga',
            ],

            'Aroba' => [
                'Aroba',
                'Yaru',
                'Sido Makmur',
                'Wimbro',
                'Sangguar',
            ],

            'Masyeta' => [
                'Masyeta',
                'Mestofu',
                'Kalibiru',
                'Mesomda',
            ],

            'Biscoop' => [
                'Jahabra',
                'Ibori',
                'Menyembru',
                'Meyorga',
                'Laudoho',
                'Eniba',
                'Mowitka',
            ],

            'Tomu' => [
                'Sebyar Rejosasi',
                'Tomu',
                'Taroy',
                'Ekam',
            ],

            'Kamundan' => [
                'Kalitami I',
                'Kalitami II',
                'Kenara',
                'Bibiram',
            ],

            'Weriagar' => [
                'Weriagar',
                'Mogotira',
                'Weriagar Baru',
                'Weriagar Utara',
                'Tuanaikin',
            ],

            'Moskona Barat' => [
                'Meyerga',
                'Macok',
                'Istiwkem',
                'Majnic',
            ],

            'Meyado' => [
                'Meyado',
                'Barma',
                'Barma Baru',
                'Vasco Damneen',
            ],

            'Moskona Timur' => [
                'Igomu',
                'Mesna',
                'Sumuy',
            ],
        ];

        $order = 1;

        foreach ($kecamatan as $namaKecamatan => $kelurahanList) {
            DB::table('ref_kecamatan')->updateOrInsert(
                ['nama' => $namaKecamatan],
                ['urutan' => $order++, 'created_at' => now(), 'updated_at' => now()],
            );

            $kecamatanId = DB::table('ref_kecamatan')->where('nama', $namaKecamatan)->value('id');
            $kelurahanOrder = 1;

            foreach ($kelurahanList as $namaKelurahan) {
                DB::table('ref_kelurahan')->updateOrInsert(
                    ['kecamatan_id' => $kecamatanId, 'nama' => $namaKelurahan],
                    ['urutan' => $kelurahanOrder++, 'created_at' => now(), 'updated_at' => now()],
                );
            }
        }

        foreach ([
            'SMP ADVENT TELUK BINTUNI',
            'SMP HARMONI SCHOOL TERPADU BINTUNI',
            'SMP MUHAMMADIYAH',
            'SMP YPK BINTUNI',
            'SMP YPPK SANTA MONIKA',
            'SMP NEGERI I BINTUNI',
            'SMP NEGERI TERPADU',
            'SMP NEGERI 2 BINTUNI',
            'SMP PERINTIS MANIMERI II',
            'SMP PERINTIS KELAPA DUA',
            'SMP STELLA MARIS TOFOI',
            'SMP TAMAN BANGSA TOFOI',
            'SMP YPK TANAH MERAH',
            'SMP YPK BETHEL IDOOR',
            'SMP NEGERI 1 ARANDAY',
            'SMP NEGERI 2 ARANDAY',
            'SMP NEGERI BABO',
            'SMP NEGERI MERDEY',
            'SMP NEGERI KURI SARBE',
            'SMP NEGERI FRUATA FAFURWAR',
            'SMP SATU ATAP KALITAMI',
            'SMP SATU ATAP MEYERGA',
            'SMP SATU ATAP WERIAGAR',
            'SMP SATAP MUYEBA',
            'SMPN WIMRO',
            'SMPN SATAP HORNA',
            'SMP NEGERI SATU ATAP JAGIRO',
            'SMP PERINTIS STENGKOL I',
            'SMP PERINTIS STENGKOL III',
            'SMPN SIBENA',

        ] as $index => $namaSekolah) {
            DB::table('ref_sekolah_asal')->updateOrInsert(
                ['nama' => $namaSekolah],
                ['urutan' => $index + 1, 'created_at' => now(), 'updated_at' => now()],
            );
        }

        $wilayah = [
            'Papua Barat' => [
                'Teluk Bintuni' => [
                    'Bintuni' => ['Bintuni Timur', 'Bintuni Barat'],
                    'Manimeri' => ['Bumi Saniari', 'Banjar Ausoy'],
                ],
                'Manokwari' => [
                    'Manokwari Barat' => ['Padarni', 'Wosi'],
                ],
            ],
            'Papua' => [
                'Jayapura' => [
                    'Heram' => ['Waena', 'Yabansai'],
                ],
            ],
        ];

        $provinsiOrder = 1;

        foreach ($wilayah as $namaProvinsi => $kabupatenList) {
            DB::table('ref_wilayah_provinsi')->updateOrInsert(
                ['nama' => $namaProvinsi],
                ['urutan' => $provinsiOrder++, 'created_at' => now(), 'updated_at' => now()],
            );

            $provinsiId = DB::table('ref_wilayah_provinsi')->where('nama', $namaProvinsi)->value('id');
            $kabupatenOrder = 1;

            foreach ($kabupatenList as $namaKabupaten => $kecamatanList) {
                DB::table('ref_wilayah_kabupaten')->updateOrInsert(
                    ['provinsi_id' => $provinsiId, 'nama' => $namaKabupaten],
                    ['urutan' => $kabupatenOrder++, 'created_at' => now(), 'updated_at' => now()],
                );

                $kabupatenId = DB::table('ref_wilayah_kabupaten')
                    ->where('provinsi_id', $provinsiId)
                    ->where('nama', $namaKabupaten)
                    ->value('id');
                $kecamatanOrder = 1;

                foreach ($kecamatanList as $namaKecamatan => $kelurahanList) {
                    DB::table('ref_wilayah_kecamatan')->updateOrInsert(
                        ['kabupaten_id' => $kabupatenId, 'nama' => $namaKecamatan],
                        ['urutan' => $kecamatanOrder++, 'created_at' => now(), 'updated_at' => now()],
                    );

                    $kecamatanId = DB::table('ref_wilayah_kecamatan')
                        ->where('kabupaten_id', $kabupatenId)
                        ->where('nama', $namaKecamatan)
                        ->value('id');
                    $kelurahanOrder = 1;

                    foreach ($kelurahanList as $namaKelurahan) {
                        DB::table('ref_wilayah_kelurahan')->updateOrInsert(
                            ['kecamatan_id' => $kecamatanId, 'nama' => $namaKelurahan],
                            ['urutan' => $kelurahanOrder++, 'created_at' => now(), 'updated_at' => now()],
                        );
                    }
                }
            }
        }
    }
}
