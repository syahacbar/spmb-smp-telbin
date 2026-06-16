<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ref_kecamatan')) {
            Schema::create('ref_kecamatan', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 100)->unique();
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ref_kelurahan')) {
            Schema::create('ref_kelurahan', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('kecamatan_id')->constrained('ref_kecamatan')->cascadeOnDelete();
                $table->string('nama', 100);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();

                $table->unique(['kecamatan_id', 'nama']);
            });
        }

        if (! Schema::hasTable('ref_sekolah_asal')) {
            Schema::create('ref_sekolah_asal', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 150)->unique();
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_sekolah_asal');
        Schema::dropIfExists('ref_kelurahan');
        Schema::dropIfExists('ref_kecamatan');
    }
};
