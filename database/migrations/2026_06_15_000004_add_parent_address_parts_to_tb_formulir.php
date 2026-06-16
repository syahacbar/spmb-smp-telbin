<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_formulir', 'alamat_ortu_kabupaten')) {
                $table->string('alamat_ortu_kabupaten', 100)->nullable()->after('alamat_ortu');
            }

            if (! Schema::hasColumn('tb_formulir', 'alamat_ortu_kecamatan')) {
                $table->string('alamat_ortu_kecamatan', 100)->nullable()->after('alamat_ortu_kabupaten');
            }

            if (! Schema::hasColumn('tb_formulir', 'alamat_ortu_kelurahan')) {
                $table->string('alamat_ortu_kelurahan', 100)->nullable()->after('alamat_ortu_kecamatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            foreach (['alamat_ortu_kelurahan', 'alamat_ortu_kecamatan', 'alamat_ortu_kabupaten'] as $column) {
                if (Schema::hasColumn('tb_formulir', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
