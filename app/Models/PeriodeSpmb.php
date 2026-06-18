<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeSpmb extends Model
{
    protected $table = 'tb_periode_spmb';

    protected $fillable = [
        'nama', 'tahun_pendaftaran', 'tahun_pelajaran',
        'mulai_registrasi', 'selesai_registrasi', 'is_active',
    ];

    protected $casts = [
        'mulai_registrasi' => 'date',
        'selesai_registrasi' => 'date',
        'is_active' => 'boolean',
    ];
}
