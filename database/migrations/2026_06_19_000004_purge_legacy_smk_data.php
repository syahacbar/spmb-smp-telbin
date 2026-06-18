<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $demoUserIds = [
            '0812111122',
            '0000000001',
            '0000000002',
            '0098765432',
            '0098765433',
            '0098765434',
        ];

        DB::table('tb_formulir')->whereIn('nisn', $demoUserIds)->delete();
        DB::table('tb_pengguna')->whereIn('id_pengguna', $demoUserIds)->delete();

        DB::table('tb_calon_siswa')
            ->where('is_active', false)
            ->where(function ($query): void {
                $query
                    ->where('asal_sekolah', 'like', 'SMP%')
                    ->orWhere('asal_sekolah', 'like', 'MTS%');
            })
            ->delete();

        Schema::dropIfExists('ref_sekolah_asal');

        DB::table('tb_pengaturan_spmb')
            ->where('key', 'tempat_tes')
            ->where('value', 'like', '%SMK%')
            ->update(['value' => 'Dinas Pendidikan Kabupaten Teluk Bintuni']);

        DB::table('tb_pengaturan_spmb')
            ->where('key', 'catatan_kartu')
            ->update([
                'value' => "Peserta wajib mengikuti tahapan SPMB sesuai jadwal resmi.\nPeserta wajib mencetak dan membawa kartu pendaftaran sebagai bukti keikutsertaan.\nPeserta wajib memantau pengumuman melalui portal SPMB.",
            ]);

        DB::table('tb_pengaturan_spmb')
            ->where('key', 'kepala_ttd_path')
            ->where(function ($query): void {
                $query
                    ->where('value', 'like', 'images/ttdketua%')
                    ->orWhere('value', 'like', 'uploads/pengaturan/%');
            })
            ->update(['value' => null]);
    }

    public function down(): void
    {
        // Data SMK lama sengaja tidak dipulihkan.
    }
};
