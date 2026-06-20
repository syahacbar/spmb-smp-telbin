<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JalurPendaftaran extends Model
{
    protected $table = 'tb_jalur_pendaftaran';

    protected $fillable = ['kode', 'nama', 'deskripsi', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
