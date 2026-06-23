<x-layouts.app :pengguna="$pengguna" title="Sekolah dan Zonasi">
    <style>
        /* Custom Select2 Overrides to look premium and match Bintuni green theme */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #cbd5e1;
            border-radius: 0.5rem;
            min-height: 42px;
            padding: 0.25rem 0.5rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection {
            border-color: #0b5d4b !important;
            box-shadow: 0 0 0 0.25rem rgba(11, 93, 75, 0.15) !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #e6f3f0 !important;
            color: #0b5d4b !important;
            border: 1px solid #a3d1c6 !important;
            border-radius: 0.375rem !important;
            padding: 2px 8px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            margin-top: 4px !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #0b5d4b !important;
            border-right: 1px solid #a3d1c6 !important;
            margin-right: 6px !important;
            padding-right: 4px !important;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            background-color: #d1eae4 !important;
            color: #063c30 !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #cbd5e1;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            z-index: 9999;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-search__field {
            border-radius: 0.375rem;
            border-color: #cbd5e1;
        }
        .select2-container--bootstrap-5 .select2-dropdown .select2-search__field:focus {
            border-color: #0b5d4b !important;
            box-shadow: 0 0 0 0.2rem rgba(11, 93, 75, 0.15) !important;
            outline: 0;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: #0b5d4b !important;
            color: #ffffff !important;
        }
        .select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
            background-color: #e6f3f0 !important;
            color: #0b5d4b !important;
        }
        .select2-container {
            display: block;
        }
        .zonasi-badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            max-height: 100px;
            overflow-y: auto;
            padding: 4px;
        }
        .zonasi-badge {
            font-size: 0.72rem;
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 500;
        }
    </style>

    <div class="page-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold mb-1">Data Sekolah dan Zonasi</h3>
            <div class="text-muted">Kelola SMP tujuan dan cakupan kelurahan untuk Jalur Domisili.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-outline-primary fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#importCsvModal">
                <i class="bi bi-upload"></i> Import CSV
            </button>
            <button class="btn btn-primary fw-bold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                <i class="bi bi-plus-lg"></i> Tambah Sekolah
            </button>
        </div>
    </div>

    @include('partials.flash')

    <div class="card border-0 shadow-sm" style="border-radius: 1rem; border: 1px solid #e2e8f0;">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="sekolahTable" class="table table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 90px;">NPSN</th>
                            <th>Nama Sekolah</th>
                            <th style="width: 100px;">Status</th>
                            <th>Alamat & Lokasi</th>
                            <th>Cakupan Wilayah Zonasi</th>
                            <th style="width: 100px;" class="text-center">Keaktifan</th>
                            <th style="width: 180px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sekolahs as $sekolah)
                            @php
                                $sekolahZonasiIds = $zonasiBySchool[$sekolah->id] ?? [];
                                $sekolahZonasiNames = [];
                                foreach ($sekolahZonasiIds as $zId) {
                                    $v = $kelurahans->firstWhere('id', $zId);
                                    if ($v) {
                                        $sekolahZonasiNames[] = $v->nama;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-semibold">{{ $loop->iteration }}</td>
                                <td><code class="fw-bold text-dark">{{ $sekolah->npsn ?: '-' }}</code></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sekolah->nama }}</div>
                                    <div class="small text-muted" style="font-size: 0.76rem;">
                                        <i class="bi bi-person-fill-gear"></i> {{ $sekolah->admin_count }} admin sekolah
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $sekolah->status === 'negeri' ? 'text-bg-success' : 'text-bg-secondary' }} px-2.5 py-1.5 fw-bold" style="font-size: 0.72rem; text-transform: uppercase;">
                                        {{ $sekolah->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small text-dark text-truncate" style="max-width: 250px;" title="{{ $sekolah->alamat }}">
                                        {{ $sekolah->alamat ?: '-' }}
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.76rem;">
                                        {{ $kelurahans->firstWhere('id', $sekolah->kelurahan_id)?->nama ?? '-' }}, 
                                        {{ $kecamatans->firstWhere('id', $sekolah->kecamatan_id)?->nama ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    @if(count($sekolahZonasiNames) > 0)
                                        <div class="zonasi-badge-container">
                                            @foreach($sekolahZonasiNames as $zName)
                                                <span class="zonasi-badge">{{ $zName }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted small italic">Belum ada cakupan zonasi</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $sekolah->is_active ? 'text-bg-success' : 'text-bg-secondary' }} px-2 py-1" style="font-size: 0.72rem;">
                                        {{ $sekolah->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                        <form action="{{ route('admin.sekolah.toggle-active', $sekolah) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status keaktifan sekolah {{ $sekolah->nama }}?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $sekolah->is_active ? 'btn-outline-secondary' : 'btn-success' }} px-2 fw-semibold" title="{{ $sekolah->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" style="font-size: 0.75rem;">
                                                <i class="bi {{ $sekolah->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i> {{ $sekolah->is_active ? 'Nonaktif' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-outline-primary px-2.5 fw-bold" data-bs-toggle="modal" data-bs-target="#editSchoolModal-{{ $sekolah->id }}" style="font-size: 0.75rem;">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>

                                        <form id="hapus-sekolah-{{ $sekolah->id }}" method="post" action="{{ route('admin.sekolah.destroy', $sekolah) }}" class="d-inline" onsubmit="return confirm('Hapus sekolah {{ $sekolah->nama }}? Relasi zonasi dan akun sekolah yang tidak lagi digunakan akan ikut dibersihkan.')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 fw-semibold" style="font-size: 0.75rem;">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Sekolah -->
    <div class="modal fade" id="addSchoolModal" aria-labelledby="addSchoolModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 20px 50px rgba(16, 24, 40, 0.15);">
                <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold" id="addSchoolModalLabel">Tambah Sekolah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="post" action="{{ route('admin.sekolah.store') }}">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">NPSN</label>
                                <input name="npsn" class="form-control" placeholder="Nomor Pokok Sekolah Nasional" maxlength="20">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Nama Sekolah</label>
                                <input name="nama" class="form-control" placeholder="Nama lengkap sekolah" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="negeri">Negeri</option>
                                    <option value="swasta">Swasta</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-muted">Distrik (Kecamatan)</label>
                                <select name="kecamatan_id" class="form-select" data-school-district required>
                                    <option value="">Pilih distrik</option>
                                    @foreach($kecamatans as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Kelurahan/Kampung Sekolah</label>
                                <select name="kelurahan_id" class="form-select" data-school-village required>
                                    <option value="">Pilih kampung (pilih distrik terlebih dahulu)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Alamat Sekolah</label>
                                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat jalan lengkap sekolah"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Telepon</label>
                                <input name="telepon" class="form-control" placeholder="Nomor telepon sekolah" maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="alamat@sekolah.sch.id">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Kelurahan/Kampung dalam Zonasi (Jalur Domisili)</label>
                                <select name="kelurahan_ids[]" class="form-select zone-select-modal" multiple data-placeholder="Pilih nama kelurahan zonasi...">
                                    @foreach($kelurahans as $kelurahan)
                                        <option value="{{ $kelurahan->id }}">
                                            {{ $kelurahan->nama }} - {{ $kelurahan->nama_distrik }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Siswa yang tinggal di wilayah cakupan ini berhak memilih sekolah ini di Jalur Domisili.</div>
                            </div>

                            <div class="col-12 mt-4"><hr class="my-1"></div>
                            <div class="col-12">
                                <div class="fw-bold text-dark">Akun Login Admin Sekolah</div>
                                <div class="small text-muted mb-2">Akun ini otomatis dibuat dan dihubungkan ke sekolah baru dengan role Admin Sekolah.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Username Admin</label>
                                <input name="username" class="form-control" placeholder="Username untuk login" required autocomplete="username">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Password Admin</label>
                                <input type="password" name="password" class="form-control" placeholder="Min. 12 karakter" minlength="12" required autocomplete="new-password">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Minimal 12 karakter alfanumerik.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 0.5rem;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 0.5rem;">Simpan Sekolah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div class="modal fade" id="importCsvModal" aria-labelledby="importCsvModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 20px 50px rgba(16, 24, 40, 0.15);">
                <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold" id="importCsvModalLabel">Import Sekolah & Zonasi dari CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form method="post" action="{{ route('admin.sekolah-zonasi.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-3">
                        <p class="small text-muted mb-3">Kolom CSV harus berurutan dengan format:<br>
                            <code>npsn,nama_sekolah,status,kecamatan,kelurahan_sekolah,alamat,telepon,email,zonasi_kelurahan</code>
                            <br><span class="text-danger">*</span> Pisahkan beberapa kampung/kelurahan zonasi dengan tanda titik koma (<code>;</code>).
                        </p>
                        <div class="d-grid mb-3">
                            <a href="{{ asset('templates/import-sekolah-zonasi.csv') }}" class="btn btn-sm btn-outline-secondary fw-bold" download>
                                <i class="bi bi-download"></i> Unduh Template CSV
                            </a>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted">File CSV</label>
                            <input type="file" name="file_import" class="form-control" accept=".csv,.txt" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 0.5rem;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 0.5rem;">Proses Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals Edit Sekolah (Satu modal per sekolah) -->
    @foreach($sekolahs as $sekolah)
        <div class="modal fade" id="editSchoolModal-{{ $sekolah->id }}" aria-labelledby="editSchoolModalLabel-{{ $sekolah->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" style="border-radius: 1rem; border: none; box-shadow: 0 20px 50px rgba(16, 24, 40, 0.15);">
                    <div class="modal-header border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="modal-title fw-bold" id="editSchoolModalLabel-{{ $sekolah->id }}">Edit Sekolah & Zonasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form method="post" action="{{ route('admin.sekolah.update', $sekolah) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body px-4 py-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">NPSN</label>
                                    <input name="npsn" class="form-control" value="{{ old('npsn', $sekolah->npsn) }}" placeholder="NPSN Sekolah" maxlength="20">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small text-muted">Nama Sekolah</label>
                                    <input name="nama" class="form-control" value="{{ old('nama', $sekolah->nama) }}" placeholder="Nama sekolah" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small text-muted">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="negeri" @selected($sekolah->status === 'negeri')>Negeri</option>
                                        <option value="swasta" @selected($sekolah->status === 'swasta')>Swasta</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fw-bold small text-muted">Distrik (Kecamatan)</label>
                                    <select name="kecamatan_id" class="form-select edit-school-district-select" data-school-id="{{ $sekolah->id }}" required>
                                        <option value="">Pilih distrik</option>
                                        @foreach($kecamatans as $item)
                                            <option value="{{ $item->id }}" @selected($item->id == $sekolah->kecamatan_id)>{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Kelurahan/Kampung Sekolah</label>
                                    <select name="kelurahan_id" class="form-select edit-school-village-select" data-school-id="{{ $sekolah->id }}" required>
                                        @foreach($kelurahans as $item)
                                            @if($item->kecamatan_id == $sekolah->kecamatan_id)
                                                <option value="{{ $item->id }}" @selected($item->id == $sekolah->kelurahan_id)>{{ $item->nama }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Alamat Sekolah</label>
                                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('alamat', $sekolah->alamat) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Telepon</label>
                                    <input name="telepon" class="form-control" value="{{ old('telepon', $sekolah->telepon) }}" placeholder="Nomor telepon" maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $sekolah->email) }}" placeholder="Email sekolah">
                                </div>

                                <div class="col-12 mt-4"><hr class="my-1"></div>
                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Kelurahan/Kampung dalam Zonasi (Jalur Domisili)</label>
                                    <select name="kelurahan_ids[]" class="form-select zone-select-modal" multiple data-placeholder="Pilih nama kelurahan zonasi...">
                                        @foreach($kelurahans as $kelurahan)
                                            <option value="{{ $kelurahan->id }}" @selected(in_array($kelurahan->id, $zonasiBySchool[$sekolah->id] ?? []))>
                                                {{ $kelurahan->nama }} - {{ $kelurahan->nama_distrik }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted" style="font-size: 0.72rem;">Siswa yang tinggal di wilayah cakupan ini berhak memilih sekolah ini di Jalur Domisili.</div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 px-4 pb-4 pt-0 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 0.5rem;">Batal</button>
                            <button type="submit" class="btn btn-primary px-4" style="border-radius: 0.5rem;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data kelurahan dari backend
            const villages = @json($kelurahans);

            // Inisialisasi DataTable
            const tableEl = document.getElementById('sekolahTable');
            if (tableEl && window.DataTable) {
                new DataTable(tableEl, {
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    order: [[2, 'asc']], // Urut berdasarkan Nama Sekolah
                    columnDefs: [
                        { orderable: false, searchable: false, targets: [0, 5, 7] }, // No, Zonasi, Aksi tidak diurut
                    ],
                    language: {
                        search: 'Cari Sekolah:',
                        lengthMenu: 'Tampilkan _MENU_ data',
                        info: 'Menampilkan _START_–_END_ dari _TOTAL_ sekolah',
                        infoEmpty: 'Tidak ada data',
                        infoFiltered: '(difilter dari _MAX_ total)',
                        zeroRecords: 'Tidak ada sekolah yang sesuai pencarian',
                        emptyTable: 'Belum ada data sekolah',
                        paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' },
                    },
                });
            }

            // Dropdown kampung dinamis di form Tambah Sekolah
            const district = document.querySelector('[data-school-district]');
            const village = document.querySelector('[data-school-village]');
            function fillVillages() {
                if (!village) return;
                village.innerHTML = '<option value="">Pilih kampung</option>';
                const districtVal = district.value;
                if (districtVal) {
                    villages.filter(item => String(item.kecamatan_id) === String(districtVal)).forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.nama;
                        village.appendChild(option);
                    });
                }
            }
            district?.addEventListener('change', fillVillages);

            // Dropdown kampung dinamis di form Edit Sekolah
            $(document).on('change', '.edit-school-district-select', function () {
                const schoolId = $(this).data('school-id');
                const districtId = $(this).val();
                const villageSelect = $(`.edit-school-village-select[data-school-id="${schoolId}"]`);
                
                villageSelect.html('<option value="">Pilih kampung</option>');
                
                if (districtId) {
                    const filtered = villages.filter(item => String(item.kecamatan_id) === String(districtId));
                    filtered.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.nama;
                        villageSelect.append(option);
                    });
                }
            });

            // Inisialisasi Select2 di dalam modal saat modal ditampilkan (mencegah bug dropdown Parent)
            $(document).on('shown.bs.modal', '.modal', function () {
                const modalId = $(this).attr('id');
                $(this).find('.zone-select-modal').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    dropdownParent: $('#' + modalId),
                    closeOnSelect: false
                });
            });
        });
    </script>
</x-layouts.app>
