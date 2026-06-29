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
        .queue-card .table th,
        .queue-card .table td {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
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
                    <div class="hero-kicker">Dinas Pendidikan, Kebudayaan, Pemuda dan Olahraga Kabupaten Teluk Bintuni</div>
                    <h2 class="fw-bold mt-2 mb-2">Dashboard Verifikasi Akun SPMB</h2>
                    <div class="hero-copy">Periksa kesesuaian alamat domisili dengan Kartu Keluarga sebelum memberikan akses kepada calon murid.</div>
                </div>
                <a href="{{ route('admin.pengguna', ['status' => 'menunggu_verifikasi']) }}" class="btn btn-warning fw-bold px-4">Buka Antrean Verifikasi</a>
            </div>
        </section>



        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-dark">Status Verifikasi Akun Calon Murid</h5>
            <div class="row g-3">
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
        </div>



        <section class="card queue-card mt-4">
            <div class="card-header">
                <h5 class="fw-bold mb-1">Statistik Pendaftar di Semua Sekolah Berdasarkan Jalur</h5>
                <div class="small text-muted">Jumlah pendaftar (formulir final) yang masuk di setiap sekolah tujuan.</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NPSN</th>
                            <th>Nama Sekolah</th>
                            @foreach($jalurs as $jalur)
                                <th class="text-center">{{ $jalur->nama }}</th>
                            @endforeach
                            <th class="text-center fw-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sekolahStats as $stat)
                            <tr>
                                <td>{{ $stat['npsn'] }}</td>
                                <td><strong>{{ $stat['nama'] }}</strong></td>
                                @foreach($jalurs as $jalur)
                                    <td class="text-center">{{ $stat['pendaftar_per_jalur'][$jalur->id] ?? 0 }}</td>
                                @endforeach
                                <td class="text-center fw-bold text-primary">{{ $stat['total_pendaftar'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + $jalurs->count() }}" class="text-center text-muted p-4">Belum ada data pendaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($sekolahStats->isNotEmpty())
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="2" class="text-end">Total Keseluruhan</td>
                                @foreach($jalurs as $jalur)
                                    <td class="text-center text-primary">{{ $totalPerJalur[$jalur->id] ?? 0 }}</td>
                                @endforeach
                                <td class="text-center text-success" style="font-size: 1.1rem;">{{ $grandTotal }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
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

        <h5 class="fw-bold mb-3 text-dark mt-4">Kuota & Keterisian Jalur</h5>
        <div class="row g-3 mb-4">
            @forelse($jalurStats as $stat)
                @php
                    $iconData = match($stat['kode']) {
                        'domisili' => [
                            'class' => 'bi bi-geo-alt-fill',
                            'color' => '#0d6efd',
                            'bg' => '#e7f1ff',
                        ],
                        'prestasi' => [
                            'class' => 'bi bi-trophy-fill',
                            'color' => '#ffc107',
                            'bg' => '#fff9e6',
                        ],
                        'afirmasi' => [
                            'class' => 'bi bi-heart-fill',
                            'color' => '#dc3545',
                            'bg' => '#ffeef0',
                        ],
                        'mutasi' => [
                            'class' => 'bi bi-arrow-left-right',
                            'color' => '#198754',
                            'bg' => '#e8f5e9',
                        ],
                        default => [
                            'class' => 'bi bi-file-earmark-text-fill',
                            'color' => '#6c757d',
                            'bg' => '#f8f9fa',
                        ],
                    };
                @endphp
                <div class="col-sm-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 1rem; background: linear-gradient(145deg, #fff, #f8fafc); border: 1px solid #e2e8f0;">
                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 30px; height: 30px; flex-shrink: 0; background-color: {{ $iconData['bg'] }};">
                                            <i class="{{ $iconData['class'] }}" style="color: {{ $iconData['color'] }}; font-size: 0.9rem;"></i>
                                        </div>
                                        <span class="badge bg-light text-primary fw-bold px-2 py-1" style="border-radius: 0.5rem; font-size: 0.75rem;">
                                            Jalur {{ $stat['nama'] }}
                                        </span>
                                    </div>
                                    <span class="text-muted small fw-semibold">
                                        Kuota: {{ $stat['kuota'] }}
                                    </span>
                                </div>
                                <h3 class="fw-black mb-1 mt-2 text-dark" style="font-size: 1.8rem;">
                                    {{ $stat['pendaftar'] }} <span class="text-muted fs-6 fw-normal">pendaftar</span>
                                </h3>
                            </div>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted fw-semibold">Keterisian</span>
                                    <span class="small fw-bold {{ $stat['keterisian'] > 100 ? 'text-danger' : 'text-success' }}">
                                        {{ $stat['keterisian'] }}%
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 3px; background-color: #e2e8f0;">
                                    <div class="progress-bar {{ $stat['keterisian'] > 100 ? 'bg-danger' : 'bg-success' }}" 
                                         role="progressbar" 
                                         style="width: {{ min(100, $stat['keterisian']) }}%; border-radius: 3px;" 
                                         aria-valuenow="{{ $stat['keterisian'] }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-4 text-center text-muted">Belum ada jalur pendaftaran yang dikonfigurasi.</div>
                </div>
            @endforelse
        </div>



        <section class="card queue-card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1">Statistik Asal Sekolah Calon Murid</h5>
                    <div class="small text-muted">Statistik sebaran sekolah asal pendaftar yang memilih sekolah ini.</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 80px;">No.</th>
                            <th>Nama Sekolah Asal</th>
                            <th class="text-center">Formulir Draft</th>
                            <th class="text-center">Formulir Final (Terkirim)</th>
                            <th class="text-center fw-bold">Total Pendaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asalSekolahStats as $stat)
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td><strong>{{ $stat->asal_sekolah ?: 'Tidak Diketahui' }}</strong></td>
                                <td class="text-center text-warning fw-semibold">{{ $stat->total_draft }}</td>
                                <td class="text-center text-success fw-semibold">{{ $stat->total_final }}</td>
                                <td class="text-center fw-bold text-primary">{{ $stat->total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted p-4">Belum ada data pendaftar berdasarkan sekolah asal.</td>
                            </tr>
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
