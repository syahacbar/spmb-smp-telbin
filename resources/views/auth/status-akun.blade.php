<x-layouts.app title="Status Registrasi Akun">
    @php
        $status = $registrasi?->status ?? 'menunggu_verifikasi';
        $statusMeta = match ($status) {
            'terverifikasi' => [
                'success',
                'Akun Terverifikasi',
                'Akun sudah aktif dan dapat digunakan untuk melanjutkan pendaftaran.',
                'Akun Anda siap digunakan',
            ],
            'perlu_perbaikan' => [
                'warning',
                'Perlu Perbaikan',
                'Periksa catatan Dinas Pendidikan lalu kirim ulang data yang benar.',
                'Ada data yang perlu diperbaiki',
            ],
            'ditolak' => [
                'danger',
                'Registrasi Ditolak',
                'Registrasi belum dapat disetujui. Hubungi Admin Dinas bila memerlukan penjelasan.',
                'Hubungi panitia untuk informasi',
            ],
            default => [
                'info',
                'Menunggu Verifikasi',
                'Alamat domisili dan Kartu Keluarga sedang diperiksa oleh Dinas Pendidikan.',
                'Data sedang diperiksa panitia',
            ],
        };
    @endphp

    <style>
        .account-status-page .status-panel {
            border: 0;
            background: rgba(255, 255, 255, .97);
            box-shadow: 0 28px 80px rgba(3, 45, 38, .3);
            backdrop-filter: blur(12px);
        }
        .account-status-page .status-page-shell {
            width: 100%;
            max-width: none;
        }
        .account-status-page .status-page-row {
            min-height: calc(100vh - 2rem);
        }
        .account-status-page .status-panel,
        .account-status-page .status-panel > .card-body {
            min-height: 100%;
        }
        .status-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .status-badge-card {
            border: 1px solid var(--status-border, var(--telbin-line));
            border-radius: .85rem;
            background: var(--status-bg, var(--telbin-soft));
            color: var(--status-color, var(--telbin-forest-dark));
            padding: 1rem;
        }
        .status-badge-card.status-menunggu_verifikasi {
            --status-border: #9bd4df;
            --status-bg: #e9f7fa;
            --status-color: #075e70;
        }
        .status-badge-card.status-terverifikasi {
            --status-border: #9bcbb9;
            --status-bg: #e4f3ed;
            --status-color: var(--telbin-forest-dark);
        }
        .status-badge-card.status-perlu_perbaikan {
            --status-border: #e8c978;
            --status-bg: #fff7df;
            --status-color: #77520a;
        }
        .status-badge-card.status-ditolak {
            --status-border: #efb3ad;
            --status-bg: #fff0ee;
            --status-color: #8b2920;
        }
        .status-badge-icon {
            display: inline-flex;
            width: 42px;
            height: 42px;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: rgba(255, 255, 255, .72);
            font-size: 1.1rem;
            font-weight: 900;
        }
        .status-identity-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem 1.25rem;
        }
        .status-identity-item {
            min-width: 0;
        }
        .status-identity-label {
            display: block;
            margin-bottom: .15rem;
            color: #64748b;
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .status-identity-value {
            color: #0f172a;
            overflow-wrap: anywhere;
        }
        .status-section {
            border: 1px solid var(--telbin-line);
            border-radius: .8rem;
            background: #fff;
            padding: 1rem;
        }
        .status-section-title {
            margin-bottom: .85rem;
            color: #0f172a;
            font-size: .92rem;
            font-weight: 800;
        }
        .status-account-ref {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 999px;
            background: rgba(6, 63, 53, .42);
            color: #fff;
            padding: .5rem .8rem;
            font-size: .85rem;
            font-weight: 700;
        }
        .status-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
            margin-top: 1rem;
        }
        .account-status-page .btn-success {
            background: var(--telbin-forest);
            border-color: var(--telbin-forest);
        }
        .account-status-page .btn-success:hover,
        .account-status-page .btn-success:focus {
            background: var(--telbin-forest-dark);
            border-color: var(--telbin-forest-dark);
        }
        @media (min-width: 992px) {
            .account-status-page {
                padding: 1rem 1.5rem !important;
            }
            .account-status-page .auth-copy {
                position: sticky;
                top: 2rem;
                display: flex;
                min-height: calc(100vh - 2rem);
                flex-direction: column;
                justify-content: center;
                padding-right: 2rem;
            }
            .account-status-page .status-panel-column {
                display: flex;
            }
            .account-status-page .status-panel {
                width: 100%;
            }
        }
        @media (max-width: 767.98px) {
            .status-identity-grid {
                grid-template-columns: 1fr;
            }
            .status-heading {
                align-items: stretch;
                flex-direction: column;
            }
            .status-heading form,
            .status-heading .btn {
                width: 100%;
            }
        }
    </style>

    <div class="auth-page register-auth-page account-status-page d-flex align-items-center py-4 py-lg-5">
        <div class="container-fluid status-page-shell">
            <div class="row align-items-stretch justify-content-center g-4 g-xl-5 status-page-row">
                <div class="col-lg-5 auth-copy">
                    <div class="auth-school-badge mb-4">
                        <img src="{{ asset('images/logotelukbintuni.png') }}" alt="Logo Kabupaten Teluk Bintuni" class="auth-logo">
                        <div>
                            <div class="auth-kicker">Portal Resmi SPMB</div>
                            <div class="auth-school-name">SMP Kabupaten Teluk Bintuni</div>
                        </div>
                    </div>

                    <h1 class="fw-bold">Status Registrasi Akun</h1>
                    <p class="mt-3 mb-4">Pantau proses pemeriksaan data domisili dan Kartu Keluarga calon siswa pada halaman ini.</p>

                    <div class="auth-info-grid">
                        <div class="auth-feature">
                            <span class="auth-feature-mark">1</span>
                            <span>Data registrasi diterima oleh sistem SPMB</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">2</span>
                            <span>Dinas Pendidikan memeriksa domisili dan dokumen</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">3</span>
                            <span>Setelah terverifikasi, login untuk melanjutkan pendaftaran</span>
                        </div>
                    </div>

                    <div class="status-account-ref mt-4">
                        NISN {{ $pengguna->id_pengguna }}
                    </div>
                    <p class="auth-note mt-3 mb-0">{{ $statusMeta[3] }}.</p>
                </div>

                <div class="col-md-10 col-lg-7 status-panel-column">
                    <div class="card status-panel">
                        <div class="card-body p-4 p-md-5">
                            @include('partials.flash')

                            <div class="status-heading">
                                <div>
                                    <div class="text-muted small text-uppercase fw-bold">Registrasi Akun SPMB</div>
                                    <h3 class="fw-bold mb-1">Status Akun Calon Murid</h3>
                                    <div class="text-muted">Terakhir diajukan {{ $registrasi?->submitted_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div>
                                </div>
                                <form method="post" action="{{ route('akun.logout') }}">
                                    @csrf
                                    <button class="btn btn-outline-secondary">Keluar</button>
                                </form>
                            </div>

                            <div class="status-badge-card status-{{ $status }} mb-3">
                                <div class="d-flex align-items-start gap-3">
                                    <span class="status-badge-icon">
                                        {{ $status === 'terverifikasi' ? '✓' : ($status === 'ditolak' ? '!' : '…') }}
                                    </span>
                                    <div>
                                        <h4 class="fw-bold mb-1">{{ $statusMeta[1] }}</h4>
                                        <div>{{ $statusMeta[2] }}</div>
                                    </div>
                                </div>
                                @if($registrasi?->catatan_verifikasi)
                                    <hr>
                                    <div class="fw-bold mb-1">Catatan Dinas Pendidikan</div>
                                    <div>{{ $registrasi->catatan_verifikasi }}</div>
                                @endif
                            </div>

                            <section class="status-section">
                                <div class="status-section-title">Identitas Calon Siswa</div>
                                <div class="status-identity-grid">
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">NISN</span>
                                        <strong class="status-identity-value">{{ $pengguna->id_pengguna }}</strong>
                                    </div>
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">Nama</span>
                                        <strong class="status-identity-value">{{ $calonSiswa?->nama }}</strong>
                                    </div>
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">Tempat, Tanggal Lahir</span>
                                        <strong class="status-identity-value">{{ $calonSiswa?->tempat_lahir }}, {{ $calonSiswa?->tanggal_lahir?->translatedFormat('d F Y') }}</strong>
                                    </div>
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">Asal Sekolah</span>
                                        <strong class="status-identity-value">{{ $calonSiswa?->asal_sekolah }}</strong>
                                    </div>
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">Nomor WhatsApp</span>
                                        <strong class="status-identity-value">+{{ $pengguna->telpon }}</strong>
                                    </div>
                                    <div class="status-identity-item">
                                        <span class="status-identity-label">Kabupaten</span>
                                        <strong class="status-identity-value">{{ $registrasi?->kabupaten ?? 'Teluk Bintuni' }}</strong>
                                    </div>
                                </div>
                            </section>

                            @if($status === 'terverifikasi')
                                <div class="status-actions">
                                    <form method="post" action="{{ route('akun.status.continue') }}" class="w-100">
                                        @csrf
                                        <button class="btn btn-success btn-lg w-100">Masuk ke Dashboard dan Lanjutkan Pendaftaran</button>
                                    </form>
                                </div>
                            @elseif($status === 'perlu_perbaikan')
                                <section class="status-section mt-3">
                                    <div class="status-section-title mb-1">Perbaiki Data Registrasi</div>
                                    <div class="small text-muted mb-3">KK tidak wajib diunggah ulang jika dokumen sebelumnya sudah benar.</div>

                                    <form method="post" action="{{ route('akun.perbaikan') }}" enctype="multipart/form-data" class="row g-3">
                                        @csrf
                                        @method('put')
                                        <div class="col-md-6">
                                            <label class="form-label">Distrik/Kecamatan</label>
                                            <select name="kecamatan_id" class="form-select" data-kecamatan required>
                                                <option value="">Pilih distrik</option>
                                                @foreach($kecamatanOptions as $kecamatan)
                                                    <option value="{{ $kecamatan->id }}" @selected((string) old('kecamatan_id', $registrasi->kecamatan_id) === (string) $kecamatan->id)>{{ $kecamatan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Kelurahan/Kampung</label>
                                            <select name="kelurahan_id" class="form-select" data-kelurahan required>
                                                <option value="">Pilih kampung</option>
                                                @foreach($kelurahanOptions as $kelurahan)
                                                    <option value="{{ $kelurahan->id }}" data-kecamatan="{{ $kelurahan->kecamatan_id }}" @selected((string) old('kelurahan_id', $registrasi->kelurahan_id) === (string) $kelurahan->id)>{{ $kelurahan->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Detail Alamat</label>
                                            <textarea name="detail_alamat" class="form-control" rows="2" required>{{ old('detail_alamat', $registrasi->detail_alamat) }}</textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor WhatsApp</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+62</span>
                                                <input name="no_wa" class="form-control" value="{{ old('no_wa', preg_replace('/^62/', '', $pengguna->telpon)) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Ganti Kartu Keluarga</label>
                                            <input type="file" name="kartu_keluarga" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                                            <div class="form-text">PDF/gambar maksimal 4 MB.</div>
                                        </div>
                                        <div class="col-12 d-grid">
                                            <button class="btn btn-primary">Kirim Ulang untuk Verifikasi</button>
                                        </div>
                                    </form>
                                </section>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const kecamatan = document.querySelector('[data-kecamatan]');
        const kelurahan = document.querySelector('[data-kelurahan]');
        const options = kelurahan ? Array.from(kelurahan.querySelectorAll('option[data-kecamatan]')) : [];

        function filterKelurahan() {
            options.forEach((option) => {
                option.hidden = option.dataset.kecamatan !== kecamatan?.value;
                option.disabled = option.hidden;
            });

            if (kelurahan?.selectedOptions[0]?.disabled) {
                kelurahan.value = '';
            }
        }

        kecamatan?.addEventListener('change', filterKelurahan);
        filterKelurahan();
    </script>
</x-layouts.app>
