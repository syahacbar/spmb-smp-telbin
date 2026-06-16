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
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];
}
