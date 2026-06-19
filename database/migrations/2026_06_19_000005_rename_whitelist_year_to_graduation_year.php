<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasColumn('tb_calon_siswa', 'tahun_pendaftaran')
            && ! Schema::hasColumn('tb_calon_siswa', 'tahun_lulus')
        ) {
            Schema::table('tb_calon_siswa', function (Blueprint $table): void {
                $table->renameColumn('tahun_pendaftaran', 'tahun_lulus');
            });
        }

        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            $table->index(['tahun_lulus', 'is_active'], 'tb_calon_siswa_tahun_lulus_active_index');
        });
    }

    public function down(): void
    {
        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            $table->dropIndex('tb_calon_siswa_tahun_lulus_active_index');
        });

        if (
            Schema::hasColumn('tb_calon_siswa', 'tahun_lulus')
            && ! Schema::hasColumn('tb_calon_siswa', 'tahun_pendaftaran')
        ) {
            Schema::table('tb_calon_siswa', function (Blueprint $table): void {
                $table->renameColumn('tahun_lulus', 'tahun_pendaftaran');
            });
        }
    }
};
