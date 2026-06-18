<div class="table-responsive">
    <table class="table table-bordered mb-0">
        <tbody>
        @foreach([
            'nisn' => 'NISN',
            'nama' => 'Nama',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'nik' => 'NIK',
            'jenis_kelamin' => 'Jenis Kelamin',
            'agama' => 'Agama',
            'hp' => 'No HP / WA',
            'asal_sekolah' => 'Asal Sekolah',
            'alamat_kabupaten' => 'Kabupaten Domisili',
            'alamat_kecamatan' => 'Kecamatan Domisili',
            'alamat_kelurahan' => 'Kelurahan/Desa Domisili',
            'alamat' => 'Alamat',
            'nama_ayah' => 'Nama Ayah',
            'pekerjaan_ayah' => 'Pekerjaan Ayah',
            'nama_ibu' => 'Nama Ibu',
            'pekerjaan_ibu' => 'Pekerjaan Ibu',
            'hp_ortu' => 'No HP / WA Ortu',
            'alamat_ortu_sama_dengan_siswa' => 'Alamat Orang Tua Sama dengan Domisili Siswa',
            'alamat_ortu_provinsi' => 'Provinsi Orang Tua',
            'alamat_ortu_kabupaten' => 'Kabupaten Orang Tua',
            'alamat_ortu_kecamatan' => 'Kecamatan Orang Tua',
            'alamat_ortu_kelurahan' => 'Kelurahan/Desa Orang Tua',
            'alamat_ortu' => 'Alamat Detail Orang Tua',
        ] as $field => $label)
            <tr>
                <th style="width: 230px">{{ $label }}</th>
                <td>
                    @if($field === 'alamat_ortu_sama_dengan_siswa')
                        {{ $formulir->{$field} ? 'Ya' : 'Tidak' }}
                    @else
                        {{ $formulir->{$field} }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <th>Status</th>
            <td>
                @if($formulir->isSubmitted())
                    Terkirim Final
                @else
                    Draft
                @endif
            </td>
        </tr>
        <tr>
            <th>Tanggal Simpan</th>
            <td>{{ $formulir->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        @if($formulir->submitted_at)
            <tr>
                <th>Tanggal Kirim Final</th>
                <td>{{ $formulir->submitted_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
