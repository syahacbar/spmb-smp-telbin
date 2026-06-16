<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_formulir', 'alamat_kabupaten')) {
                $table->string('alamat_kabupaten', 100)->nullable()->after('alamat');
            }

            if (! Schema::hasColumn('tb_formulir', 'alamat_kecamatan')) {
                $table->string('alamat_kecamatan', 100)->nullable()->after('alamat_kabupaten');
            }

            if (! Schema::hasColumn('tb_formulir', 'alamat_kelurahan')) {
                $table->string('alamat_kelurahan', 100)->nullable()->after('alamat_kecamatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            foreach (['alamat_kelurahan', 'alamat_kecamatan', 'alamat_kabupaten'] as $column) {
                if (Schema::hasColumn('tb_formulir', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
