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
        .address-edit-form {
            display: grid;
            gap: .9rem;
        }
        .address-edit-form .form-label {
            color: #344054;
            font-size: .78rem;
            font-weight: 900;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .address-edit-note {
            border: 1px solid #d9e7e4;
            border-radius: .7rem;
            background: #fff;
            padding: .75rem .85rem;
            color: #52647d;
            font-size: .85rem;
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
            max-width: 100%;
            max-height: 100%;
            border-radius: .7rem;
            background: #eef2f1;
            object-fit: contain;
        }
        .decision-panel {
            border: 1px solid #cfe4dc;
            border-radius: .9rem;
            background: #fff;
            padding: 1.25rem;
            box-shadow: 0 12px 30px rgba(16, 55, 47, .07);
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
                        <form method="post" action="{{ route('admin.verifikasi-akun.alamat', $registrasi) }}" class="address-edit-form">
                            @csrf
                            @method('put')

                            <div class="address-edit-note">
                                Koreksi alamat sesuai Kartu Keluarga sebelum akun disetujui. Perubahan ini akan menjadi dasar pilihan sekolah jalur domisili.
                            </div>

                            <div>
                                <label class="form-label" for="alamat-kabupaten">Kabupaten</label>
                                <input id="alamat-kabupaten" type="text" class="form-control" value="{{ $registrasi->kabupaten ?: 'Teluk Bintuni' }}" readonly>
                            </div>

                            <div>
                                <label class="form-label" for="alamat-kecamatan">Distrik/Kecamatan</label>
                                <select id="alamat-kecamatan" name="kecamatan_id" class="form-select @error('kecamatan_id') is-invalid @enderror" data-admin-kecamatan required>
                                    <option value="">Pilih distrik/kecamatan</option>
                                    @foreach($kecamatanOptions as $item)
                                        <option value="{{ $item->id }}" @selected((string) old('kecamatan_id', $registrasi->kecamatan_id) === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kecamatan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="alamat-kelurahan">Kelurahan/Desa/Kampung</label>
                                <select id="alamat-kelurahan" name="kelurahan_id" class="form-select @error('kelurahan_id') is-invalid @enderror" data-admin-kelurahan data-selected="{{ old('kelurahan_id', $registrasi->kelurahan_id) }}" required>
                                    <option value="">Pilih kelurahan/desa/kampung</option>
                                    @foreach($kelurahanOptions as $item)
                                        <option value="{{ $item->id }}" data-kecamatan="{{ $item->kecamatan_id }}" @selected((string) old('kelurahan_id', $registrasi->kelurahan_id) === (string) $item->id)>{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                @error('kelurahan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label class="form-label" for="alamat-detail">Detail Alamat</label>
                                <textarea id="alamat-detail" name="detail_alamat" class="form-control @error('detail_alamat') is-invalid @enderror" rows="3" maxlength="1000" placeholder="Jalan, RT/RW, patokan, atau detail alamat lain" required>{{ old('detail_alamat', $registrasi->detail_alamat) }}</textarea>
                                @error('detail_alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary" data-confirm="Simpan perubahan alamat domisili akun ini?">Simpan Alamat</button>
                            </div>
                        </form>
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
                        <div class="d-flex flex-wrap gap-2 mb-3 align-items-center justify-content-center bg-light p-2 rounded border border-light-subtle">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-zoom-in" title="Zoom In">
                                <i class="bi bi-zoom-in"></i> Zoom In
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-zoom-out" title="Zoom Out">
                                <i class="bi bi-zoom-out"></i> Zoom Out
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-rotate-left" title="Rotate Left">
                                <i class="bi bi-arrow-counterclockwise"></i> Putar Kiri
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-rotate-right" title="Rotate Right">
                                <i class="bi bi-arrow-clockwise"></i> Putar Kanan
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-reset-view" title="Reset">
                                <i class="bi bi-arrow-repeat"></i> Reset
                            </button>
                        </div>
                        <div class="kk-image-container border rounded bg-light overflow-hidden position-relative d-flex align-items-center justify-content-center" style="min-height: 640px; max-height: 760px; cursor: grab;">
                            <img id="kk-image-preview" src="{{ route('admin.registrasi.kk', $registrasi) }}" class="kk-image" alt="Kartu Keluarga {{ $siswa->id_pengguna }}" style="transition: transform 0.2s ease; max-width: 100%; max-height: 100%; transform-origin: center center;">
                        </div>
                    @else
                        <iframe src="{{ route('admin.registrasi.kk', $registrasi) }}#toolbar=1&navpanes=0" class="kk-frame" title="Kartu Keluarga {{ $siswa->id_pengguna }}"></iframe>
                    @endif
                </div>
            </section>

            <section class="decision-panel card verification-card mt-3">
                <div class="card-header bg-white border-bottom pb-2">
                    <h5 class="fw-bold mb-1">Keputusan Verifikasi</h5>
                    <div class="small text-muted">Keputusan dan catatan akan tampil pada panel status akun calon murid.</div>
                </div>

                <div class="card-body">
                    @if($registrasi->status !== 'terverifikasi')
                        <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom">
                            <form method="post" action="{{ route('admin.pengguna.verifikasi', $siswa) }}">
                                @csrf
                                <button class="btn btn-success" data-confirm="Alamat domisili dan Kartu Keluarga sudah sesuai. Setujui akun ini?" @disabled(! $registrasi->kartuKeluargaTersedia())>
                                    Setujui Akun
                                </button>
                            </form>
                        </div>

                        <form method="post" action="{{ route('admin.pengguna.status-verifikasi', $siswa) }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold" for="select-keputusan">Keputusan</label>
                                    <select id="select-keputusan" name="status" class="form-select" required>
                                        <option value="perlu_perbaikan">Perlu Perbaikan</option>
                                        <option value="ditolak">Tolak Registrasi</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3" id="template-perbaikan-container">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Template Catatan Perbaikan</label>
                                        <div class="d-flex flex-column gap-2 bg-light p-3 rounded border">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="temp_catatan" id="temp-dokumen" value="Mohon mengunggah (upload) dokumen Kartu Keluarga">
                                                <label class="form-check-label small" for="temp-dokumen">
                                                    <strong>Dokumen tidak sesuai:</strong> Mohon mengunggah (upload) dokumen Kartu Keluarga
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="temp_catatan" id="temp-terpotong" value="Mohon mengunggah (upload) dokumen KK secara utuh dan tidak terpotong">
                                                <label class="form-check-label small" for="temp-terpotong">
                                                    <strong>KK terpotong:</strong> Mohon mengunggah (upload) dokumen KK secara utuh dan tidak terpotong
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="temp_catatan" id="temp-nama" value="Nama calon murid tidak tertera di dalam KK yang diunggah (upload).">
                                                <label class="form-check-label small" for="temp-nama">
                                                    <strong>Nama tidak sesuai:</strong> Nama calon murid tidak tertera di dalam KK yang diunggah (upload).
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="temp_catatan" id="temp-jelas" value="Mohon mengunggah (upload) dokumen KK yang lebih jelas dan dapat dibaca">
                                                <label class="form-check-label small" for="temp-jelas">
                                                    <strong>KK tidak jelas:</strong> Mohon mengunggah (upload) dokumen KK yang lebih jelas dan dapat dibaca
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="temp_catatan" id="temp-lainnya" value="lainnya">
                                                <label class="form-check-label small" for="temp-lainnya">
                                                    <strong>Lainnya</strong> (Isi manual)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <label class="form-label fw-bold" for="textarea-catatan">Catatan untuk Calon Murid</label>
                                    <textarea id="textarea-catatan" name="catatan" class="form-control" rows="3" maxlength="1000" placeholder="Contoh: Alamat kampung yang dipilih tidak sesuai dengan alamat pada KK." required></textarea>
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
                </div>
            </section>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // -- District & Village Sync Logic --
            const districtSelect = document.querySelector('[data-admin-kecamatan]');
            const villageSelect = document.querySelector('[data-admin-kelurahan]');

            if (districtSelect && villageSelect) {
                const villageOptions = Array.from(villageSelect.options)
                    .filter((option) => option.value)
                    .map((option) => ({
                        value: option.value,
                        text: option.textContent,
                        district: option.dataset.kecamatan,
                    }));

                function syncVillages() {
                    const selectedDistrict = districtSelect.value;
                    const selectedVillage = villageSelect.dataset.selected || villageSelect.value;

                    villageSelect.replaceChildren(new Option('Pilih kelurahan/desa/kampung', ''));

                    villageOptions
                        .filter((option) => String(option.district) === String(selectedDistrict))
                        .forEach((option) => {
                            const element = new Option(option.text, option.value);
                            element.selected = String(option.value) === String(selectedVillage);
                            villageSelect.appendChild(element);
                        });

                    if (! Array.from(villageSelect.options).some((option) => option.selected && option.value)) {
                        villageSelect.value = '';
                    }

                    villageSelect.dataset.selected = villageSelect.value;
                }

                districtSelect.addEventListener('change', function () {
                    villageSelect.dataset.selected = '';
                    syncVillages();
                });

                syncVillages();
            }

            // -- Document Zoom, Rotate, & Drag Logic --
            const img = document.getElementById('kk-image-preview');
            const container = document.querySelector('.kk-image-container');
            
            if (img && container) {
                let currentScale = 1;
                let currentRotation = 0;
                let translateX = 0;
                let translateY = 0;
                let isDragging = false;
                let startX = 0;
                let startY = 0;

                function updateTransform() {
                    img.style.transform = `translate(${translateX}px, ${translateY}px) scale(${currentScale}) rotate(${currentRotation}deg)`;
                }

                document.getElementById('btn-zoom-in')?.addEventListener('click', function() {
                    currentScale += 0.25;
                    updateTransform();
                });

                document.getElementById('btn-zoom-out')?.addEventListener('click', function() {
                    if (currentScale > 0.3) {
                        currentScale -= 0.25;
                        updateTransform();
                    }
                });

                document.getElementById('btn-rotate-left')?.addEventListener('click', function() {
                    currentRotation -= 90;
                    updateTransform();
                });

                document.getElementById('btn-rotate-right')?.addEventListener('click', function() {
                    currentRotation += 90;
                    updateTransform();
                });

                document.getElementById('btn-reset-view')?.addEventListener('click', function() {
                    currentScale = 1;
                    currentRotation = 0;
                    translateX = 0;
                    translateY = 0;
                    updateTransform();
                });

                // Dragging logic
                container.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    isDragging = true;
                    startX = e.clientX - translateX;
                    startY = e.clientY - translateY;
                    container.style.cursor = 'grabbing';
                });

                window.addEventListener('mousemove', function(e) {
                    if (!isDragging) return;
                    translateX = e.clientX - startX;
                    translateY = e.clientY - startY;
                    updateTransform();
                });

                window.addEventListener('mouseup', function() {
                    isDragging = false;
                    container.style.cursor = 'grab';
                });

                // Touch support
                container.addEventListener('touchstart', function(e) {
                    if (e.touches.length === 1) {
                        isDragging = true;
                        startX = e.touches[0].clientX - translateX;
                        startY = e.touches[0].clientY - translateY;
                    }
                });

                window.addEventListener('touchmove', function(e) {
                    if (!isDragging || e.touches.length !== 1) return;
                    translateX = e.touches[0].clientX - startX;
                    translateY = e.touches[0].clientY - startY;
                    updateTransform();
                });

                window.addEventListener('touchend', function() {
                    isDragging = false;
                });
            }

            // -- Template Notes Logic --
            const statusSelect = document.querySelector('select[name="status"]');
            const templateContainer = document.getElementById('template-perbaikan-container');
            const catatanTextarea = document.querySelector('textarea[name="catatan"]');
            const templates = document.querySelectorAll('input[name="temp_catatan"]');

            if (statusSelect && templateContainer && catatanTextarea) {
                function toggleTemplates() {
                    if (statusSelect.value === 'perlu_perbaikan') {
                        templateContainer.style.display = 'block';
                    } else {
                        templateContainer.style.display = 'none';
                        // Clean radio checks if hidden
                        templates.forEach(radio => radio.checked = false);
                    }
                }

                statusSelect.addEventListener('change', toggleTemplates);
                toggleTemplates(); // run on load

                templates.forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            if (this.value === 'lainnya') {
                                catatanTextarea.value = '';
                                catatanTextarea.focus();
                            } else {
                                catatanTextarea.value = this.value;
                            }
                        }
                    });
                });
            }
        });
    </script>
</x-layouts.app>
