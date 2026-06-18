<x-layouts.app :pengguna="$pengguna" title="Dasbor">
    @php
        $formatTanggalWaktuIndonesia = function ($date): string {
            if (! $date) {
                return '-';
            }

            $bulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];
            $tanggal = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);

            return $tanggal->format('d').' '.$bulan[(int) $tanggal->format('n')].' '.$tanggal->format('Y').' pukul '.$tanggal->format('H.i').' WIT';
        };
    @endphp

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Dasbor</h3>
            <div class="text-muted">Ringkasan aktivitas SPMB SMP Kabupaten Teluk Bintuni</div>
        </div>
    </div>

    <div class="row g-3">
        @if($pengguna->isAdminDinas())
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card admin-stat-card stat-final">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Pendaftar Final</div>
                                <div class="display-6 fw-bold">{{ $totalFormulir }}</div>
                            </div>
                            <div class="stat-icon"><span class="stat-icon-shape stat-icon-final" aria-hidden="true"></span></div>
                        </div>
                        <a href="{{ route('admin.pendaftar') }}" class="small fw-bold text-decoration-none">Lihat data</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card admin-stat-card stat-draft">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Draft Formulir</div>
                                <div class="display-6 fw-bold">{{ $totalDraft }}</div>
                            </div>
                            <div class="stat-icon"><span class="stat-icon-shape stat-icon-draft" aria-hidden="true"></span></div>
                        </div>
                        <div class="small text-muted">Belum dikirim final</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card admin-stat-card stat-waiting">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Akun Menunggu</div>
                                <div class="display-6 fw-bold">{{ $totalMenungguVerifikasi }}</div>
                            </div>
                            <div class="stat-icon"><span class="stat-icon-shape stat-icon-waiting" aria-hidden="true"></span></div>
                        </div>
                        <a href="{{ route('admin.pengguna') }}" class="small fw-bold text-decoration-none">Verifikasi akun</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card shadow-sm stat-card admin-stat-card stat-student">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <div class="muted-label">Total Siswa</div>
                                <div class="display-6 fw-bold">{{ $totalPengguna }}</div>
                            </div>
                            <div class="stat-icon"><span class="stat-icon-shape stat-icon-student" aria-hidden="true"></span></div>
                        </div>
                        <div class="small text-muted">{{ $totalTerverifikasi }} akun terverifikasi</div>
                    </div>
                </div>
            </div>

        @else
            <div class="col-12">
                <div class="card shadow-sm welcome-card">
                    <div class="card-body p-4">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Selamat Datang</div>
                        <h4 class="fw-bold mb-1">{{ $pengguna->nama_pengguna ?: $pengguna->id_pengguna }}</h4>
                        <div class="text-muted">di Portal SPMB SMP Kabupaten Teluk Bintuni. Silakan pantau status dan lanjutkan pendaftaran setelah akun diverifikasi.</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">Status Formulir</h5>
                                <div class="text-muted">Pantau proses pendaftaran dan lanjutkan dari langkah terakhir.</div>
                            </div>
                            <span class="badge text-bg-success">Akun Terverifikasi</span>
                        </div>
                        @if($formulirSaya)
                            @if($formulirSaya->isSubmitted())
                                <div class="alert alert-success">Formulir Anda sudah dikirim final pada {{ $formatTanggalWaktuIndonesia($formulirSaya->submitted_at) }}.</div>
                                <a href="{{ route('formulir.riwayat') }}" class="btn btn-success">Lihat Riwayat</a>
                                <a href="{{ route('formulir.cetak', $formulirSaya) }}" class="btn btn-outline-success" target="_blank">Cetak Kartu</a>
                            @else
                                <div class="alert alert-warning">Formulir Anda masih draft. Periksa kembali data sebelum dikirim final.</div>
                                <a href="{{ route('formulir.periksa', $formulirSaya) }}" class="btn btn-primary">Periksa dan Kirim</a>
                                <a href="{{ route('formulir.edit', $formulirSaya) }}" class="btn btn-outline-secondary">Edit Data</a>
                            @endif
                        @else
                            <div class="alert alert-info">Anda belum mengisi formulir registrasi.</div>
                            <a href="{{ route('formulir.create') }}" class="btn btn-primary">Isi Formulir</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Juknis Pendaftaran</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Alur SPMB:</h6>
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">Login menggunakan akun yang telah diverifikasi oleh panitia.</li>
                                <li class="mb-2">Isi biodata calon murid dan data orang tua/wali secara lengkap.</li>
                                <li class="mb-2">Unggah seluruh dokumen persyaratan sesuai ketentuan.</li>
                                <li class="mb-2">Periksa kembali data pendaftaran sebelum dikirim final.</li>
                                <li>Cetak kartu pendaftaran setelah formulir berhasil dikirim final.</li>
                            </ol>
                        </div>

                        <div>
                            <h6 class="fw-bold mb-3">Persiapan File Dokumen yang Telah Dipindai (Scan):</h6>
                            <ol class="mb-0 ps-3">
                                <li class="mb-2">
                                    <strong>Ijazah SD/Sederajat atau Surat Keterangan Lulus.</strong>
                                    Format file <strong>*.pdf</strong> berukuran maksimal <strong>1 MB</strong> serta tulisan dapat dilihat/dibaca dengan jelas.
                                </li>
                                <li class="mb-2">
                                    <strong>Kartu Keluarga.</strong>
                                    Format file <strong>*.pdf</strong> berukuran maksimal <strong>1 MB</strong> serta tulisan dapat dilihat/dibaca dengan jelas.
                                </li>
                                <li>
                                    <strong>Pas Foto.</strong>
                                    Ukuran/dimensi <strong>3x4</strong>, wajah terlihat jelas, dan menggunakan pakaian yang rapi.
                                    Format file <strong>*.jpg</strong>, <strong>*.jpeg</strong>, atau <strong>*.png</strong> berukuran maksimal <strong>1 MB</strong>.
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
