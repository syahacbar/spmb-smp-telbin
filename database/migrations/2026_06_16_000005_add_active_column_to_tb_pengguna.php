<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            if (! Schema::hasColumn('tb_pengguna', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_pengguna', function (Blueprint $table): void {
            if (Schema::hasColumn('tb_pengguna', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
