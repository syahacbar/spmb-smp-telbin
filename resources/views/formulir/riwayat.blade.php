<x-layouts.app :pengguna="$pengguna" title="Riwayat Registrasi">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Riwayat Registrasi</h3>
            <div class="text-muted">Lihat status formulir dan cetak kartu pendaftaran setelah final.</div>
        </div>
    </div>

    @forelse($formulirs as $formulir)
        @php
            $nomorPendaftaran = 'SPMB-2026-'.str_pad((string) $formulir->id, 3, '0', STR_PAD_LEFT);
            $alamatSiswa = collect([
                $formulir->alamat,
                $formulir->alamat_kelurahan,
                $formulir->alamat_kecamatan,
                $formulir->alamat_kabupaten,
            ])->filter()->implode(', ');
            $alamatOrtu = collect([
                $formulir->alamat_ortu,
                $formulir->alamat_ortu_kelurahan,
                $formulir->alamat_ortu_kecamatan,
                $formulir->alamat_ortu_kabupaten,
                $formulir->alamat_ortu_provinsi,
            ])->filter()->implode(', ');
        @endphp

        <div class="history-card card shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="history-header">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Nomor Pendaftaran</div>
                        <div class="history-number">{{ $nomorPendaftaran }}</div>
                    </div>
                    <div class="text-md-end">
                        @if($formulir->isSubmitted())
                            <span class="badge text-bg-success">Terkirim Final</span>
                            <div class="small text-muted mt-1">{{ $formulir->submitted_at?->format('d/m/Y H:i') }}</div>
                        @else
                            <span class="badge text-bg-warning">Draft</span>
                            <div class="small text-muted mt-1">Belum dikirim final</div>
                        @endif
                    </div>
                </div>

                <div class="history-layout">
                    <div class="history-main">
                        <section class="history-section">
                            <div class="history-section-title">Data Calon Peserta Didik</div>
                            <div class="history-identity">
                                <div class="history-avatar">{{ strtoupper(mb_substr($formulir->nama, 0, 1)) }}</div>
                                <div>
                                    <div class="history-name">{{ $formulir->nama }}</div>
                                    <div class="text-muted">{{ $formulir->nisn }} · {{ $formulir->asal_sekolah }}</div>
                                </div>
                            </div>
                            <div class="history-grid mt-3">
                                <div>
                                    <span>NIK</span>
                                    <strong>{{ $formulir->nik }}</strong>
                                </div>
                                <div>
                                    <span>TTL</span>
                                    <strong>{{ $formulir->tempat_lahir }}, {{ $formulir->tanggal_lahir?->format('d/m/Y') }}</strong>
                                </div>
                                <div>
                                    <span>Jenis Kelamin</span>
                                    <strong>{{ $formulir->jenis_kelamin }}</strong>
                                </div>
                                <div>
                                    <span>Agama</span>
                                    <strong>{{ $formulir->agama }}</strong>
                                </div>
                                <div class="history-grid-full">
                                    <span>Domisili</span>
                                    <strong>{{ $alamatSiswa }}</strong>
                                </div>
                            </div>
                        </section>

                        <section class="history-section">
                            <div class="history-section-title">Data Orang Tua / Wali</div>
                            <div class="history-grid">
                                <div>
                                    <span>Ayah</span>
                                    <strong>{{ $formulir->nama_ayah }} · {{ $formulir->pekerjaan_ayah }}</strong>
                                </div>
                                <div>
                                    <span>Ibu</span>
                                    <strong>{{ $formulir->nama_ibu }} · {{ $formulir->pekerjaan_ibu }}</strong>
                                </div>
                                <div>
                                    <span>No HP / WA</span>
                                    <strong>{{ $formulir->hp_ortu }}</strong>
                                </div>
                                <div>
                                    <span>Sama dengan Domisili Siswa</span>
                                    <strong>{{ $formulir->alamat_ortu_sama_dengan_siswa ? 'Ya' : 'Tidak' }}</strong>
                                </div>
                                <div class="history-grid-full">
                                    <span>Alamat Orang Tua / Wali</span>
                                    <strong>{{ $alamatOrtu }}</strong>
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="history-side">
                        <section class="history-side-panel">
                            <div class="history-section-title">Berkas</div>
                            <div class="document-list">
                                @foreach([
                                    'surat_keterangan_lulus' => 'Ijazah / SKL',
                                    'kartu_keluarga' => 'Kartu Keluarga',
                                    'foto_selfie' => 'Pas Foto',
                                ] as $field => $label)
                                    @if($formulir->berkasTersedia($field))
                                        <a href="{{ $formulir->berkasUrl($field) }}" data-document-preview data-document-title="{{ $label }}" data-document-type="{{ $formulir->berkasIsImage($field) ? 'image' : 'pdf' }}" data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">{{ $label }}</a>
                                    @else
                                        <span class="text-muted small">{{ $label }} belum tersedia</span>
                                    @endif
                                @endforeach
                            </div>
                        </section>

                        <section class="history-side-panel">
                            <div class="history-section-title">Aktivitas</div>
                            <div class="history-timeline">
                                <div>
                                    <span></span>
                                    <p>Formulir disimpan<br><small>{{ $formulir->created_at?->format('d/m/Y H:i') }}</small></p>
                                </div>
                                <div class="{{ $formulir->isSubmitted() ? '' : 'muted' }}">
                                    <span></span>
                                    <p>Dikirim final<br><small>{{ $formulir->submitted_at?->format('d/m/Y H:i') ?: 'Menunggu' }}</small></p>
                                </div>
                            </div>
                        </section>

                        <div class="d-grid gap-2">
                            @if($formulir->isSubmitted())
                                <a href="{{ route('formulir.cetak', $formulir) }}" class="btn btn-outline-success" target="_blank">Cetak Kartu</a>
                            @else
                                <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-outline-secondary">Edit Data</a>
                                <a href="{{ route('formulir.periksa', $formulir) }}" class="btn btn-primary">Periksa dan Kirim</a>
                            @endif
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state card shadow-sm">
            <div class="card-body text-center p-5">
                <div class="fw-bold mb-1">Belum ada formulir registrasi.</div>
                <div class="text-muted mb-3">Mulai isi formulir untuk melanjutkan proses pendaftaran.</div>
                <a href="{{ route('formulir.create') }}" class="btn btn-primary">Isi Formulir</a>
            </div>
        </div>
    @endforelse
</x-layouts.app>
