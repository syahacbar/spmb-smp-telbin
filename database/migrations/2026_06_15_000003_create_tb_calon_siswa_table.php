<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tb_calon_siswa')) {
            Schema::create('tb_calon_siswa', function (Blueprint $table): void {
                $table->string('nisn', 10)->primary();
                $table->string('nama', 100);
                $table->string('tempat_lahir', 100);
                $table->date('tanggal_lahir');
                $table->string('asal_sekolah', 100);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_calon_siswa');
    }
};
