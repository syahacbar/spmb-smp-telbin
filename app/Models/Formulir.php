<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Formulir extends Model
{
    public const DOCUMENT_FIELDS = [
        'surat_keterangan_lulus',
        'kartu_keluarga',
        'foto_selfie',
        'dokumen_pendukung',
    ];

    protected $table = 'tb_formulir';

    public const UPDATED_AT = null;

    protected $fillable = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'jenis_kelamin',
        'agama',
        'hp',
        'asal_sekolah',
        'alamat',
        'alamat_kabupaten',
        'alamat_kecamatan',
        'alamat_kelurahan',
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'hp_ortu',
        'alamat_ortu',
        'alamat_ortu_sama_dengan_siswa',
        'alamat_ortu_provinsi',
        'alamat_ortu_kabupaten',
        'alamat_ortu_kecamatan',
        'alamat_ortu_kelurahan',
        'jalur_id',
        'sekolah_id',
        'surat_keterangan_lulus',
        'kartu_keluarga',
        'foto_selfie',
        'dokumen_pendukung',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'submitted_at' => 'datetime',
        'alamat_ortu_sama_dengan_siswa' => 'boolean',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'nisn', 'id_pengguna');
    }

    public function jalur(): BelongsTo
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_id');
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_id');
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function berkasUrl(string $field): string
    {
        abort_unless(in_array($field, self::DOCUMENT_FIELDS, true), 404);

        return route('formulir.berkas.show', [$this, $field]);
    }

    public function berkasDownloadUrl(string $field): string
    {
        return $this->berkasUrl($field).'?download=1';
    }

    public function berkasIsImage(string $field): bool
    {
        if (! in_array($field, self::DOCUMENT_FIELDS, true)) {
            return false;
        }

        return in_array(strtolower(pathinfo((string) $this->{$field}, PATHINFO_EXTENSION)), [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ], true);
    }

    public function berkasTersedia(string $field): bool
    {
        if (! in_array($field, self::DOCUMENT_FIELDS, true)) {
            return false;
        }

        $path = $this->{$field};

        if (! $path) {
            return false;
        }

        if (str_starts_with($path, 'dokumen/') || str_starts_with($path, 'registrasi/kk/')) {
            return Storage::disk('local')->exists($path);
        }

        return str_starts_with($path, 'uploads/dokumen/') && is_file(public_path($path));
    }
}
