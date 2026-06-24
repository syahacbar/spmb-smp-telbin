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
            min-width: 130px;
        }
        .settings-table .narrow {
            min-width: 92px;
            width: 92px;
        }
        .settings-table .wide {
            min-width: 240px;
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
                <form method="post" action="{{ route('admin.pengaturan.whitelist.import') }}" enctype="multipart/form-data" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="text" name="tahun_lulus" value="{{ old('tahun_lulus', $settings['tahun_pendaftaran']) }}" class="form-control" maxlength="4" inputmode="numeric" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">File Whitelist</label>
                        <input type="file" name="calon_siswa_file" class="form-control" accept=".xlsx,.csv,.txt,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain" required>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button class="btn btn-primary">Import Whitelist</button>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted mb-2">Format XLSX/CSV: <strong>NISN, Nama Siswa, Tempat Lahir, Tanggal Lahir, Asal Sekolah, Nilai Matematika, Nilai Bahasa Indonesia</strong>.</div>
                        <div class="form-check">
                            <input type="checkbox" name="deactivate_missing_in_year" value="1" class="form-check-input" id="nonaktifTidakAdaCsv" checked>
                            <label class="form-check-label" for="nonaktifTidakAdaCsv">Nonaktifkan NISN pada tahun lulus yang sama jika tidak ada di file baru</label>
                            <div class="form-text">Data tahun lulus lain otomatis dinonaktifkan tetapi tidak dihapus. Siswa lama dapat diaktifkan kembali satu per satu.</div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="bulk-action-panel mb-4">
                    <form method="post" action="{{ route('admin.pengaturan.whitelist.deactivate') }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-lg-7">
                            <label class="form-label fw-bold">Penonaktifan Massal Berdasarkan Tahun Lulus</label>
                            <select name="tahun_lulus" class="form-select" required>
                                <option value="">Pilih tahun lulus</option>
                                @foreach($whitelistYears as $year)
                                    @php($stat = $whitelistStats->firstWhere('tahun_lulus', $year))
                                    <option value="{{ $year }}">
                                        {{ $year }} — {{ number_format((int) ($stat?->active_total ?? 0)) }} aktif dari {{ number_format((int) ($stat?->total ?? 0)) }} data
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Pengaktifan kembali siswa lama hanya dilakukan per NISN melalui tombol pada tabel.</div>
                        </div>
                        <div class="col-lg-5 d-grid">
                            <button class="btn btn-outline-danger" data-confirm="Nonaktifkan seluruh calon siswa aktif pada tahun yang dipilih?">Nonaktifkan</button>
                        </div>
                    </form>
                </div>

                <div class="whitelist-table-wrap">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle settings-table mb-0 w-100" id="whitelistTable" style="width: 100%">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th class="wide">Nama</th>
                                <th>Asal Sekolah</th>
                                <th>TKA Matematika</th>
                                <th>TKA Bahasa Indonesia</th>
                                <th>Tahun Lulus</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($whitelist as $calonSiswa)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $calonSiswa->nisn }}</td>
                                    <td>{{ $calonSiswa->nama }}</td>
                                    <td>{{ $calonSiswa->asal_sekolah }}</td>
                                    <td>{{ $calonSiswa->nilai_tka_matematika ?? '-' }}</td>
                                    <td>{{ $calonSiswa->nilai_tka_bahasa_indonesia ?? '-' }}</td>
                                    <td>{{ $calonSiswa->tahun_lulus }}</td>
                                    <td>
                                        @if($calonSiswa->is_active)
                                            <span class="badge text-bg-success">Aktif</span>
                                        @else
                                            <span class="badge text-bg-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="post" action="{{ route('admin.pengaturan.whitelist.toggle', $calonSiswa) }}">
                                            @csrf
                                            <button
                                                class="btn btn-sm {{ $calonSiswa->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-confirm="{{ $calonSiswa->is_active ? 'Nonaktifkan' : 'Aktifkan' }} NISN {{ $calonSiswa->nisn }}?"
                                            >
                                                {{ $calonSiswa->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
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
                order: [[4, 'desc'], [2, 'asc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: 0 },
                    { type: 'num', targets: [0, 4] },
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
                    row.cells[0].textContent = table.page.info().start + index + 1;
                });
            };

            table.on('draw', updateRowNumbers);
            updateRowNumbers();

            document.getElementById('whitelist-tab')?.addEventListener('shown.bs.tab', function () {
                table.columns.adjust();
            });
        });
    </script>
</x-layouts.app>
