<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_sekolah', function (Blueprint $table): void {
            $table->string('foto', 255)->nullable()->after('email');
            $table->string('kepala_sekolah', 150)->nullable()->after('foto');
            $table->text('deskripsi')->nullable()->after('kepala_sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('tb_sekolah', function (Blueprint $table): void {
            $table->dropColumn(['foto', 'kepala_sekolah', 'deskripsi']);
        });
    }
};
