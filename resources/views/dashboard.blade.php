<x-layouts.app :pengguna="$pengguna" title="Dasbor">
    <style>
        .dinas-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(135deg, #063f35, #0b5d4b 58%, #0788a8);
            color: #fff;
            padding: 1.6rem;
            box-shadow: 0 18px 42px rgba(6, 63, 53, .2);
        }
        .dinas-hero::after {
            content: "";
            position: absolute;
            right: -4rem;
            bottom: -6rem;
            width: 16rem;
            height: 16rem;
            border: 2rem solid rgba(242, 184, 75, .14);
            border-radius: 50%;
        }
        .dinas-hero > * { position: relative; z-index: 1; }
        .dinas-hero .hero-kicker {
            color: #f2b84b;
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .dinas-hero .hero-copy { color: rgba(255,255,255,.78); }
        .dashboard-stat {
            --accent: #0b5d4b;
            --soft: #e4f3ed;
            min-height: 148px;
            border: 1px solid #d8e8e2;
            border-radius: .9rem;
            background: linear-gradient(145deg, var(--soft), #fff 62%);
            box-shadow: 0 12px 28px rgba(16, 55, 47, .06);
        }
        .dashboard-stat.waiting { --accent: #d18b0b; --soft: #fff7df; }
        .dashboard-stat.revision { --accent: #0788a8; --soft: #e9f7fa; }
        .dashboard-stat.rejected { --accent: #b5473d; --soft: #fff0ee; }
        .dashboard-stat.approved { --accent: #0b5d4b; --soft: #e4f3ed; }
        .dashboard-stat-icon {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: .75rem;
            background: var(--accent);
            color: #fff;
            font-weight: 900;
            box-shadow: 0 10px 20px color-mix(in srgb, var(--accent) 20%, transparent);
        }
        .dashboard-stat-value {
            color: var(--accent);
            font-size: 2rem;
            font-weight: 900;
            line-height: 1;
        }
        .queue-card {
            overflow: hidden;
            border: 1px solid #d8e8e2;
            border-radius: 1rem;
            box-shadow: 0 14px 34px rgba(16, 55, 47, .07);
        }
        .queue-card .card-header {
            border-bottom-color: #dcece6;
            padding: 1.1rem 1.25rem;
        }
        .queue-row {
            display: grid;
            grid-template-columns: minmax(190px, 1.2fr) minmax(150px, .9fr) minmax(140px, .8fr) auto;
            gap: 1rem;
            align-items: center;
            border-bottom: 1px solid #edf4f1;
            padding: 1rem 1.25rem;
        }
        .queue-row:last-child { border-bottom: 0; }
        .queue-name { color: #12372f; font-weight: 800; }
        .queue-meta { color: #667085; font-size: .84rem; }
        .student-empty-dashboard {
            display: grid;
            min-height: calc(100vh - 180px);
            place-items: center;
        }
        .student-welcome-card {
            width: min(100%, 720px);
            border: 1px solid #cfe4dc;
            border-radius: 1.2rem;
            background: #fff;
            padding: clamp(1.5rem, 4vw, 3rem);
            text-align: center;
            box-shadow: 0 20px 48px rgba(6, 63, 53, .1);
        }
        .student-welcome-icon {
            display: grid;
            width: 72px;
            height: 72px;
            margin: 0 auto 1.25rem;
            place-items: center;
            border-radius: 1rem;
            background: linear-gradient(135deg, #0b5d4b, #0788a8);
            color: #fff;
            font-size: 1.8rem;
            font-weight: 900;
            box-shadow: 0 14px 30px rgba(11, 93, 75, .22);
        }
        @media (max-width: 991.98px) {
            .queue-row { grid-template-columns: 1fr 1fr; }
            .queue-action { justify-self: end; }
        }
        @media (max-width: 575.98px) {
            .queue-row { grid-template-columns: 1fr; }
            .queue-action { justify-self: start; }
        }
    </style>

    @if($pengguna->isAdminDinas())
        <section class="dinas-hero mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="hero-kicker">Dinas Pendidikan Kabupaten Teluk Bintuni</div>
                    <h2 class="fw-bold mt-2 mb-2">Dashboard Verifikasi Akun SPMB</h2>
                    <div class="hero-copy">Periksa kesesuaian alamat domisili dengan Kartu Keluarga sebelum memberikan akses kepada calon murid.</div>
                </div>
                <a href="{{ route('admin.pengguna', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-warning fw-bold px-4">Buka Antrean Verifikasi</a>
            </div>
        </section>

        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat waiting h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <div class="small text-muted fw-bold">MENUNGGU</div>
                                <div class="dashboard-stat-value mt-1">{{ $totalMenungguVerifikasi }}</div>
                            </div>
                            <div class="dashboard-stat-icon">!</div>
                        </div>
                        <a href="{{ route('admin.pengguna', ['status' => 'menunggu_verifikasi']) }}" class="small fw-bold text-decoration-none">Periksa antrean &rarr;</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat revision h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <div class="small text-muted fw-bold">PERLU PERBAIKAN</div>
                                <div class="dashboard-stat-value mt-1">{{ (int) ($statusCounts['perlu_perbaikan'] ?? 0) }}</div>
                            </div>
                            <div class="dashboard-stat-icon">i</div>
                        </div>
                        <a href="{{ route('admin.pengguna', ['status' => 'perlu_perbaikan']) }}" class="small fw-bold text-decoration-none">Lihat data &rarr;</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat approved h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <div class="small text-muted fw-bold">DISETUJUI</div>
                                <div class="dashboard-stat-value mt-1">{{ (int) ($statusCounts['terverifikasi'] ?? 0) }}</div>
                            </div>
                            <div class="dashboard-stat-icon">&#10003;</div>
                        </div>
                        <a href="{{ route('admin.pengguna', ['status' => 'terverifikasi']) }}" class="small fw-bold text-decoration-none">Lihat akun aktif &rarr;</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card dashboard-stat rejected h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <div class="small text-muted fw-bold">DITOLAK</div>
                                <div class="dashboard-stat-value mt-1">{{ (int) ($statusCounts['ditolak'] ?? 0) }}</div>
                            </div>
                            <div class="dashboard-stat-icon">&times;</div>
                        </div>
                        <a href="{{ route('admin.pengguna', ['status' => 'ditolak']) }}" class="small fw-bold text-decoration-none">Lihat data &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <section class="card queue-card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Antrean Pemeriksaan Terlama</h5>
                    <div class="small text-muted">Prioritaskan registrasi yang lebih dahulu diajukan.</div>
                </div>
                <span class="badge rounded-pill text-bg-warning">{{ $totalMenungguVerifikasi }} menunggu</span>
            </div>
            <div class="card-body p-0">
                @forelse($antreanVerifikasi as $registrasi)
                    <div class="queue-row">
                        <div>
                            <div class="queue-name">{{ $registrasi->pengguna?->calonSiswa?->nama ?? $registrasi->pengguna?->nama_pengguna ?? '-' }}</div>
                            <div class="queue-meta">NISN {{ $registrasi->nisn }}</div>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $registrasi->pengguna?->calonSiswa?->asal_sekolah ?? '-' }}</div>
                            <div class="queue-meta">Asal sekolah</div>
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $registrasi->submitted_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div>
                            <div class="queue-meta">Waktu pengajuan (WIT)</div>
                        </div>
                        <div class="queue-action">
                            <a href="{{ route('admin.verifikasi-akun.show', $registrasi) }}" class="btn btn-primary btn-sm px-3">Periksa</a>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center">
                        <div class="fw-bold text-success mb-1">Tidak ada antrean verifikasi</div>
                        <div class="text-muted">Semua registrasi akun telah ditangani.</div>
                    </div>
                @endforelse
            </div>
        </section>
    @elseif($pengguna->isAdminSekolah())
        @php
            $prestasiRanks = $pendaftarSekolah
                ->filter(fn ($item) => $item->jalur?->kode === 'prestasi')
                ->sortByDesc(function ($item) {
                    $siswa = $item->pengguna?->calonSiswa;
                    return $siswa && $siswa->nilai_tka_matematika !== null && $siswa->nilai_tka_bahasa_indonesia !== null
                        ? ((float) $siswa->nilai_tka_matematika + (float) $siswa->nilai_tka_bahasa_indonesia) / 2
                        : -1;
                })
                ->values()
                ->mapWithKeys(fn ($item, $index) => [$item->id => $index + 1]);
        @endphp
        <section class="dinas-hero mb-4">
            <div class="hero-kicker">Portal Admin Sekolah</div>
            <h2 class="fw-bold mt-2 mb-2">{{ $sekolahAdmin->pluck('nama')->join(', ') ?: 'Sekolah belum ditetapkan' }}</h2>
            <div class="hero-copy">Daftar calon murid yang memilih sekolah ini akan tampil setelah formulir mereka disimpan.</div>
        </section>

        <section class="card queue-card">
            <div class="card-header">
                <h5 class="fw-bold mb-1">Pendaftar Sekolah</h5>
                <div class="small text-muted">{{ $pendaftarSekolah->count() }} calon murid</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>NISN</th><th>Nama</th><th>Jalur</th><th>Nilai TKA / Peringkat</th><th>Status</th><th>Berkas</th></tr></thead>
                    <tbody>
                    @forelse($pendaftarSekolah as $item)
                        @php
                            $tka = $item->pengguna?->calonSiswa;
                            $rataTka = $tka && $tka->nilai_tka_matematika !== null && $tka->nilai_tka_bahasa_indonesia !== null
                                ? number_format(((float) $tka->nilai_tka_matematika + (float) $tka->nilai_tka_bahasa_indonesia) / 2, 2)
                                : '-';
                        @endphp
                        <tr>
                            <td>{{ $item->nisn }}</td>
                            <td><strong>{{ $item->nama }}</strong><div class="small text-muted">{{ $item->asal_sekolah }}</div></td>
                            <td>{{ $item->jalur?->nama ?? '-' }}</td>
                            <td data-order="{{ $rataTka === '-' ? -1 : $rataTka }}">
                                {{ $rataTka }}
                                @if($item->jalur?->kode === 'prestasi')
                                    <div class="small fw-bold text-primary">Peringkat #{{ $prestasiRanks[$item->id] ?? '-' }}</div>
                                @endif
                            </td>
                            <td><span class="badge {{ $item->isSubmitted() ? 'text-bg-success' : 'text-bg-warning' }}">{{ $item->isSubmitted() ? 'Final' : 'Draft' }}</span></td>
                            <td>
                                @if($item->dokumen_pendukung)
                                    <a href="{{ $item->berkasUrl('dokumen_pendukung') }}" target="_blank" class="btn btn-sm btn-outline-primary">Pendukung</a>
                                @else
                                    <span class="text-muted small">Tidak wajib</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted p-5">Belum ada calon murid yang memilih sekolah ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @else
        <div class="student-empty-dashboard">
            <section class="student-welcome-card">
                <div class="student-welcome-icon">&#10003;</div>
                <div class="text-uppercase small fw-bold text-success mb-2">Akun Terverifikasi</div>
                <h2 class="fw-bold mb-2">Selamat datang, {{ $pengguna->nama_pengguna ?: $pengguna->id_pengguna }}</h2>
                @if($formulirSaya)
                    <p class="text-muted mb-4">Data pendaftaran Anda sudah tersimpan. Lanjutkan dari tahap terakhir atau periksa sebelum dikirim final.</p>
                    <a href="{{ $formulirSaya->isSubmitted() ? route('formulir.riwayat') : route('formulir.edit', $formulirSaya) }}" class="btn btn-primary btn-lg">
                        {{ $formulirSaya->isSubmitted() ? 'Lihat Pendaftaran' : 'Lanjutkan Pengisian' }}
                    </a>
                @else
                    <p class="text-muted mb-4">Lengkapi biodata, unggah pas foto, lalu pilih sekolah tujuan. Jalur pendaftaran akan menyesuaikan zonasi sekolah yang dipilih.</p>
                    <a href="{{ route('formulir.create') }}" class="btn btn-primary btn-lg">Mulai Lengkapi Pendaftaran</a>
                @endif
            </section>
        </div>
    @endif
</x-layouts.app>
