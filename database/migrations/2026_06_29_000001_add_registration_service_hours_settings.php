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
    }

    public function down(): void
    {
        DB::table('tb_pengaturan_spmb')
            ->whereIn('key', array_keys($this->settings()))
            ->delete();
    }

    private function settings(): array
    {
        return [
            'jam_pelayanan_aktif' => '0',
            'jam_pelayanan_mulai' => '08:00',
            'jam_pelayanan_selesai' => '14:00',
            'jam_pelayanan_hari' => '1,2,3,4,5,6,7',
            'jam_pelayanan_pesan_tutup' => 'Layanan pendaftaran dibuka pukul 08.00 sampai 14.00 WIT.',
        ];
    }
};
