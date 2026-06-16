<x-layouts.app :pengguna="$pengguna" title="Data Registrasi">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data Registrasi</h3>
            <div class="text-muted">Formulir yang sudah dikirim final oleh siswa.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Asal Sekolah</th>
                        <th>Minat A</th>
                        <th>Minat B</th>
                        <th>Dokumen</th>
                        <th>Tanggal Kirim</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($formulirs as $formulir)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $formulir->nisn }}</td>
                            <td>{{ $formulir->nama }}</td>
                            <td>{{ $formulir->asal_sekolah }}</td>
                            <td>{{ $formulir->program_keahlian_1 }}</td>
                            <td>{{ $formulir->program_keahlian_2 }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    @foreach(['surat_keterangan_lulus', 'kartu_keluarga', 'foto_selfie'] as $field)
                                        <a href="{{ asset($formulir->{$field}) }}" target="_blank">
                                            <img src="{{ asset($formulir->{$field}) }}" class="doc-thumb" alt="{{ $field }}">
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                            <td>{{ $formulir->submitted_at?->format('d/m/Y H:i') ?: $formulir->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-success btn-sm">Edit</a>
                                <a href="{{ route('formulir.cetak', $formulir) }}" class="btn btn-outline-success btn-sm" target="_blank">Cetak</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data registrasi.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
