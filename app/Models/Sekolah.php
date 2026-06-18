<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'tb_sekolah';

    protected $fillable = [
        'npsn', 'nama', 'status', 'kecamatan_id', 'kelurahan_id',
        'alamat', 'telepon', 'email', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];
}
