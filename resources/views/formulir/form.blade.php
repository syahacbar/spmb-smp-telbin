<x-layouts.app :pengguna="$pengguna" title="Formulir Registrasi">
    @php
        $isEdit = $formulir->exists;
        $pekerjaanAyah = ['PNS', 'TNI/Polri', 'Pedagang', 'Petani', 'Nelayan', 'Buruh Bangunan', 'Kontraktor', 'Pegawai Swasta', 'Wiraswasta', 'Lainnya', 'Tidak Ada Pekerjaan'];
        $pekerjaanIbu = ['PNS', 'Ibu Rumah Tangga', 'Pedagang', 'Petani', 'Pegawai Swasta', 'Wiraswasta',  'Lainnya', 'Tidak Ada'];
        $tanggalLahirMaksimal = now()->subYears(13)->format('Y-m-d');
        $tanggalLahirMinimal = now()->subYears(21)->format('Y-m-d');
        $nomorHpAkun = $akunPendaftar->telpon ?? $formulir->hp ?? '';
        $emailAkun = $akunPendaftar->email ?? '';
        $hasMasterIdentity = isset($calonSiswa) && $calonSiswa;
        $selectedKecamatan = old('alamat_kecamatan', $formulir->alamat_kecamatan);
        $selectedKelurahan = old('alamat_kelurahan', $formulir->alamat_kelurahan);
        $parentAddressSame = $errors->any()
            ? (bool) old('alamat_ortu_sama_dengan_siswa')
            : (bool) $formulir->alamat_ortu_sama_dengan_siswa;
        $selectedProvinsiOrtu = old('alamat_ortu_provinsi', $formulir->alamat_ortu_provinsi);
        $selectedKabupatenOrtu = old('alamat_ortu_kabupaten', $formulir->alamat_ortu_kabupaten);
        $selectedKecamatanOrtu = old('alamat_ortu_kecamatan', $formulir->alamat_ortu_kecamatan);
        $selectedKelurahanOrtu = old('alamat_ortu_kelurahan', $formulir->alamat_ortu_kelurahan);
        $kelurahanOptionsByKecamatan = $kelurahanOptionsByKecamatan ?? [];
        $sekolahAsalOptions = $sekolahAsalOptions ?? [];
        $wilayahProvinsiOptions = $wilayahProvinsiOptions ?? [];
        $wilayahOptions = $wilayahOptions ?? [];
        $documentGuides = [
            'surat_keterangan_lulus' => [
                'label' => 'Ijazah / SKL',
                'description' => 'Ijazah SD/Sederajat atau Surat Keterangan Lulus.',
                'hint' => 'Format PDF, JPG, JPEG, PNG, atau WEBP, maksimal 1 MB. Tulisan harus jelas dan terbaca.',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp',
            ],
            'kartu_keluarga' => [
                'label' => 'Kartu Keluarga',
                'description' => 'Kartu Keluarga hasil pindai/scan.',
                'hint' => 'Format PDF, JPG, JPEG, PNG, atau WEBP, maksimal 1 MB. Tulisan harus jelas dan terbaca.',
                'accept' => '.pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp',
            ],
            'foto_selfie' => [
                'label' => 'Pas Foto',
                'description' => 'Pas foto ukuran 3x4 dengan latar belakang biru.',
                'hint' => 'Format *.jpg, *.jpeg, atau *.png, maksimal 1 MB. Wajah harus terlihat jelas.',
                'accept' => '.jpg,.jpeg,.png,image/jpeg,image/png',
            ],
        ];
    @endphp

    <div class="page-title">
        <div>
            <h3 class="fw-bold">{{ $isEdit ? 'Edit Formulir Registrasi' : 'Formulir Registrasi' }}</h3>
            <div class="text-muted">Lengkapi biodata dan berkas dasar. Pemilihan jalur serta sekolah dilakukan pada tahap berikutnya.</div>
        </div>
    </div>

    @if(! $formulir->isSubmitted())
        <div class="alert alert-info">
            Data yang disimpan akan masuk ke halaman pemeriksaan sebelum dikirim final.
        </div>
    @endif

    @if($hasMasterIdentity)
        <div class="alert alert-success">
            Identitas utama calon peserta didik diambil dari database resmi calon siswa Kabupaten Teluk Bintuni.
        </div>
    @endif

    <form method="post" action="{{ $formAction ?? ($isEdit ? route('formulir.update', $formulir) : route('formulir.store')) }}" enctype="multipart/form-data" data-registration-form novalidate>
        @csrf
        @if($isEdit)
            @method('put')
        @endif

        <div class="registration-shell">
            <aside class="registration-nav" aria-label="Navigasi bagian formulir">
                <a href="#data-diri" class="registration-nav-link">
                    <span>1</span>
                    <div>
                        <strong>Data Diri</strong>
                        <small>Identitas calon peserta didik</small>
                    </div>
                </a>
                <a href="#data-orang-tua" class="registration-nav-link">
                    <span>2</span>
                    <div>
                        <strong>Orang Tua / Wali</strong>
                        <small>Kontak keluarga</small>
                    </div>
                </a>
                <a href="#upload-dokumen" class="registration-nav-link">
                    <span>3</span>
                    <div>
                        <strong>Unggah Dokumen</strong>
                        <small>Berkas persyaratan</small>
                    </div>
                </a>
            </aside>

            <div class="registration-content">
                <section id="data-diri" class="card shadow-sm mb-3 form-section">
                    <div class="card-header">
                        <span class="section-number">1</span>
                        <div>
                            <div class="fw-bold">Data Diri Calon Peserta Didik</div>
                            <div class="small text-muted">Identitas utama calon peserta didik.</div>
                        </div>
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NISN</label>
                            <input class="form-control" value="{{ $formulir->nisn ?: $pengguna->id_pengguna }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input name="nama" value="{{ $hasMasterIdentity ? $calonSiswa->nama : old('nama', $formulir->nama) }}" class="form-control" data-field-label="Nama lengkap" @disabled($hasMasterIdentity) required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tempat Lahir</label>
                            <input name="tempat_lahir" value="{{ $hasMasterIdentity ? $calonSiswa->tempat_lahir : old('tempat_lahir', $formulir->tempat_lahir) }}" class="form-control" data-field-label="Tempat lahir" @disabled($hasMasterIdentity) required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ $hasMasterIdentity ? $calonSiswa->tanggal_lahir->format('Y-m-d') : old('tanggal_lahir', optional($formulir->tanggal_lahir)->format('Y-m-d') ?: $formulir->tanggal_lahir) }}" class="form-control" @unless($hasMasterIdentity) min="{{ $tanggalLahirMinimal }}" max="{{ $tanggalLahirMaksimal }}" @endunless data-field-label="Tanggal lahir" @disabled($hasMasterIdentity) data-open-date-on-focus required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Aktif <span class="text-muted fw-normal">(opsional)</span></label>
                            <input type="email" name="email" value="{{ old('email', $emailAkun) }}" class="form-control" autocomplete="email">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor HP/WA</label>
                            <input value="{{ $nomorHpAkun }}" class="form-control" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Asal Sekolah</label>
                            <input name="asal_sekolah" value="{{ $hasMasterIdentity ? $calonSiswa->asal_sekolah : old('asal_sekolah', $formulir->asal_sekolah) }}" class="form-control" data-field-label="Asal sekolah" data-school-input data-school-list="asal-sekolah-options" autocomplete="off" @disabled($hasMasterIdentity) required>
                            <datalist id="asal-sekolah-options">
                                @foreach($sekolahAsalOptions as $option)
                                    <option value="{{ $option }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIK</label>
                            <input name="nik" value="{{ old('nik', $formulir->nik) }}" class="form-control" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" data-field-label="NIK" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" data-field-label="Jenis kelamin" required>
                                <option value="">--Pilih salah satu--</option>
                                @foreach(['Laki-laki', 'Perempuan'] as $option)
                                    <option value="{{ $option }}" @selected(old('jenis_kelamin', $formulir->jenis_kelamin) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Agama</label>
                            <select name="agama" class="form-select" data-field-label="Agama" required>
                                <option value="">--Pilih salah satu--</option>
                                @foreach(['Islam', 'Kristen Protestan', 'Kristen Katholik', 'Hindu', 'Budha'] as $option)
                                    <option value="{{ $option }}" @selected(old('agama', $formulir->agama) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="address-group">
                                <div class="fw-bold mb-2">Tempat Tinggal / Domisili</div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Kabupaten</label>
                                        <input class="form-control" value="Teluk Bintuni" disabled>
                                        <input type="hidden" name="alamat_kabupaten" value="Teluk Bintuni">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kecamatan/Distrik</label>
                                        <select name="alamat_kecamatan" class="form-select" data-field-label="Kecamatan domisili" data-kecamatan-select required>
                                            <option value="">--Pilih salah satu--</option>
                                            @foreach($kecamatanOptions as $option)
                                                <option value="{{ $option }}" @selected($selectedKecamatan === $option)>{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kelurahan/Desa</label>
                                        <select name="alamat_kelurahan" class="form-select" data-field-label="Kelurahan/desa domisili" data-kelurahan-select data-selected-kelurahan="{{ $selectedKelurahan }}" required>
                                            <option value="">--Pilih salah satu--</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat (Nama jalan, nomor rumah, RT/RW, kompleks, dll.)</label>
                                        <textarea name="alamat" class="form-control" rows="2" data-field-label="Detail alamat domisili" placeholder="Contoh: Jl. ABC Nomor 123 RT 001 RW 002, Kompleks ABC, dan seterusnya" required>{{ old('alamat', $formulir->alamat) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="data-orang-tua" class="card shadow-sm mb-3 form-section">
                    <div class="card-header">
                        <span class="section-number">2</span>
                        <div>
                            <div class="fw-bold">Data Orang Tua / Wali</div>
                            <div class="small text-muted">Data kontak dan alamat keluarga.</div>
                        </div>
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Ayah</label>
                            <input name="nama_ayah" value="{{ old('nama_ayah', $formulir->nama_ayah) }}" class="form-control" data-field-label="Nama ayah" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan Ayah</label>
                            <select name="pekerjaan_ayah" class="form-select" data-field-label="Pekerjaan ayah" required>
                                <option value="">--Pilih salah satu--</option>
                                @foreach($pekerjaanAyah as $option)
                                    <option value="{{ $option }}" @selected(old('pekerjaan_ayah', $formulir->pekerjaan_ayah) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Ibu</label>
                            <input name="nama_ibu" value="{{ old('nama_ibu', $formulir->nama_ibu) }}" class="form-control" data-field-label="Nama ibu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pekerjaan Ibu</label>
                            <select name="pekerjaan_ibu" class="form-select" data-field-label="Pekerjaan ibu" required>
                                <option value="">--Pilih salah satu--</option>
                                @foreach($pekerjaanIbu as $option)
                                    <option value="{{ $option }}" @selected(old('pekerjaan_ibu', $formulir->pekerjaan_ibu) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No HP / WA Ortu</label>
                            <input name="hp_ortu" value="{{ old('hp_ortu', $formulir->hp_ortu) }}" class="form-control" inputmode="tel" data-field-label="No HP / WA orang tua" required>
                        </div>
                        <div class="col-md-12">
                            <div class="address-group">
                                <div class="mb-3">
                                    <div class="fw-bold">Alamat Orang Tua / Wali</div>
                                </div>
                                <div class="same-address-callout mb-3">
                                    <div class="form-check mb-0">
                                        <input type="checkbox" name="alamat_ortu_sama_dengan_siswa" value="1" id="alamat-ortu-sama" class="form-check-input" data-parent-same-address @checked($parentAddressSame)>
                                        <label class="form-check-label fw-bold" for="alamat-ortu-sama">Alamat orang tua / wali sama dengan alamat calon peserta didik</label>
                                    </div>
                                    <div class="small text-muted">Centang bagian ini jika alamat orang tua/wali mengikuti data domisili calon peserta didik.</div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Provinsi</label>
                                        <input name="alamat_ortu_provinsi" value="{{ $selectedProvinsiOrtu }}" class="form-control" list="alamat-ortu-provinsi-options" data-field-label="Provinsi orang tua/wali" data-parent-provinsi autocomplete="off" placeholder="Pilih atau ketik provinsi" required>
                                        <datalist id="alamat-ortu-provinsi-options">
                                            @foreach($wilayahProvinsiOptions as $option)
                                                <option value="{{ $option }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kabupaten</label>
                                        <input name="alamat_ortu_kabupaten" value="{{ $selectedKabupatenOrtu }}" class="form-control" list="alamat-ortu-kabupaten-options" data-field-label="Kabupaten orang tua/wali" data-parent-kabupaten autocomplete="off" placeholder="Pilih atau ketik kabupaten" required>
                                        <datalist id="alamat-ortu-kabupaten-options"></datalist>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kecamatan/Distrik</label>
                                        <input name="alamat_ortu_kecamatan" value="{{ $selectedKecamatanOrtu }}" class="form-control" list="alamat-ortu-kecamatan-options" data-field-label="Kecamatan orang tua/wali" data-parent-kecamatan autocomplete="off" placeholder="Pilih atau ketik kecamatan/distrik" required>
                                        <datalist id="alamat-ortu-kecamatan-options"></datalist>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kelurahan/Desa</label>
                                        <input name="alamat_ortu_kelurahan" value="{{ $selectedKelurahanOrtu }}" class="form-control" list="alamat-ortu-kelurahan-options" data-field-label="Kelurahan/desa orang tua/wali" data-parent-kelurahan autocomplete="off" placeholder="Pilih atau ketik kelurahan/desa" required>
                                        <datalist id="alamat-ortu-kelurahan-options"></datalist>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label">Alamat (Nama jalan, nomor rumah, RT/RW, kompleks, dll.)</label>
                                        <textarea name="alamat_ortu" class="form-control" rows="2" data-field-label="Detail alamat orang tua/wali" data-parent-detail-address placeholder="Contoh: Jl. ABC Nomor 123 RT 001 RW 002, Kompleks ABC, dan seterusnya" required>{{ old('alamat_ortu', $formulir->alamat_ortu) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="upload-dokumen" class="card shadow-sm mb-3 form-section">
                    <div class="card-header">
                        <span class="section-number">3</span>
                        <div>
                            <div class="fw-bold">Unggah Dokumen</div>
                            <div class="small text-muted">Siapkan file hasil scan sesuai ketentuan juknis.</div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row g-3">
                            @foreach($documentGuides as $field => $document)
                                <div class="col-xl-4 col-md-6">
                                    <div class="upload-box upload-box-modern">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                            <div>
                                                <label class="form-label fw-bold mb-1">{{ $document['label'] }}</label>
                                                <div class="small text-muted">{{ $document['description'] }}</div>
                                            </div>
                                            <span class="badge text-bg-primary">Max 1 MB</span>
                                        </div>

                                        @if($isEdit && $formulir->{$field})
                                            <div class="uploaded-file mb-2">
                                                <div class="fw-bold small">Berkas saat ini</div>
                                                <a href="{{ $formulir->berkasUrl($field) }}"
                                                   class="small text-decoration-none"
                                                   data-document-preview
                                                   data-document-title="{{ $document['label'] }}"
                                                   data-document-type="{{ $formulir->berkasIsImage($field) ? 'image' : 'pdf' }}"
                                                   data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">Lihat berkas</a>
                                            </div>
                                            <div class="small text-muted mb-2">Kosongkan jika tidak ingin mengganti berkas.</div>
                                        @endif

                                        <div class="input-group upload-file-control">
                                            <label for="upload-{{ $field }}" class="btn btn-outline-primary">Pilih file</label>
                                            <span class="form-control upload-file-name" id="upload-name-{{ $field }}" data-empty-file-text="Belum ada file dipilih">Belum ada file dipilih</span>
                                            <input type="file" id="upload-{{ $field }}" name="{{ $field }}" class="visually-hidden" accept="{{ $document['accept'] }}" data-file-validation="{{ $field === 'foto_selfie' ? 'image' : 'document' }}" data-field-label="{{ $document['label'] }}" data-max-file-size="1048576" data-file-name-target="upload-name-{{ $field }}" @required(! $isEdit)>
                                        </div>
                                        <div class="document-hint mt-2">{{ $document['hint'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="sticky-actions d-flex flex-column flex-sm-row gap-2 mb-4">
            <button class="btn btn-primary">{{ $isEdit ? 'Simpan Perubahan dan Periksa' : 'Simpan dan Periksa' }}</button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-registration-form]');

            if (! form) {
                return;
            }

            const kelurahanByKecamatan = @json($kelurahanOptionsByKecamatan);
            const wilayahOptions = @json($wilayahOptions);
            const schoolInput = form.querySelector('[data-school-input]');
            const parentSameAddress = form.querySelector('[data-parent-same-address]');
            const parentProvinsi = form.querySelector('[data-parent-provinsi]');
            const parentKabupaten = form.querySelector('[data-parent-kabupaten]');
            const parentKecamatan = form.querySelector('[data-parent-kecamatan]');
            const parentKelurahan = form.querySelector('[data-parent-kelurahan]');
            const parentDetailAddress = form.querySelector('[data-parent-detail-address]');
            const domisiliKecamatan = form.querySelector('[name="alamat_kecamatan"]');
            const domisiliKelurahan = form.querySelector('[name="alamat_kelurahan"]');
            const domisiliDetailAddress = form.querySelector('[name="alamat"]');

            form.querySelectorAll('[data-kecamatan-select]').forEach(function (kecamatanSelect) {
                const addressGroup = kecamatanSelect.closest('.address-group');
                const kelurahanSelect = addressGroup ? addressGroup.querySelector('[data-kelurahan-select]') : null;

                if (! kelurahanSelect) {
                    return;
                }

                const fillKelurahan = function () {
                    const selectedKecamatan = kecamatanSelect.value;
                    const selectedKelurahan = kelurahanSelect.dataset.selectedKelurahan || kelurahanSelect.value;
                    const kelurahanList = kelurahanByKecamatan[selectedKecamatan] || [];

                    kelurahanSelect.innerHTML = '<option value="">--Pilih salah satu--</option>';

                    kelurahanList.forEach(function (kelurahan) {
                        const option = document.createElement('option');
                        option.value = kelurahan;
                        option.textContent = kelurahan;
                        option.selected = selectedKelurahan === kelurahan;
                        kelurahanSelect.appendChild(option);
                    });

                    kelurahanSelect.dataset.selectedKelurahan = '';
                };

                kecamatanSelect.addEventListener('change', function () {
                    kelurahanSelect.dataset.selectedKelurahan = '';
                    fillKelurahan();
                });

                fillKelurahan();
            });

            const fillDatalist = function (input, options) {
                if (! input || ! input.list) {
                    return;
                }

                input.list.innerHTML = '';

                options.forEach(function (value) {
                    const option = document.createElement('option');
                    option.value = value;
                    input.list.appendChild(option);
                });
            };

            const fillParentKabupaten = function (selectedKabupaten) {
                const provinsi = parentProvinsi ? parentProvinsi.value : '';
                const options = Object.keys(wilayahOptions[provinsi] || {});
                fillDatalist(parentKabupaten, options);

                if (selectedKabupaten !== undefined && parentKabupaten) {
                    parentKabupaten.value = selectedKabupaten;
                }
            };

            const fillParentKecamatan = function (selectedKecamatan) {
                const provinsi = parentProvinsi ? parentProvinsi.value : '';
                const kabupaten = parentKabupaten ? parentKabupaten.value : '';
                const options = Object.keys(wilayahOptions[provinsi]?.[kabupaten] || {});
                fillDatalist(parentKecamatan, options);

                if (selectedKecamatan !== undefined && parentKecamatan) {
                    parentKecamatan.value = selectedKecamatan;
                }
            };

            const fillParentKelurahan = function (selectedKelurahan) {
                const provinsi = parentProvinsi ? parentProvinsi.value : '';
                const kabupaten = parentKabupaten ? parentKabupaten.value : '';
                const kecamatan = parentKecamatan ? parentKecamatan.value : '';
                const options = wilayahOptions[provinsi]?.[kabupaten]?.[kecamatan] || [];
                fillDatalist(parentKelurahan, options);

                if (selectedKelurahan !== undefined && parentKelurahan) {
                    parentKelurahan.value = selectedKelurahan;
                }
            };

            const fillParentWilayah = function () {
                fillParentKabupaten();
                fillParentKecamatan();
                fillParentKelurahan();
            };

            const setParentAddressDisabled = function (isDisabled) {
                [parentProvinsi, parentKabupaten, parentKecamatan, parentKelurahan, parentDetailAddress].forEach(function (control) {
                    if (! control) {
                        return;
                    }

                    control.disabled = isDisabled;
                    control.classList.toggle('is-valid', false);
                    control.classList.toggle('is-invalid', false);
                    control.setCustomValidity('');
                });
            };

            const syncParentAddressFromStudent = function () {
                if (! parentSameAddress?.checked) {
                    return;
                }

                if (parentProvinsi) {
                    parentProvinsi.value = 'Papua Barat';
                }

                fillParentKabupaten('Teluk Bintuni');
                fillParentKecamatan(domisiliKecamatan?.value || '');
                fillParentKelurahan(domisiliKelurahan?.value || '');

                if (parentDetailAddress) {
                    parentDetailAddress.value = domisiliDetailAddress?.value || '';
                }
            };

            if (parentProvinsi && parentKabupaten && parentKecamatan && parentKelurahan) {
                fillParentWilayah();

                parentProvinsi.addEventListener('input', function () {
                    fillParentKabupaten();
                    fillParentKecamatan();
                    fillParentKelurahan();
                });

                parentKabupaten.addEventListener('input', function () {
                    fillParentKecamatan();
                    fillParentKelurahan();
                });

                parentKecamatan.addEventListener('input', function () {
                    fillParentKelurahan();
                });
            }

            if (parentSameAddress) {
                const toggleParentSameAddress = function () {
                    syncParentAddressFromStudent();
                    setParentAddressDisabled(parentSameAddress.checked);
                };

                parentSameAddress.addEventListener('change', toggleParentSameAddress);

                [domisiliKecamatan, domisiliKelurahan, domisiliDetailAddress].forEach(function (control) {
                    control?.addEventListener('change', syncParentAddressFromStudent);
                    control?.addEventListener('input', syncParentAddressFromStudent);
                });

                toggleParentSameAddress();
            }

            if (schoolInput) {
                const listId = schoolInput.dataset.schoolList;

                schoolInput.addEventListener('input', function () {
                    if (schoolInput.value.trim().length >= 3) {
                        schoolInput.setAttribute('list', listId);
                    } else {
                        schoolInput.removeAttribute('list');
                    }
                });
            }

            form.querySelectorAll('input[type="file"][data-file-name-target]').forEach(function (input) {
                const target = document.getElementById(input.dataset.fileNameTarget);

                if (! target) {
                    return;
                }

                const updateFileName = function () {
                    const fileName = input.files[0]?.name || target.dataset.emptyFileText || 'Belum ada file dipilih';
                    target.textContent = fileName;
                    target.title = fileName;
                };

                input.addEventListener('change', updateFileName);
                updateFileName();
            });

            const getControls = function () {
                return Array.from(form.querySelectorAll('input, select, textarea')).filter(function (control) {
                return ! control.disabled && ! control.readOnly && control.type !== 'hidden';
                });
            };

            const getLabel = function (control) {
                return control.dataset.fieldLabel || 'Kolom ini';
            };

            const feedbackAnchor = function (control) {
                if (control.type === 'radio') {
                    return control.closest('.col-md-6') || control;
                }

                return control.closest('.input-group') || control;
            };

            const feedbackFor = function (control) {
                const anchor = feedbackAnchor(control);
                let feedback = anchor.nextElementSibling;

                if (! feedback || ! feedback.classList.contains('validation-message')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback validation-message d-block';
                    anchor.insertAdjacentElement('afterend', feedback);
                }

                return feedback;
            };

            const setError = function (control, message) {
                control.classList.add('is-invalid');
                control.classList.remove('is-valid');
                control.setCustomValidity(message);
                feedbackFor(control).textContent = message;
            };

            const clearError = function (control) {
                control.classList.remove('is-invalid');
                control.classList.add('is-valid');
                control.setCustomValidity('');
                feedbackFor(control).textContent = '';
            };

            const radioGroup = function (control) {
                return Array.from(form.querySelectorAll('input[type="radio"][name="' + control.name + '"]'));
            };

            const validateRadio = function (control) {
                const radios = radioGroup(control);
                const checked = radios.some(function (radio) {
                    return radio.checked;
                });

                if (! checked) {
                    setError(radios[0], getLabel(control) + ' wajib dipilih.');
                    return false;
                }

                radios.forEach(function (radio) {
                    clearError(radio);
                });

                return true;
            };

            const validateFile = function (control) {
                const file = control.files[0];
                const label = getLabel(control);
                const maxSize = Number(control.dataset.maxFileSize || 1048576);
                const fileKind = control.dataset.fileValidation;

                if (! file) {
                    if (control.required) {
                        setError(control, label + ' wajib diunggah.');
                        return false;
                    }

                    clearError(control);
                    control.classList.remove('is-valid');
                    return true;
                }

                if (file.size > maxSize) {
                    setError(control, label + ' maksimal berukuran 1 MB.');
                    return false;
                }

                if (fileKind === 'document' && ! ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                    setError(control, label + ' harus berupa file PDF, JPG, JPEG, PNG, atau WEBP.');
                    return false;
                }

                if (fileKind === 'image' && ! ['image/jpeg', 'image/png'].includes(file.type)) {
                    setError(control, label + ' harus berupa file JPG, JPEG, atau PNG.');
                    return false;
                }

                clearError(control);
                return true;
            };

            const validateControl = function (control) {
                const label = getLabel(control);

                if (control.type === 'file') {
                    return validateFile(control);
                }

                if (control.type === 'radio') {
                    return validateRadio(control);
                }

                if (control.required && (String(control.value).trim() === '' || control.validity.valueMissing)) {
                    setError(control, label + ' wajib diisi.');
                    return false;
                }

                if (control.validity.patternMismatch && control.name === 'nik') {
                    setError(control, 'NIK harus berisi 16 digit angka.');
                    return false;
                }

                if (control.validity.rangeUnderflow || control.validity.rangeOverflow) {
                    setError(control, 'Usia peserta harus berada pada rentang 13 sampai 21 tahun.');
                    return false;
                }

                clearError(control);
                return true;
            };

            getControls().forEach(function (control) {
                control.addEventListener(control.type === 'file' ? 'change' : 'input', function () {
                    validateControl(control);

                });

                control.addEventListener('blur', function () {
                    validateControl(control);
                });

                if (control.dataset.openDateOnFocus !== undefined && typeof control.showPicker === 'function') {
                    control.addEventListener('focus', function () {
                        try {
                            control.showPicker();
                        } catch (error) {
                        }
                    });

                    control.addEventListener('click', function () {
                        try {
                            control.showPicker();
                        } catch (error) {
                        }
                    });
                }
            });

            form.addEventListener('submit', function (event) {
                let isValid = true;

                getControls().forEach(function (control) {
                    if (! validateControl(control)) {
                        isValid = false;
                    }
                });

                if (! isValid) {
                    event.preventDefault();
                    event.stopPropagation();

                    const firstInvalid = form.querySelector('.is-invalid');

                    if (firstInvalid) {
                        firstInvalid.closest('section')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        setTimeout(function () {
                            firstInvalid.focus({ preventScroll: true });
                        }, 250);
                    }
                }
            });
        });
    </script>
</x-layouts.app>
