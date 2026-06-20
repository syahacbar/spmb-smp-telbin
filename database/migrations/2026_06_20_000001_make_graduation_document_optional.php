<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->string('surat_keterangan_lulus', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('tb_formulir')->whereNull('surat_keterangan_lulus')->update([
            'surat_keterangan_lulus' => '',
        ]);

        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->string('surat_keterangan_lulus', 255)->nullable(false)->change();
        });
    }
};
