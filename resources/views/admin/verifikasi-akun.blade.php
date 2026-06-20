<x-layouts.app :pengguna="$pengguna" title="Pemeriksaan Registrasi Akun">
    @php
        $statusLabels = [
            'menunggu_verifikasi' => ['Menunggu Pemeriksaan', 'warning'],
            'terverifikasi' => ['Disetujui', 'success'],
            'perlu_perbaikan' => ['Perlu Perbaikan', 'info'],
            'ditolak' => ['Ditolak', 'danger'],
        ];
        [$statusLabel, $statusColor] = $statusLabels[$registrasi->status] ?? ['Tidak Diketahui', 'secondary'];
        $siswa = $registrasi->pengguna;
    @endphp

    <style>
        .verification-hero {
            border: 1px solid #b9d9ce;
            border-radius: 1rem;
            background: linear-gradient(135deg, #063f35, #0b5d4b 58%, #0788a8);
            color: #fff;
            padding: 1.4rem;
            box-shadow: 0 18px 42px rgba(6, 63, 53, .18);
        }
        .verification-hero .text-muted { color: rgba(255,255,255,.72) !important; }
        .verification-grid {
            display: grid;
            grid-template-columns: minmax(320px, .78fr) minmax(420px, 1.22fr);
            gap: 1rem;
            align-items: start;
        }
        .verification-card {
            border-color: #cfe4dc;
            border-radius: .9rem;
            box-shadow: 0 12px 30px rgba(16, 55, 47, .07);
        }
        .verification-card .card-header {
            border-bottom-color: #dcece6;
            border-radius: .9rem .9rem 0 0;
            padding: 1rem 1.15rem;
        }
        .identity-list {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }
        .identity-item {
            min-width: 0;
            border-bottom: 1px solid #edf4f1;
            padding-bottom: .65rem;
        }
        .identity-item.wide { grid-column: 1 / -1; }
        .identity-label {
            display: block;
            color: #667085;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .identity-value {
            display: block;
            margin-top: .2rem;
            color: #12372f;
            overflow-wrap: anywhere;
        }
        .address-check {
            border: 1px solid #a8d5c6;
            border-radius: .8rem;
            background: #eef7f3;
            padding: 1rem;
        }
        .kk-frame {
            width: 100%;
            min-height: 640px;
            border: 0;
            border-radius: .7rem;
            background: #eef2f1;
        }
        .kk-image {
            display: block;
            width: 100%;
            max-height: 760px;
            border-radius: .7rem;
            background: #eef2f1;
            object-fit: contain;
        }
        .decision-panel {
            position: sticky;
            bottom: 1rem;
            border: 1px solid #cfe4dc;
            border-radius: .9rem;
            background: rgba(255, 255, 255, .96);
            padding: 1rem;
            box-shadow: 0 18px 46px rgba(6, 63, 53, .14);
            backdrop-filter: blur(12px);
        }
        .history-item {
            position: relative;
            border-left: 3px solid #cfe4dc;
            padding: 0 0 1rem 1rem;
        }
        .history-item:last-child { padding-bottom: 0; }
        .history-item::before {
            content: "";
            position: absolute;
            top: .2rem;
            left: -.43rem;
            width: .7rem;
            height: .7rem;
            border-radius: 50%;
            background: #0b5d4b;
        }
        @media (max-width: 991.98px) {
            .verification-grid { grid-template-columns: 1fr; }
            .kk-frame { min-height: 520px; }
            .decision-panel { position: static; }
        }
        @media (max-width: 575.98px) {
            .identity-list { grid-template-columns: 1fr; }
            .identity-item.wide { grid-column: auto; }
        }
    </style>

    <div class="verification-hero mb-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
            <div>
                <a href="{{ route('admin.pengguna', ['status' => 'menunggu_verifikasi']) }}" class="text-white text-decoration-none small">&larr; Kembali ke antrean</a>
                <h3 class="fw-bold mt-2 mb-1">Pemeriksaan Domisili Calon Murid</h3>
                <div class="text-muted">Cocokkan alamat yang diinput dengan alamat pada Kartu Keluarga.</div>
            </div>
            <div class="text-end">
                <span class="badge text-bg-{{ $statusColor }} fs-6">{{ $statusLabel }}</span>
                <div class="small mt-2 text-muted">Diajukan {{ $registrasi->submitted_at?->translatedFormat('d F Y, H:i') ?? '-' }} WIT</div>
            </div>
        </div>
    </div>

    @include('partials.flash')

    <div class="verification-grid">
        <div class="d-grid gap-3">
            <section class="card verification-card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Data Calon Murid</h5>
                </div>
                <div class="card-body">
                    <div class="identity-list">
                        <div class="identity-item">
                            <span class="identity-label">NISN</span>
                            <strong class="identity-value">{{ $siswa->id_pengguna }}</strong>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Nama</span>
                            <strong class="identity-value">{{ $calonSiswa?->nama ?? $siswa->nama_pengguna }}</strong>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Tempat, Tanggal Lahir</span>
                            <strong class="identity-value">{{ $calonSiswa?->tempat_lahir ?? '-' }}, {{ $calonSiswa?->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</strong>
                        </div>
                        <div class="identity-item">
                            <span class="identity-label">Asal Sekolah</span>
                            <strong class="identity-value">{{ $calonSiswa?->asal_sekolah ?? '-' }}</strong>
                        </div>
                        <div class="identity-item wide">
                            <span class="identity-label">Nomor WhatsApp</span>
                            <strong class="identity-value">+{{ $siswa->telpon }}</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card verification-card">
                <div class="card-header">
                    <h5 class="fw-bold mb-0">Alamat yang Diinput</h5>
                </div>
                <div class="card-body">
                    <div class="address-check">
                        <div class="identity-list">
                            <div class="identity-item">
                                <span class="identity-label">Kabupaten</span>
                                <strong class="identity-value">{{ $registrasi->kabupaten }}</strong>
                            </div>
                            <div class="identity-item">
                                <span class="identity-label">Distrik/Kecamatan</span>
                                <strong class="identity-value">{{ $kecamatan ?: '-' }}</strong>
                            </div>
                            <div class="identity-item">
                                <span class="identity-label">Kelurahan/Kampung</span>
                                <strong class="identity-value">{{ $kelurahan ?: '-' }}</strong>
                            </div>
                            <div class="identity-item wide">
                                <span class="identity-label">Detail Alamat</span>
                                <strong class="identity-value">{{ $registrasi->detail_alamat ?: '-' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light border mt-3 mb-0 small">
                        Pastikan distrik, kampung, dan detail alamat di atas sesuai dengan alamat keluarga yang terbaca pada KK.
                    </div>
                </div>
            </section>

            @if($riwayat->isNotEmpty())
                <section class="card verification-card">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Riwayat Verifikasi</h5>
                    </div>
                    <div class="card-body">
                        @foreach($riwayat as $item)
                            <div class="history-item">
                                <div class="fw-bold">{{ str($item->status_baru)->replace('_', ' ')->title() }}</div>
                                <div class="small text-muted">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y, H:i') }} WIT · {{ $item->nama_petugas ?: 'Sistem/Calon Murid' }}</div>
                                @if($item->catatan)
                                    <div class="small mt-1">{{ $item->catatan }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <div class="d-grid gap-3">
            <section class="card verification-card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="fw-bold mb-0">Kartu Keluarga</h5>
                        <div class="small text-muted">Dokumen tersimpan privat dan hanya dapat dibuka Admin Dinas.</div>
                    </div>
                    @if($registrasi->kartuKeluargaTersedia())
                        <a href="{{ route('admin.registrasi.kk', $registrasi) }}" target="_blank" class="btn btn-sm btn-outline-primary">Buka Tab Baru</a>
                    @endif
                </div>
                <div class="card-body">
                    @if(! $registrasi->kartuKeluargaTersedia())
                        <div class="alert alert-danger mb-0">Berkas Kartu Keluarga tidak tersedia. Registrasi tidak dapat disetujui.</div>
                    @elseif($registrasi->kartuKeluargaIsImage())
                        <img src="{{ route('admin.registrasi.kk', $registrasi) }}" class="kk-image" alt="Kartu Keluarga {{ $siswa->id_pengguna }}">
                    @else
                        <iframe src="{{ route('admin.registrasi.kk', $registrasi) }}#toolbar=1&navpanes=0" class="kk-frame" title="Kartu Keluarga {{ $siswa->id_pengguna }}"></iframe>
                    @endif
                </div>
            </section>

            <section class="decision-panel">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Keputusan Verifikasi</h5>
                        <div class="small text-muted">Keputusan dan catatan akan tampil pada panel status akun calon murid.</div>
                    </div>
                </div>

                @if($registrasi->status !== 'terverifikasi')
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <form method="post" action="{{ route('admin.pengguna.verifikasi', $siswa) }}">
                            @csrf
                            <button class="btn btn-success" data-confirm="Alamat domisili dan Kartu Keluarga sudah sesuai. Setujui akun ini?" @disabled(! $registrasi->kartuKeluargaTersedia())>
                                Setujui Akun
                            </button>
                        </form>
                    </div>

                    <form method="post" action="{{ route('admin.pengguna.status-verifikasi', $siswa) }}">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Keputusan</label>
                                <select name="status" class="form-select" required>
                                    <option value="perlu_perbaikan">Perlu Perbaikan</option>
                                    <option value="ditolak">Tolak Registrasi</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Catatan untuk Calon Murid</label>
                                <textarea name="catatan" class="form-control" rows="3" maxlength="1000" placeholder="Contoh: Alamat kampung yang dipilih tidak sesuai dengan alamat pada KK." required></textarea>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button class="btn btn-outline-danger" data-confirm="Simpan keputusan dan catatan verifikasi ini?">Simpan Keputusan</button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success mb-0">
                        Akun telah disetujui{{ $registrasi->verified_at ? ' pada '.$registrasi->verified_at->translatedFormat('d F Y, H:i').' WIT' : '' }}.
                        Calon murid dapat login dan masuk ke dashboard.
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.app>
