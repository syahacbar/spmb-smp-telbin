<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RegistrasiAkun extends Model
{
    protected $table = 'tb_registrasi_akun';

    protected $fillable = [
        'nisn', 'periode_id', 'kabupaten', 'kecamatan_id', 'kelurahan_id',
        'detail_alamat', 'kartu_keluarga_path', 'status', 'catatan_verifikasi',
        'submitted_at', 'verified_at', 'rejected_at', 'verified_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'nisn', 'id_pengguna');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeSpmb::class, 'periode_id');
    }

    public function kartuKeluargaTersedia(): bool
    {
        return $this->kartu_keluarga_path
            && Storage::disk('local')->exists($this->kartu_keluarga_path);
    }

    public function kartuKeluargaIsImage(): bool
    {
        return in_array(strtolower(pathinfo((string) $this->kartu_keluarga_path, PATHINFO_EXTENSION)), [
            'jpg', 'jpeg', 'png', 'webp',
        ], true);
    }
}
