<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach ($this->settings() as $key => $value) {
            DB::table('tb_pengaturan_spmb')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => $now, 'created_at' => $now],
            );
        }

        DB::table('tb_pengaturan_spmb')
            ->where('key', 'jam_pelayanan_hari')
            ->delete();
    }

    public function down(): void
    {
        DB::table('tb_pengaturan_spmb')
            ->whereIn('key', array_keys($this->settings()))
            ->delete();

        DB::table('tb_pengaturan_spmb')->updateOrInsert(
            ['key' => 'jam_pelayanan_hari'],
            ['value' => '1,2,3,4,5,6,7', 'updated_at' => now(), 'created_at' => now()],
        );
    }

    private function settings(): array
    {
        return [
            'jam_pelayanan_tanggal_mulai' => '2026-07-01',
            'jam_pelayanan_tanggal_selesai' => '2026-07-06',
        ];
    }
};
