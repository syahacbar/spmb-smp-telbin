<x-layouts.app :pengguna="$pengguna" title="Periksa Formulir">
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
        $documents = [
            'surat_keterangan_lulus' => ['label' => 'Ijazah / SKL'],
            'kartu_keluarga' => ['label' => 'Kartu Keluarga'],
            'foto_selfie' => ['label' => 'Pas Foto'],
        ];
    @endphp

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Periksa Formulir Pendaftaran</h3>
            <div class="text-muted">Pastikan data dan berkas sudah benar sebelum dikirim final.</div>
        </div>
    </div>

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
                                <div class="text-muted">{{ $formulir->nisn }} &middot; {{ $formulir->asal_sekolah }}</div>
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
                            <div>
                                <span>No HP / WA</span>
                                <strong>{{ $formulir->hp }}</strong>
                            </div>
                            <div>
                                <span>Tanggal Simpan</span>
                                <strong>{{ $formulir->created_at?->format('d/m/Y H:i') }}</strong>
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
                                <strong>{{ $formulir->nama_ayah }} &middot; {{ $formulir->pekerjaan_ayah }}</strong>
                            </div>
                            <div>
                                <span>Ibu</span>
                                <strong>{{ $formulir->nama_ibu }} &middot; {{ $formulir->pekerjaan_ibu }}</strong>
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

                    <section class="history-section">
                        <div class="history-section-title">Berkas Pendaftaran</div>
                        <div class="row g-3">
                            @foreach($documents as $field => $document)
                                @php
                                    $path = $formulir->{$field};
                                    $fileExists = $formulir->berkasTersedia($field);
                                    $fileUrl = $formulir->berkasUrl($field);
                                    $isImage = $fileExists && $formulir->berkasIsImage($field);
                                    $isPdf = $fileExists && ! $isImage;
                                @endphp
                                <div class="col-md-4">
                                    <div class="uploaded-file h-100">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                            <div>
                                                <div class="fw-bold">{{ $document['label'] }}</div>
                                                <div class="small text-muted">{{ $isImage ? 'Gambar' : 'PDF' }}</div>
                                            </div>
                                            <span class="badge text-bg-light">{{ $fileExists ? 'Ada' : 'Kosong' }}</span>
                                        </div>

                                        @if($isImage)
                                            <a href="{{ $fileUrl }}" class="d-block mb-2" data-document-preview data-document-title="{{ $document['label'] }}" data-document-type="image" data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">
                                                <img src="{{ $fileUrl }}" class="img-fluid border rounded" alt="{{ $document['label'] }}">
                                            </a>
                                        @elseif($isPdf)
                                            <div class="ratio ratio-4x3 mb-2">
                                                <iframe src="{{ $fileUrl }}#toolbar=0&navpanes=0&scrollbar=0" class="border rounded bg-white" title="Preview {{ $document['label'] }}"></iframe>
                                            </div>
                                            <div class="document-hint mb-2">Preview halaman pertama berkas PDF.</div>
                                        @else
                                            <div class="document-hint mb-2">Berkas belum tersedia.</div>
                                        @endif

                                        @if($fileExists)
                                            <a href="{{ $fileUrl }}" class="btn btn-outline-primary btn-sm w-100" data-document-preview data-document-title="{{ $document['label'] }}" data-document-type="{{ $isImage ? 'image' : 'pdf' }}" data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">Buka Berkas</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                <aside class="history-side">
                    <section class="history-side-panel">
                        <div class="history-section-title">Status Pemeriksaan</div>
                        @if($formulir->isSubmitted())
                            <div class="alert alert-success mb-0">
                                Formulir sudah dikirim final pada {{ $formulir->submitted_at?->format('d/m/Y H:i') }}.
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                Pastikan seluruh data dan berkas sudah benar sebelum dikirim final.
                            </div>
                        @endif
                    </section>

                    <section class="history-side-panel">
                        <div class="history-section-title">Berkas</div>
                        <div class="document-list">
                            @foreach($documents as $field => $document)
                                @php
                                    $path = $formulir->{$field};
                                    $fileExists = $formulir->berkasTersedia($field);
                                @endphp
                                @if($fileExists)
                                    <a href="{{ $formulir->berkasUrl($field) }}" data-document-preview data-document-title="{{ $document['label'] }}" data-document-type="{{ $formulir->berkasIsImage($field) ? 'image' : 'pdf' }}" data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">{{ $document['label'] }}</a>
                                @else
                                    <span class="text-muted small">{{ $document['label'] }} belum tersedia</span>
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
                            <a href="{{ route('formulir.riwayat') }}" class="btn btn-outline-secondary">Kembali ke Riwayat</a>
                        @else
                            <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-outline-secondary">Edit Data</a>
                            <form method="post" action="{{ route('formulir.kirim', $formulir) }}" class="mb-0">
                                @csrf
                                <button class="btn btn-primary w-100" data-confirm="Kirim formulir final? Data tidak dapat diedit lagi oleh siswa setelah dikirim.">Kirim Final</button>
                            </form>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-layouts.app>
