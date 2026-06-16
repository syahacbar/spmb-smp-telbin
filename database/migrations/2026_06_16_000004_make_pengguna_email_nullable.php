<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tb_pengguna') || ! Schema::hasColumn('tb_pengguna', 'email')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tb_pengguna MODIFY email varchar(100) NULL');
        }
    }

    public function down(): void
    {
        // Dibiarkan nullable agar akun yang sudah dibuat tanpa email tidak rusak saat rollback.
    }
};
