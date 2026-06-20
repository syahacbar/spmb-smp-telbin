<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_active',
        'verified_at',
        'verification_notice_seen_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'verification_notice_seen_at' => 'datetime',
    ];

    public function calonSiswa(): BelongsTo
    {
        return $this->belongsTo(CalonSiswa::class, 'id_pengguna', 'nisn');
    }

    public function formulirTerbaru(): HasOne
    {
        return $this->formulir();
    }

    public function formulir(): HasOne
    {
        return $this->hasOne(Formulir::class, 'nisn', 'id_pengguna');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'pengguna_role', 'pengguna_id', 'role_id');
    }

    public function sekolah(): BelongsToMany
    {
        return $this->belongsToMany(Sekolah::class, 'pengguna_sekolah', 'pengguna_id', 'sekolah_id');
    }

    public function registrasiAkun(): HasOne
    {
        return $this->hasOne(RegistrasiAkun::class, 'nisn', 'id_pengguna');
    }

    public function hasRole(string $role): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('kode', $role);
        }

        if ($this->exists) {
            return $this->roles()->where('kode', $role)->exists();
        }

        return match ($role) {
            'admin_dinas' => $this->level === 'Administrator',
            'calon_murid' => $this->level === 'User',
            default => false,
        };
    }

    public function isAdminDinas(): bool
    {
        return $this->hasRole('admin_dinas');
    }

    public function isAdminSekolah(): bool
    {
        return $this->hasRole('admin_sekolah');
    }

    public function isCalonMurid(): bool
    {
        return $this->hasRole('calon_murid');
    }

    public function roleLabel(): string
    {
        if ($this->isAdminDinas()) {
            return 'Admin Dinas';
        }

        if ($this->isAdminSekolah()) {
            return 'Admin Sekolah';
        }

        return 'Calon Murid';
    }
}
