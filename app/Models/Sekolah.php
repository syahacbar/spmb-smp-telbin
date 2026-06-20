<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sekolah extends Model
{
    protected $table = 'tb_sekolah';

    protected $fillable = [
        'npsn', 'nama', 'status', 'kecamatan_id', 'kelurahan_id',
        'alamat', 'telepon', 'email', 'latitude', 'longitude', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_sekolah', 'sekolah_id', 'pengguna_id');
    }
}
