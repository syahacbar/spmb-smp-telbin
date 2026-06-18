<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_formulir', function (Blueprint $table): void {
            $columns = array_values(array_filter(
                ['program_keahlian_1', 'program_keahlian_2'],
                fn (string $column): bool => Schema::hasColumn('tb_formulir', $column),
            ));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::dropIfExists('tb_program_keahlian');
    }

    public function down(): void
    {
        if (! Schema::hasTable('tb_program_keahlian')) {
            Schema::create('tb_program_keahlian', function (Blueprint $table): void {
                $table->id();
                $table->string('nama', 100)->unique();
                $table->string('singkatan', 20)->nullable();
                $table->unsignedInteger('kuota')->default(0);
                $table->json('aliases')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        Schema::table('tb_formulir', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_formulir', 'program_keahlian_1')) {
                $table->string('program_keahlian_1', 100)->nullable();
            }
            if (! Schema::hasColumn('tb_formulir', 'program_keahlian_2')) {
                $table->string('program_keahlian_2', 100)->nullable();
            }
        });
    }
};
