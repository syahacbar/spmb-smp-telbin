<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateNisn = DB::table('tb_formulir')
            ->select('nisn')
            ->groupBy('nisn')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('nisn');

        if ($duplicateNisn->isNotEmpty()) {
            throw new RuntimeException(
                'Unique constraint tb_formulir.nisn tidak dapat dipasang karena ditemukan NISN ganda: '
                .$duplicateNisn->implode(', '),
            );
        }

        $orphanNisn = DB::table('tb_formulir')
            ->leftJoin('tb_pengguna', 'tb_pengguna.id_pengguna', '=', 'tb_formulir.nisn')
            ->whereNull('tb_pengguna.id_pengguna')
            ->distinct()
            ->pluck('tb_formulir.nisn');

        if ($orphanNisn->isNotEmpty()) {
            throw new RuntimeException(
                'Foreign key tb_formulir.nisn tidak dapat dipasang karena ditemukan formulir tanpa akun: '
                .$orphanNisn->implode(', '),
            );
        }

        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->dropIndex('tb_formulir_nisn_index');
            $table->string('nisn', 11)->change();
            $table->unique('nisn', 'tb_formulir_nisn_unique');
            $table->foreign('nisn', 'tb_formulir_nisn_foreign')
                ->references('id_pengguna')
                ->on('tb_pengguna')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            $table->dropForeign('tb_formulir_nisn_foreign');
            $table->dropUnique('tb_formulir_nisn_unique');
            $table->string('nisn', 50)->change();
            $table->index('nisn', 'tb_formulir_nisn_index');
        });
    }
};
