<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalonSiswa extends Model
{
    protected $table = 'tb_calon_siswa';

    protected $primaryKey = 'nisn';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'asal_sekolah',
        'nilai_tka_matematika',
        'nilai_tka_bahasa_indonesia',
        'tahun_pendaftaran',
        'is_active',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'nilai_tka_matematika' => 'decimal:2',
        'nilai_tka_bahasa_indonesia' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActiveForYear($query, string $year)
    {
        return $query->where('tahun_pendaftaran', $year)->where('is_active', true);
    }
}
