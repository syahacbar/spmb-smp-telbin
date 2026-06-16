<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengguna extends Model
{
    protected $table = 'tb_pengguna';

    protected $primaryKey = 'id_pengguna';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id_pengguna',
        'nama_pengguna',
        'alamat',
        'telpon',
        'email',
        'username',
        'password',
        'level',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'id_pengguna', 'nisn');
    }

    public function formulirTerbaru(): HasOne
    {
        return $this->hasOne(Formulir::class, 'nisn', 'id_pengguna')->latestOfMany('id');
    }
}
