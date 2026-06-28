<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PengaturanSpmb extends Model
{
    protected $table = 'tb_pengaturan_spmb';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    public static function defaults(): array
    {
        return [
            'tahun_pendaftaran' => '2026',
            'tahun_pelajaran' => '2026/2027',
            'kepala_nama' => 'Panitia SPMB',
            'kepala_nip' => '',
            'kepala_jabatan' => 'Panitia SPMB',
            'kepala_ttd_path' => '',
            'tanggal_tes' => '06 Juli 2026',
            'waktu_tes' => '08.00 WIT s.d. selesai',
            'tempat_tes' => 'Dinas Pendidikan Kabupaten Teluk Bintuni',
            'catatan_kartu'       => "Peserta wajib mengikuti tahapan SPMB sesuai jadwal resmi.\nPeserta wajib mencetak dan membawa kartu pendaftaran sebagai bukti keikutsertaan.\nPeserta wajib memantau pengumuman melalui portal SPMB.",
            'tombol_terima_tolak_aktif' => '0',
            'jam_pelayanan_aktif' => '0',
            'jam_pelayanan_mulai' => '08:00',
            'jam_pelayanan_selesai' => '14:00',
            'jam_pelayanan_hari' => '1,2,3,4,5,6,7',
            'jam_pelayanan_pesan_tutup' => 'Layanan pendaftaran dibuka pukul 08.00 sampai 14.00 WIT.',
        ];
    }

    public static function allSettings(): array
    {
        if (! Schema::hasTable('tb_pengaturan_spmb')) {
            return self::defaults();
        }

        return array_replace(
            self::defaults(),
            self::query()->pluck('value', 'key')->all(),
        );
    }

    public static function getValue(string $key, ?string $fallback = null): ?string
    {
        $settings = self::allSettings();

        return $settings[$key] ?? $fallback;
    }

    public static function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings): void {
            foreach ($settings as $key => $value) {
                self::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value],
                );
            }
        });
    }
}
