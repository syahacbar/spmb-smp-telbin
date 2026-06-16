<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_formulir', 'alamat_ortu_sama_dengan_siswa')) {
                $table->boolean('alamat_ortu_sama_dengan_siswa')->default(false)->after('alamat_ortu');
            }

            if (! Schema::hasColumn('tb_formulir', 'alamat_ortu_provinsi')) {
                $table->string('alamat_ortu_provinsi', 100)->nullable()->after('alamat_ortu_sama_dengan_siswa');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            foreach (['alamat_ortu_provinsi', 'alamat_ortu_sama_dengan_siswa'] as $column) {
                if (Schema::hasColumn('tb_formulir', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
