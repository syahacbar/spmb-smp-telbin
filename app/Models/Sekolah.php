<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sekolah extends Model
{
    protected $table = 'tb_sekolah';

    protected $fillable = [
        'npsn', 'nama', 'status', 'kecamatan_id', 'kelurahan_id',
        'alamat', 'telepon', 'email', 'latitude', 'longitude', 'is_active',
        'foto', 'kepala_sekolah', 'deskripsi',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'foto' => 'string',
        'kepala_sekolah' => 'string',
        'deskripsi' => 'string',
    ];

    public function pengguna(): BelongsToMany
    {
        return $this->belongsToMany(Pengguna::class, 'pengguna_sekolah', 'sekolah_id', 'pengguna_id');
    }

    public function formulirs(): HasMany
    {
        return $this->hasMany(Formulir::class, 'sekolah_id');
    }
}
