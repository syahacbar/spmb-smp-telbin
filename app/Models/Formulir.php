<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formulir extends Model
{
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
        'program_keahlian_1',
        'program_keahlian_2',
        'surat_keterangan_lulus',
        'kartu_keluarga',
        'foto_selfie',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'submitted_at' => 'datetime',
        'alamat_ortu_sama_dengan_siswa' => 'boolean',
    ];

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}
