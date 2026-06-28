<x-layouts.app :pengguna="$pengguna" title="Pengaturan SPMB">
    <style>
        .settings-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
        }
        .settings-tabs {
            gap: .35rem;
            border-bottom: 1px solid #d9e2ef;
        }
        .settings-tabs .nav-link {
            border: 0;
            border-bottom: 3px solid transparent;
            border-radius: .5rem .5rem 0 0;
            color: #667085;
            font-weight: 800;
            padding: .85rem 1rem;
        }
        .settings-tabs .nav-link:hover {
            border-bottom-color: #fecaca;
            color: #991b1b;
        }
        .settings-tabs .nav-link.active {
            border-bottom-color: var(--spmb-red);
            background: #fff;
            color: var(--spmb-red);
        }
        .settings-tab-content {
            padding-top: 1rem;
        }
        .bulk-action-panel {
            border: 1px solid #fed7aa;
            border-radius: .65rem;
            background: #fff7ed;
            padding: 1rem;
        }
        .whitelist-table-wrap .dt-search,
        .whitelist-table-wrap .dt-length {
            margin-bottom: .75rem;
        }
        .whitelist-table-wrap,
        .whitelist-table-wrap .dt-container,
        .whitelist-table-wrap table {
            width: 100% !important;
        }
        .settings-section-title {
            color: #172033;
            font-size: 1rem;
            font-weight: 900;
            margin: 0;
        }
        .settings-section-subtitle {
            color: #667085;
            font-size: .88rem;
            margin: .2rem 0 0;
        }
        .signature-preview {
            width: 180px;
            height: 84px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px dashed #bfdbfe;
            border-radius: .5rem;
            background: #eff6ff;
        }
        .signature-preview img {
            max-width: 160px;
            max-height: 64px;
            object-fit: contain;
        }
        .settings-table td,
        .settings-table th {
            min-width: 60px;
        }
        .settings-table .checkbox-col {
            min-width: 45px !important;
            width: 45px !important;
            text-align: center;
        }
        .settings-table .no-col {
            min-width: 50px !important;
            width: 50px !important;
            text-align: center;
        }
        .settings-table .nisn-col {
            min-width: 110px !important;
            width: 110px !important;
            text-align: center;
        }
        .settings-table .wide-col {
            min-width: 180px !important;
        }
        .settings-table .score-col {
            min-width: 100px !important;
            width: 100px !important;
            text-align: center;
        }
        .settings-table .year-col {
            min-width: 90px !important;
            width: 90px !important;
            text-align: center;
        }
        .settings-table .status-col {
            min-width: 90px !important;
            width: 90px !important;
            text-align: center;
        }
        .settings-table .action-col {
            min-width: 120px !important;
            width: 120px !important;
            text-align: center;
        }
        .primary-contact {
            border-left: 4px solid #16a34a;
        }
        .whitelist-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .75rem;
        }
        .whitelist-summary-item {
            border: 1px solid #e4e7ec;
            border-radius: .65rem;
            background: #f8fafc;
            padding: .9rem;
        }
        .whitelist-summary-item span {
            display: block;
            color: #667085;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .whitelist-summary-item strong {
            color: #172033;
            font-size: 1.35rem;
            font-weight: 900;
        }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Pengaturan SPMB</h3>
            <div class="text-muted">Kelola periode, whitelist calon murid, identitas dokumen, dan kontak layanan.</div>
        </div>
    </div>

    <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kartu-tab" data-bs-toggle="tab" data-bs-target="#kartu-pane" type="button" role="tab" aria-controls="kartu-pane" aria-selected="true">Kartu & Identitas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="whitelist-tab" data-bs-toggle="tab" data-bs-target="#whitelist-pane" type="button" role="tab" aria-controls="whitelist-pane" aria-selected="false">Whitelist Calon Siswa</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="akses-tab" data-bs-toggle="tab" data-bs-target="#akses-pane" type="button" role="tab" aria-controls="akses-pane" aria-selected="false">Akses Sekolah</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jam-pelayanan-tab" data-bs-toggle="tab" data-bs-target="#jam-pelayanan-pane" type="button" role="tab" aria-controls="jam-pelayanan-pane" aria-selected="false">Jam Pelayanan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="kontak-tab" data-bs-toggle="tab" data-bs-target="#kontak-pane" type="button" role="tab" aria-controls="kontak-pane" aria-selected="false">Kontak Panitia</button>
        </li>
    </ul>

    <div class="tab-content settings-tab-content" id="settingsTabContent">
        <section class="tab-pane fade show active card shadow-sm" id="kartu-pane" role="tabpanel" aria-labelledby="kartu-tab" tabindex="0">
            <div class="card-header">
                <h4 class="settings-section-title">Kartu Pendaftaran & Kepala Sekolah</h4>
                <p class="settings-section-subtitle">Data ini dipakai untuk nomor kartu, jadwal, catatan, dan tanda tangan pada cetak PDF/kartu.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.pengaturan.identitas') }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Tahun Pendaftaran</label>
                        <input type="text" name="tahun_pendaftaran" value="{{ old('tahun_pendaftaran', $settings['tahun_pendaftaran']) }}" class="form-control" maxlength="4" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun Pelajaran</label>
                        <input type="text" name="tahun_pelajaran" value="{{ old('tahun_pelajaran', $settings['tahun_pelajaran']) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nama Penandatangan</label>
                        <input type="text" name="kepala_nama" value="{{ old('kepala_nama', $settings['kepala_nama']) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">NIP Penandatangan</label>
                        <input type="text" name="kepala_nip" value="{{ old('kepala_nip', $settings['kepala_nip']) }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jabatan Penandatangan</label>
                        <input type="text" name="kepala_jabatan" value="{{ old('kepala_jabatan', $settings['kepala_jabatan']) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">TTD Digital</label>
                        <input type="file" name="kepala_ttd" class="form-control" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Wawancara</label>
                        <input type="text" name="tanggal_tes" value="{{ old('tanggal_tes', $settings['tanggal_tes']) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Waktu Wawancara</label>
                        <input type="text" name="waktu_tes" value="{{ old('waktu_tes', $settings['waktu_tes']) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tempat Wawancara</label>
                        <input type="text" name="tempat_tes" value="{{ old('tempat_tes', $settings['tempat_tes']) }}" class="form-control" required>
                    </div>
                    <div class="col-lg-8">
                        <label class="form-label">Catatan Kartu</label>
                        <textarea name="catatan_kartu" class="form-control" rows="4" required>{{ old('catatan_kartu', $settings['catatan_kartu']) }}</textarea>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Preview TTD Saat Ini</label>
                        <div class="signature-preview">
                            @if($settings['kepala_ttd_path'])
                                <img
                                    src="{{ str_starts_with($settings['kepala_ttd_path'], 'pengaturan/tanda-tangan/') ? route('admin.pengaturan.signature.show') : asset($settings['kepala_ttd_path']) }}"
                                    alt="Tanda tangan digital"
                                >
                            @else
                                <span class="text-muted small">Belum ada TTD</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary">Simpan Pengaturan Kartu</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="tab-pane fade card shadow-sm" id="whitelist-pane" role="tabpanel" aria-labelledby="whitelist-tab" tabindex="0">
            <div class="card-header">
                <h4 class="settings-section-title">Whitelist Calon Siswa</h4>
                <p class="settings-section-subtitle">Import data NISN berdasarkan tahun lulus. Saat cohort baru diimpor, data lama tetap tersimpan dan otomatis dinonaktifkan.</p>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold m-0" style="font-size: 1rem; color: #172033;">Daftar Whitelist Calon Siswa</h5>
                    <div class="d-flex gap-2">
                        <button type="submit" form="bulkDeactivateForm" id="bulkDeactivateBtn" class="btn btn-sm btn-outline-danger fw-bold" style="border-radius: 0.45rem;" disabled>
                            <i class="bi bi-shield-slash-fill"></i> Nonaktifkan Terpilih
                        </button>
                        <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#importWhitelistModal" style="border-radius: 0.45rem;">
                            <i class="bi bi-file-earmark-excel-fill"></i> Import Whitelist
                        </button>
                        <button class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#tambahCalonSiswaModal" style="border-radius: 0.45rem;">
                            <i class="bi bi-person-plus-fill"></i> Tambah Calon Siswa Manual
                        </button>
                    </div>
                </div>

                <form id="bulkDeactivateForm" method="post" action="{{ route('admin.pengaturan.whitelist.deactivate') }}">
                    @csrf
                    <div class="whitelist-table-wrap">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle settings-table mb-0 w-100" id="whitelistTable" style="width: 100%">
                                <thead>
                                <tr>
                                    <th class="checkbox-col">
                                        <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                    </th>
                                    <th class="no-col">No</th>
                                    <th class="nisn-col">NISN</th>
                                    <th class="wide-col">Nama</th>
                                    <th class="wide-col">Asal Sekolah</th>
                                    <th class="score-col">TKA Matematika</th>
                                    <th class="score-col">TKA Bahasa Indonesia</th>
                                    <th class="year-col">Tahun Lulus</th>
                                    <th class="status-col">Status</th>
                                    <th class="action-col">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($whitelist as $calonSiswa)
                                    <tr>
                                        <td class="checkbox-col">
                                            <input type="checkbox" name="selected_ids[]" value="{{ $calonSiswa->id }}" class="form-check-input select-row-checkbox">
                                        </td>
                                        <td class="no-col text-center">{{ $loop->iteration }}</td>
                                        <td class="nisn-col">{{ $calonSiswa->nisn }}</td>
                                        <td class="wide-col">{{ $calonSiswa->nama }}</td>
                                        <td class="wide-col">{{ $calonSiswa->asal_sekolah }}</td>
                                        <td class="score-col text-center">{{ $calonSiswa->nilai_tka_matematika ?? '-' }}</td>
                                        <td class="score-col text-center">{{ $calonSiswa->nilai_tka_bahasa_indonesia ?? '-' }}</td>
                                        <td class="year-col text-center">{{ $calonSiswa->tahun_lulus }}</td>
                                        <td class="status-col text-center">
                                            @if($calonSiswa->is_active)
                                                <span class="badge text-bg-success">Aktif</span>
                                            @else
                                                <span class="badge text-bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td class="action-col text-center">
                                            <button
                                                type="submit"
                                                form="toggleForm{{ $calonSiswa->id }}"
                                                class="btn btn-sm {{ $calonSiswa->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="{{ $calonSiswa->is_active ? 'Nonaktifkan' : 'Aktifkan' }} NISN {{ $calonSiswa->nisn }}?"
                                            >
                                                {{ $calonSiswa->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

                @foreach($whitelist as $calonSiswa)
                    <form id="toggleForm{{ $calonSiswa->id }}" method="post" action="{{ route('admin.pengaturan.whitelist.toggle', $calonSiswa) }}" class="d-none">
                        @csrf
                    </form>
                @endforeach
            </div>
        </section>

        <section class="tab-pane fade card shadow-sm" id="akses-pane" role="tabpanel" aria-labelledby="akses-tab" tabindex="0">
            <div class="card-header border-bottom-0">
                <h4 class="settings-section-title">Akses Sekolah</h4>
                <p class="settings-section-subtitle">Kelola izin akses fitur dan tombol aksi untuk panel Administrator Sekolah.</p>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.pengaturan.akses-sekolah') }}" class="row g-3">
                    @csrf
                    <div class="col-lg-8">
                        <div class="border rounded p-4 bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <div class="fs-2 text-primary" style="margin-top: -3px;">
                                    <i class="bi bi-shield-lock-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1">Aksi Penerimaan Murid</h5>
                                    <p class="text-muted small mb-3">Tentukan apakah admin sekolah berhak langsung menerima atau menolak pendaftaran calon murid di panel mereka.</p>
                                    
                                    <div class="form-check form-switch fs-6">
                                        <input type="hidden" name="tombol_terima_tolak_aktif" value="0">
                                        <input type="checkbox" name="tombol_terima_tolak_aktif" value="1" class="form-check-input" id="tombolTerimaTolakAktif" @checked((bool) (int) ($settings['tombol_terima_tolak_aktif'] ?? 0))>
                                        <label class="form-check-label fw-bold text-dark" for="tombolTerimaTolakAktif">
                                            Aktifkan Tombol Terima/Tolak Pendaftar
                                        </label>
                                    </div>
                                    <div class="small text-muted mt-3">
                                        <span class="badge text-bg-warning">Catatan</span> Jika opsi ini dinonaktifkan, status pendaftaran di dashboard sekolah hanya dapat dilihat, dan tombol aksi "Terima" atau "Tolak" akan berada dalam kondisi terkunci (tergembok).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary px-4 py-2" style="border-radius: 0.5rem;">Simpan Pengaturan Akses</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="tab-pane fade card shadow-sm" id="jam-pelayanan-pane" role="tabpanel" aria-labelledby="jam-pelayanan-tab" tabindex="0">
            <div class="card-header border-bottom-0">
                <h4 class="settings-section-title">Jam Pelayanan Pendaftaran</h4>
                <p class="settings-section-subtitle">Atur jam layanan calon murid untuk cek NISN, registrasi akun, pengisian formulir, dan kirim final.</p>
            </div>
            <div class="card-body">
                @php
                    $selectedServiceDays = collect(explode(',', (string) ($settings['jam_pelayanan_hari'] ?? '1,2,3,4,5,6,7')))
                        ->map(fn ($day) => (int) trim($day))
                        ->filter()
                        ->all();
                    $oldServiceDays = old('jam_pelayanan_hari');
                    $selectedServiceDays = is_array($oldServiceDays)
                        ? collect($oldServiceDays)->map(fn ($day) => (int) $day)->all()
                        : $selectedServiceDays;
                    $dayLabels = [
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ];
                @endphp
                <form method="post" action="{{ route('admin.pengaturan.jam-pelayanan') }}" class="row g-3">
                    @csrf
                    <div class="col-lg-8">
                        <div class="border rounded p-4 bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <div class="fs-2 text-primary" style="margin-top: -3px;">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1">Batasi Jam Pelayanan Calon Murid</h5>
                                    <p class="text-muted small mb-3">Jika aktif, calon murid hanya dapat membuat akun dan mengisi formulir pada hari dan jam yang ditentukan. Admin tetap dapat mengelola data.</p>

                                    <div class="form-check form-switch fs-6 mb-3">
                                        <input type="hidden" name="jam_pelayanan_aktif" value="0">
                                        <input type="checkbox" name="jam_pelayanan_aktif" value="1" class="form-check-input" id="jamPelayananAktif" @checked((bool) (int) ($settings['jam_pelayanan_aktif'] ?? 0))>
                                        <label class="form-check-label fw-bold text-dark" for="jamPelayananAktif">
                                            Aktifkan Pembatasan Jam Pelayanan
                                        </label>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Jam Mulai</label>
                                            <input type="time" name="jam_pelayanan_mulai" value="{{ old('jam_pelayanan_mulai', $settings['jam_pelayanan_mulai'] ?? '08:00') }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Jam Selesai</label>
                                            <input type="time" name="jam_pelayanan_selesai" value="{{ old('jam_pelayanan_selesai', $settings['jam_pelayanan_selesai'] ?? '14:00') }}" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Hari Pelayanan</label>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($dayLabels as $dayNumber => $dayLabel)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="jam_pelayanan_hari[]" value="{{ $dayNumber }}" id="jamPelayananHari{{ $dayNumber }}" @checked(in_array($dayNumber, $selectedServiceDays, true))>
                                                        <label class="form-check-label" for="jamPelayananHari{{ $dayNumber }}">{{ $dayLabel }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="form-text">Kosongkan semua hari untuk memakai setiap hari.</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Pesan Saat Layanan Tutup</label>
                                            <textarea name="jam_pelayanan_pesan_tutup" class="form-control" rows="3" maxlength="500">{{ old('jam_pelayanan_pesan_tutup', $settings['jam_pelayanan_pesan_tutup'] ?? '') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="small text-muted mt-3">
                                        <span class="badge text-bg-info">Zona waktu</span> Jam dihitung menggunakan WIT (Asia/Jayapura).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button class="btn btn-primary px-4 py-2" style="border-radius: 0.5rem;">Simpan Jam Pelayanan</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="tab-pane fade card shadow-sm" id="kontak-pane" role="tabpanel" aria-labelledby="kontak-tab" tabindex="0">
            <div class="card-header">
                <h4 class="settings-section-title">Kontak Panitia</h4>
                <p class="settings-section-subtitle">Kontak utama dipakai untuk tombol WhatsApp di landing page dan halaman daftar.</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($contacts as $contact)
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100 {{ $contact->is_primary ? 'primary-contact' : '' }}">
                                <form method="post" action="{{ route('admin.pengaturan.kontak.update', $contact) }}" class="row g-2">
                                    @csrf
                                    @method('put')
                                    <div class="col-md-6">
                                        <label class="form-label">Nama</label>
                                        <input type="text" name="nama" value="{{ old("kontak.$contact->id.nama", $contact->nama) }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Label</label>
                                        <input type="text" name="label" value="{{ old("kontak.$contact->id.label", $contact->label) }}" class="form-control">
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" name="nomor_whatsapp" value="{{ old("kontak.$contact->id.nomor_whatsapp", $contact->nomor_whatsapp) }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="kontakAktif{{ $contact->id }}" @checked($contact->is_active)>
                                            <label class="form-check-label" for="kontakAktif{{ $contact->id }}">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex flex-wrap gap-2">
                                        <button class="btn btn-outline-primary btn-sm">Simpan</button>
                                        @if(! $contact->is_primary)
                                            <button class="btn btn-outline-success btn-sm" type="submit" form="kontakUtama{{ $contact->id }}">Jadikan Utama</button>
                                        @else
                                            <span class="badge text-bg-success align-self-center">Kontak Utama</span>
                                        @endif
                                        <button class="btn btn-outline-danger btn-sm" type="submit" form="hapusKontak{{ $contact->id }}" data-confirm="Hapus kontak panitia ini?">Hapus</button>
                                    </div>
                                </form>
                                <form id="kontakUtama{{ $contact->id }}" method="post" action="{{ route('admin.pengaturan.kontak.primary', $contact) }}" class="d-none">@csrf</form>
                                <form id="hapusKontak{{ $contact->id }}" method="post" action="{{ route('admin.pengaturan.kontak.destroy', $contact) }}" class="d-none">
                                    @csrf
                                    @method('delete')
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr>

                <form method="post" action="{{ route('admin.pengaturan.kontak.store') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Nama Kontak</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" placeholder="Nama admin/petugas">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Label</label>
                        <input type="text" name="label" value="{{ old('label') }}" class="form-control" placeholder="Admin Pendaftaran">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nomor WhatsApp</label>
                        <input type="text" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" class="form-control" placeholder="62812...">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input type="checkbox" name="is_primary" value="1" class="form-check-input" id="kontakUtamaBaru">
                            <label class="form-check-label" for="kontakUtamaBaru">Jadikan utama</label>
                        </div>
                    </div>
                    <div class="col-md-1 d-grid">
                        <button class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabStorageKey = 'spmb-settings-active-tab';
            const storedTab = window.localStorage.getItem(tabStorageKey);
            const storedTabButton = storedTab ? document.querySelector(`[data-bs-target="${storedTab}"]`) : null;

            if (storedTabButton) {
                bootstrap.Tab.getOrCreateInstance(storedTabButton).show();
            }

            document.querySelectorAll('#settingsTabs [data-bs-toggle="tab"]').forEach(function (tabButton) {
                tabButton.addEventListener('shown.bs.tab', function (event) {
                    window.localStorage.setItem(tabStorageKey, event.target.dataset.bsTarget);
                });
            });

            document.querySelectorAll('#settingsTabContent form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    const pane = form.closest('.tab-pane');

                    if (pane) {
                        window.localStorage.setItem(tabStorageKey, `#${pane.id}`);
                    }
                });
            });

            const tableElement = document.getElementById('whitelistTable');

            if (! tableElement || ! window.DataTable) {
                return;
            }

            const table = new DataTable(tableElement, {
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[5, 'desc'], [3, 'asc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [0, 1, 9] },
                    { type: 'num', targets: [1, 5] },
                ],
                language: {
                    search: 'Cari calon siswa:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ calon siswa',
                    infoEmpty: 'Tidak ada calon siswa yang ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ total calon siswa)',
                    zeroRecords: 'Data calon siswa tidak ditemukan',
                    emptyTable: 'Belum ada data whitelist calon siswa.',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });

            const updateRowNumbers = function () {
                table.rows({ page: 'current' }).nodes().each(function (row, index) {
                    if (row.cells[1]) {
                        row.cells[1].textContent = table.page.info().start + index + 1;
                    }
                });
            };

            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const bulkDeactivateBtn = document.getElementById('bulkDeactivateBtn');

            const updateBulkButtonState = function () {
                const checkedCount = table.$('.select-row-checkbox:checked').length;
                if (bulkDeactivateBtn) {
                    bulkDeactivateBtn.disabled = checkedCount === 0;
                    if (checkedCount > 0) {
                        bulkDeactivateBtn.innerHTML = `<i class="bi bi-shield-slash-fill"></i> Nonaktifkan Terpilih (${checkedCount})`;
                    } else {
                        bulkDeactivateBtn.innerHTML = `<i class="bi bi-shield-slash-fill"></i> Nonaktifkan Terpilih`;
                    }
                }
            };

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    const isChecked = this.checked;
                    table.$('.select-row-checkbox').each(function () {
                        this.checked = isChecked;
                    });
                    updateBulkButtonState();
                });
            }

            tableElement.addEventListener('change', function (e) {
                if (e.target.classList.contains('select-row-checkbox')) {
                    updateBulkButtonState();

                    const totalCheckboxes = table.$('.select-row-checkbox').length;
                    const checkedCheckboxes = table.$('.select-row-checkbox:checked').length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = totalCheckboxes === checkedCheckboxes;
                        selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
                    }
                }
            });

            const bulkDeactivateForm = document.getElementById('bulkDeactivateForm');
            if (bulkDeactivateForm) {
                bulkDeactivateForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const checkedCheckboxes = table.$('.select-row-checkbox:checked');
                    if (checkedCheckboxes.length === 0) {
                        alert('Silakan pilih data yang ingin dinonaktifkan.');
                        return;
                    }

                    const confirmMsg = `Nonaktifkan ${checkedCheckboxes.length} calon siswa yang terpilih?`;
                    if (!confirm(confirmMsg)) {
                        return;
                    }

                    // Hapus input hidden yang ditambahkan sebelumnya
                    bulkDeactivateForm.querySelectorAll('input[name="selected_ids[]"]').forEach(el => el.remove());

                    // Tambahkan input hidden untuk semua baris yang dicentang lintas halaman
                    checkedCheckboxes.each(function () {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'selected_ids[]';
                        hiddenInput.value = this.value;
                        bulkDeactivateForm.appendChild(hiddenInput);
                    });

                    bulkDeactivateForm.submit();
                });
            }

            table.on('draw', function () {
                updateRowNumbers();
                const visibleCheckboxes = document.querySelectorAll('.select-row-checkbox');
                const visibleChecked = document.querySelectorAll('.select-row-checkbox:checked');
                if (selectAllCheckbox && visibleCheckboxes.length > 0) {
                    selectAllCheckbox.checked = visibleCheckboxes.length === visibleChecked.length;
                    selectAllCheckbox.indeterminate = visibleChecked.length > 0 && visibleChecked.length < visibleCheckboxes.length;
                }
            });

            updateRowNumbers();

            document.getElementById('whitelist-tab')?.addEventListener('shown.bs.tab', function () {
                table.columns.adjust();
            });
        });
    </script>

    <div class="modal fade" id="tambahCalonSiswaModal" tabindex="-1" aria-labelledby="tambahCalonSiswaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 0.75rem; border: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">
                <form method="post" action="{{ route('admin.pengaturan.whitelist.store') }}">
                    @csrf
                    <div class="modal-header border-bottom-0 px-4 pt-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="tambahCalonSiswaModalLabel" style="font-size: 1.15rem;">Tambah Calon Siswa Manual</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">NISN <span class="text-danger">*</span></label>
                                <input type="text" name="nisn" class="form-control" placeholder="10 digit NISN" pattern="[0-9]{10}" maxlength="10" required style="border-radius: 0.45rem;">
                                <div class="form-text small">Harus berupa 10 digit angka unik.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama lengkap calon siswa" maxlength="100" required style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small mb-1">Tempat Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir" maxlength="100" required style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small mb-1">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="text" name="tanggal_lahir" placeholder="dd-mm-yyyy" pattern="\d{2}-\d{2}-\d{4}" class="form-control" required style="border-radius: 0.45rem;" title="Format harus dd-mm-yyyy (Contoh: 15-05-2013)">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">Asal Sekolah <span class="text-danger">*</span></label>
                                <input type="text" name="asal_sekolah" class="form-control" placeholder="Nama SD/MI/Sederajat" maxlength="100" required style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small mb-1">Nilai TKA Matematika</label>
                                <input type="number" step="0.01" min="0" max="100" name="nilai_tka_matematika" class="form-control" placeholder="0.00 - 100.00" style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-muted small mb-1">Nilai TKA B. Indonesia</label>
                                <input type="number" step="0.01" min="0" max="100" name="nilai_tka_bahasa_indonesia" class="form-control" placeholder="0.00 - 100.00" style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">Tahun Lulus <span class="text-danger">*</span></label>
                                <input type="text" name="tahun_lulus" value="{{ $settings['tahun_pendaftaran'] }}" class="form-control" maxlength="4" required style="border-radius: 0.45rem;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 0.45rem;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 0.45rem;">Tambah Calon Siswa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importWhitelistModal" tabindex="-1" aria-labelledby="importWhitelistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0.75rem; border: 0; box-shadow: 0 10px 40px rgba(0,0,0,0.12);">
                <form method="post" action="{{ route('admin.pengaturan.whitelist.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom-0 px-4 pt-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark" id="importWhitelistModalLabel" style="font-size: 1.15rem;">Import Whitelist Calon Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">Tahun Lulus <span class="text-danger">*</span></label>
                                <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $settings['tahun_pendaftaran']) }}" class="form-control" maxlength="4" inputmode="numeric" required style="border-radius: 0.45rem;">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold text-muted small mb-1">File Whitelist <span class="text-danger">*</span></label>
                                <input type="file" name="calon_siswa_file" class="form-control" accept=".xlsx,.csv,.txt,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain" required style="border-radius: 0.45rem;">
                                <div class="form-text small mt-2">
                                    Format XLSX/CSV: <strong>NISN, Nama Siswa, Tempat Lahir, Tanggal Lahir, Asal Sekolah, Nilai Matematika, Nilai Bahasa Indonesia</strong>.
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="deactivate_missing_in_year" value="1" class="form-check-input" id="nonaktifTidakAdaCsv" checked>
                                    <label class="form-check-label small" for="nonaktifTidakAdaCsv">Nonaktifkan NISN pada tahun lulus yang sama jika tidak ada di file baru</label>
                                    <div class="form-text small">Data tahun lulus lain otomatis dinonaktifkan tetapi tidak dihapus. Siswa lama dapat diaktifkan kembali satu per satu.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-2 d-flex justify-content-between align-items-center">
                        <a href="{{ route('admin.pengaturan.whitelist.download-format') }}" class="btn btn-sm btn-outline-success fw-bold" style="border-radius: 0.45rem;">
                            <i class="bi bi-download"></i> Unduh Format
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 0.45rem;">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary px-4" style="border-radius: 0.45rem;">Import</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
