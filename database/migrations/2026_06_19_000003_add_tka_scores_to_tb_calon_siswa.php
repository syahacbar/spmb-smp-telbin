<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            $table->decimal('nilai_tka_matematika', 5, 2)->nullable()->after('asal_sekolah');
            $table->decimal('nilai_tka_bahasa_indonesia', 5, 2)->nullable()->after('nilai_tka_matematika');
        });
    }

    public function down(): void
    {
        Schema::table('tb_calon_siswa', function (Blueprint $table): void {
            $table->dropColumn(['nilai_tka_matematika', 'nilai_tka_bahasa_indonesia']);
        });
    }
};
