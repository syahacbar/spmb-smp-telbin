<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            $table->timestamp('verification_notice_seen_at')->nullable()->after('verified_at');
        });

        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->foreignId('jalur_id')->nullable()->after('alamat_ortu_kelurahan')
                ->constrained('tb_jalur_pendaftaran')->restrictOnDelete();
            $table->foreignId('sekolah_id')->nullable()->after('jalur_id')
                ->constrained('tb_sekolah')->restrictOnDelete();
            $table->string('dokumen_pendukung', 255)->nullable()->after('foto_selfie');
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sekolah_id');
            $table->dropConstrainedForeignId('jalur_id');
            $table->dropColumn('dokumen_pendukung');
        });

        Schema::table('tb_pengguna', function (Blueprint $table): void {
            $table->dropColumn('verification_notice_seen_at');
        });
    }
};
