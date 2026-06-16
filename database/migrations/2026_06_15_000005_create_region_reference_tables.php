<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ref_wilayah_provinsi')) {
            Schema::create('ref_wilayah_provinsi', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 100)->unique();
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ref_wilayah_kabupaten')) {
            Schema::create('ref_wilayah_kabupaten', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('provinsi_id')->constrained('ref_wilayah_provinsi')->cascadeOnDelete();
                $table->string('nama', 100);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();

                $table->unique(['provinsi_id', 'nama']);
            });
        }

        if (! Schema::hasTable('ref_wilayah_kecamatan')) {
            Schema::create('ref_wilayah_kecamatan', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('kabupaten_id')->constrained('ref_wilayah_kabupaten')->cascadeOnDelete();
                $table->string('nama', 100);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();

                $table->unique(['kabupaten_id', 'nama']);
            });
        }

        if (! Schema::hasTable('ref_wilayah_kelurahan')) {
            Schema::create('ref_wilayah_kelurahan', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('kecamatan_id')->constrained('ref_wilayah_kecamatan')->cascadeOnDelete();
                $table->string('nama', 100);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();

                $table->unique(['kecamatan_id', 'nama']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_wilayah_kelurahan');
        Schema::dropIfExists('ref_wilayah_kecamatan');
        Schema::dropIfExists('ref_wilayah_kabupaten');
        Schema::dropIfExists('ref_wilayah_provinsi');
    }
};
