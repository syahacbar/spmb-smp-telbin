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
        $parentAddressSame = $errors->any()
            ? (bool) old('alamat_ortu_sama_dengan_siswa')
            : (bool) $formulir->alamat_ortu_sama_dengan_siswa;
        $selectedProvinsiOrtu = old('alamat_ortu_provinsi', $formulir->alamat_ortu_provinsi);
        $selectedKabupatenOrtu = old('alamat_ortu_kabupaten', $formulir->alamat_ortu_kabupaten);
        $selectedKecamatanOrtu = old('alamat_ortu_kecamatan', $formulir->alamat_ortu_kecamatan);
        $selectedKelurahanOrtu = old('alamat_ortu_kelurahan', $formulir->alamat_ortu_kelurahan);
        $sekolahAsalOptions = $sekolahAsalOptions ?? [];
        $wilayahProvinsiOptions = $wilayahProvinsiOptions ?? [];
        $wilayahOptions = $wilayahOptions ?? [];
        $jalurOptions = $jalurOptions ?? collect();
        $schoolOptions = $schoolOptions ?? collect();
        $selectedJalur = (string) old('jalur_id', $formulir->jalur_id);
        $selectedSekolah = (string) old('sekolah_id', $formulir->sekolah_id);
        $documentGuides = [
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
            <div class="text-muted">Lengkapi biodata, unggah pas foto, lalu pilih sekolah tujuan.</div>
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
            <aside class="registration-nav" aria-label="Tahapan formulir">
                <button type="button" class="registration-nav-link active" data-step-target="1">
                    <span>1</span>
                    <div>
                        <strong>Lengkapi Biodata</strong>
                        <small>Siswa dan orang tua/wali</small>
                    </div>
                </button>
                <button type="button" class="registration-nav-link" data-step-target="2">
                    <span>2</span>
                    <div>
                        <strong>Upload Pas Foto</strong>
                        <small>Foto peserta 3x4</small>
                    </div>
                </button>
                <button type="button" class="registration-nav-link" data-step-target="3">
                    <span>3</span>
                    <div>
                        <strong>Pilih Sekolah</strong>
                        <small>Zonasi dan jalur masuk</small>
                    </div>
                </button>
            </aside>

            <div class="registration-content">
                <section id="data-diri" class="card shadow-sm mb-3 form-section form-step" data-form-step="1">
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
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                    <div>
                                        <div class="fw-bold">Tempat Tinggal / Domisili</div>
                                        <div class="small text-muted">Data ini diambil otomatis dari registrasi akun dan tidak perlu diisi kembali.</div>
                                    </div>
                                    <span class="badge text-bg-success align-self-start">Terverifikasi saat registrasi akun</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="small text-muted">Kabupaten</div>
                                        <div class="fw-semibold">{{ $domisili['alamat_kabupaten'] }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">Kecamatan/Distrik</div>
                                        <div class="fw-semibold">{{ $domisili['alamat_kecamatan'] }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="small text-muted">Kelurahan/Desa</div>
                                        <div class="fw-semibold">{{ $domisili['alamat_kelurahan'] }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="small text-muted">Detail Alamat</div>
                                        <div class="fw-semibold">{{ $domisili['alamat'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="data-orang-tua" class="card shadow-sm mb-3 form-section form-step" data-form-step="1">
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

                <section id="upload-dokumen" class="card shadow-sm mb-3 form-section form-step" data-form-step="2" hidden>
                    <div class="card-header">
                        <span class="section-number">2</span>
                        <div>
                            <div class="fw-bold">Upload Pas Foto</div>
                            <div class="small text-muted">Gunakan pas foto 3x4 berlatar biru dan terlihat jelas.</div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row g-3">
                            @foreach($documentGuides as $field => $document)
                                <div class="col-lg-7">
                                    <div class="upload-box upload-box-modern">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                                            <div>
                                                <label class="form-label fw-bold mb-1">{{ $document['label'] }}</label>
                                                <div class="small text-muted">{{ $document['description'] }}</div>
                                            </div>
                                            <span class="badge text-bg-primary">
                                                {{ ($document['from_registration'] ?? false) ? 'Dari akun' : 'Max 1 MB' }}
                                            </span>
                                        </div>

                                        @php
                                            $isRegistrationDocument = $document['from_registration'] ?? false;
                                            $registrationDocumentAvailable = $isRegistrationDocument
                                                && isset($registrasiAkun)
                                                && $registrasiAkun?->kartuKeluargaTersedia();
                                            $legacyDocumentAvailable = $isRegistrationDocument
                                                && $isEdit
                                                && $formulir->{$field};
                                        @endphp

                                        @if($isRegistrationDocument)
                                            <div class="uploaded-file mb-2">
                                                <div class="fw-bold small">Berkas dari registrasi akun</div>
                                                @if($registrationDocumentAvailable)
                                                    <a href="{{ route('registrasi.kk', $registrasiAkun) }}"
                                                       class="small text-decoration-none"
                                                       data-document-preview
                                                       data-document-title="{{ $document['label'] }}"
                                                       data-document-type="{{ $registrasiAkun->kartuKeluargaIsImage() ? 'image' : 'pdf' }}"
                                                       data-document-download="{{ route('registrasi.kk', ['registrasi' => $registrasiAkun, 'download' => 1]) }}">Lihat Berkas</a>
                                                @elseif($legacyDocumentAvailable)
                                                    <a href="{{ $formulir->berkasUrl($field) }}"
                                                       class="small text-decoration-none"
                                                       data-document-preview
                                                       data-document-title="{{ $document['label'] }}"
                                                       data-document-type="{{ $formulir->berkasIsImage($field) ? 'image' : 'pdf' }}"
                                                       data-document-download="{{ $formulir->berkasDownloadUrl($field) }}">Lihat Berkas</a>
                                                @else
                                                    <span class="small text-danger">Berkas tidak tersedia</span>
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary w-100" disabled>Upload KK dinonaktifkan</button>
                                            <div class="document-hint mt-2">Kartu Keluarga sudah diunggah saat registrasi akun dan tidak perlu diunggah ulang.</div>
                                        @elseif($isEdit && $formulir->{$field})
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

                                        @if(! $isRegistrationDocument)
                                            <div class="input-group upload-file-control">
                                                <label for="upload-{{ $field }}" class="btn btn-outline-primary">Pilih file</label>
                                                <span class="form-control upload-file-name" id="upload-name-{{ $field }}" data-empty-file-text="Belum ada file dipilih">Belum ada file dipilih</span>
                                                <input type="file" id="upload-{{ $field }}" name="{{ $field }}" class="visually-hidden" accept="{{ $document['accept'] }}" data-file-validation="{{ $field === 'foto_selfie' ? 'image' : 'document' }}" data-field-label="{{ $document['label'] }}" data-max-file-size="1048576" data-file-name-target="upload-name-{{ $field }}" @required(! $isEdit)>
                                            </div>
                                            <div class="document-hint mt-2">{{ $document['hint'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                <section id="pilih-sekolah" class="card shadow-sm mb-3 form-section form-step" data-form-step="3" hidden>
                    <div class="card-header">
                        <span class="section-number">3</span>
                        <div>
                            <div class="fw-bold">Pilih Sekolah Tujuan</div>
                            <div class="small text-muted">Pilih sekolah terlebih dahulu. Sistem akan menentukan jalur berdasarkan zonasi domisili.</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <select name="sekolah_id" class="visually-hidden" data-school-target data-selected="{{ $selectedSekolah }}" data-field-label="Sekolah tujuan" required>
                            <option value="">Pilih sekolah tujuan</option>
                            @foreach($schoolOptions as $school)
                                <option value="{{ $school['id'] }}" @selected($selectedSekolah === (string) $school['id'])>{{ $school['nama'] }}</option>
                            @endforeach
                        </select>
                        <select name="jalur_id" class="visually-hidden" data-jalur-select data-field-label="Jalur pendaftaran" required>
                            <option value="">Pilih jalur</option>
                            @foreach($jalurOptions as $jalur)
                                <option value="{{ $jalur->id }}" data-code="{{ $jalur->kode }}" @selected($selectedJalur === (string) $jalur->id)>{{ $jalur->nama }}</option>
                            @endforeach
                        </select>

                        <div class="school-choice-grid mb-3" data-school-choice-list>
                            @foreach($schoolOptions as $school)
                                <article class="school-choice-card" data-school-choice="{{ $school['id'] }}">
                                    <div class="d-flex justify-content-between gap-2 mb-3">
                                        <div>
                                            <h6 class="fw-bold mb-1">{{ $school['nama'] }}</h6>
                                            <div class="small text-muted">{{ ucfirst($school['status']) }}</div>
                                        </div>
                                        @if($school['eligible_domisili'])
                                            <span class="badge text-bg-success align-self-start">Dalam Zonasi</span>
                                        @else
                                            <span class="badge text-bg-light align-self-start">Luar Zonasi</span>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-primary w-100" data-choose-school="{{ $school['id'] }}">Pilih Sekolah</button>
                                </article>
                            @endforeach
                        </div>

                        <div class="d-none" data-pathway-selection-panel>
                            <div class="alert alert-success d-none" data-zone-notice-inside>
                                <div class="fw-bold"><span class="badge text-bg-success me-2">Dalam Zonasi</span> Sekolah berada dalam zonasi domisili Anda.</div>
                                <div class="small mt-1">Anda dapat memilih salah satu dari 4 jalur masuk di bawah (Sistem merekomendasikan <strong>Jalur Domisili</strong>).</div>
                            </div>

                            <div class="alert alert-warning d-none" data-zone-notice-outside>
                                <div class="fw-bold"><span class="badge text-bg-warning me-2">Luar Zonasi</span> Sekolah berada di luar zonasi domisili Anda.</div>
                                <div class="small mt-1">Anda hanya dapat memilih dari 3 jalur masuk di bawah (Jalur Domisili tidak tersedia).</div>
                            </div>

                            <div class="row g-2 mb-3" data-path-list-container>
                                @foreach($jalurOptions as $jalur)
                                    <div class="col-md-3 d-none" data-pathway-button-wrapper="{{ $jalur->kode }}">
                                        <button type="button"
                                                class="btn btn-outline-primary w-100 h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center"
                                                data-choose-path="{{ $jalur->kode }}"
                                                data-path-id="{{ $jalur->id }}"
                                                @disabled(
                                                    $jalur->kode === 'prestasi'
                                                    && (
                                                        ! $nilaiTka
                                                        || $nilaiTka['matematika'] === null
                                                        || $nilaiTka['bahasa_indonesia'] === null
                                                    )
                                                )>
                                            <strong class="d-block text-primary" style="font-size: 0.95rem; font-weight: 800;">
                                                {{ $jalur->nama }}
                                            </strong>
                                            @if($jalur->kode === 'domisili')
                                                <span class="badge text-bg-success mt-1 small" style="font-size: 0.7rem; font-weight: 700;">Rekomendasi</span>
                                            @endif
                                            <span class="text-muted mt-2 d-block" style="font-size: 0.72rem; line-height: 1.2;">{{ $jalur->deskripsi }}</span>
                                            @if(
                                                $jalur->kode === 'prestasi'
                                                && (
                                                    ! $nilaiTka
                                                    || $nilaiTka['matematika'] === null
                                                    || $nilaiTka['bahasa_indonesia'] === null
                                                )
                                            )
                                                <span class="d-block text-danger mt-2 fw-bold" style="font-size: 0.7rem;">Nilai TKA belum tersedia</span>
                                            @endif
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <div class="address-group d-none mb-3" data-tka-panel>
                                <div class="fw-bold mb-2">Persyaratan Jalur Prestasi</div>
                                <div class="d-flex flex-wrap gap-4">
                                    <div><div class="small text-muted">TKA Matematika</div><strong>{{ $nilaiTka['matematika'] ?? '-' }}</strong></div>
                                    <div><div class="small text-muted">TKA Bahasa Indonesia</div><strong>{{ $nilaiTka['bahasa_indonesia'] ?? '-' }}</strong></div>
                                </div>
                                <div class="small text-muted mt-2">Nilai TKA wajib tersedia dan pemeringkatan dilakukan berdasarkan data resmi Dinas.</div>
                            </div>

                            <div class="upload-box upload-box-modern d-none" data-support-panel>
                                <label class="form-label fw-bold" data-support-title>Dokumen Pendukung Jalur</label>
                                @if($isEdit && $formulir->dokumen_pendukung)
                                    <div class="uploaded-file mb-2">
                                        <a href="{{ $formulir->berkasUrl('dokumen_pendukung') }}" data-document-preview data-document-title="Dokumen Pendukung" data-document-type="{{ $formulir->berkasIsImage('dokumen_pendukung') ? 'image' : 'pdf' }}" data-document-download="{{ $formulir->berkasDownloadUrl('dokumen_pendukung') }}">Lihat berkas saat ini</a>
                                    </div>
                                @endif
                                <input type="file" name="dokumen_pendukung" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" data-support-file data-file-validation="document" data-field-label="Dokumen pendukung" data-max-file-size="2097152">
                                <div class="small text-muted mt-2" data-support-description>Format PDF/gambar, maksimal 2 MB.</div>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0">
                            Setelah disimpan, formulir akan masuk ke halaman pemeriksaan sebelum dikirim final.
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="sticky-actions d-flex flex-column flex-sm-row gap-2 mb-4">
            <button type="button" class="btn btn-outline-secondary d-none" data-step-previous>Kembali ke Tahap Sebelumnya</button>
            <button type="button" class="btn btn-primary" data-step-next>Lanjutkan</button>
            <button type="submit" class="btn btn-primary d-none" data-step-submit>{{ $isEdit ? 'Simpan Perubahan dan Periksa' : 'Simpan dan Periksa' }}</button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
        </div>
    </form>

    <div class="modal fade" id="prestasiSchoolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="small text-muted">Detail Jalur Prestasi</div>
                        <h5 class="modal-title mb-0" data-prestasi-modal-name>Nama Sekolah</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="review-summary mb-3">
                        <div class="review-summary-item"><div class="small text-muted">Nilai TKA Anda</div><strong data-prestasi-modal-score>-</strong></div>
                        <div class="review-summary-item"><div class="small text-muted">Cut-off Sementara</div><strong data-prestasi-modal-cutoff>-</strong></div>
                        <div class="review-summary-item"><div class="small text-muted">Kuota Prestasi</div><strong data-prestasi-modal-quota>-</strong></div>
                        <div class="review-summary-item"><div class="small text-muted">Pendaftar</div><strong data-prestasi-modal-applicants>-</strong></div>
                    </div>
                    <div class="d-flex justify-content-between gap-2 mb-2">
                        <strong>Estimasi Peluang</strong>
                        <strong data-prestasi-modal-opportunity>-</strong>
                    </div>
                    <div class="opportunity-progress">
                        <div class="opportunity-progress-bar" data-prestasi-modal-progress></div>
                    </div>
                    <div class="small text-muted mt-3">Simulasi bersifat informatif dan bukan keputusan kelulusan.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-prestasi-modal-select>Pilih Sekolah</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('[data-registration-form]');

            if (! form) {
                return;
            }

            const wilayahOptions = @json($wilayahOptions);
            const schoolOptions = @json($schoolOptions);
            const schoolInput = form.querySelector('[data-school-input]');
            const pathSelect = form.querySelector('[data-jalur-select]');
            const targetSchool = form.querySelector('[data-school-target]');
            const schoolChoiceList = form.querySelector('[data-school-choice-list]');
            const pathwaySelectionPanel = form.querySelector('[data-pathway-selection-panel]');
            const zoneNoticeInside = form.querySelector('[data-zone-notice-inside]');
            const zoneNoticeOutside = form.querySelector('[data-zone-notice-outside]');
            const pathListContainer = form.querySelector('[data-path-list-container]');
            const supportTitle = form.querySelector('[data-support-title]');
            const supportDescription = form.querySelector('[data-support-description]');
            const standardSchoolPanel = form.querySelector('[data-standard-school-panel]');
            const domicileSchoolPanel = form.querySelector('[data-domicile-school-panel]');
            const domicileMapElement = form.querySelector('[data-domicile-map]');
            const domicileSchoolList = form.querySelector('[data-domicile-school-list]');
            const domicileLoading = form.querySelector('[data-domicile-loading]');
            const domicileError = form.querySelector('[data-domicile-error]');
            const prestasiSchoolPanel = form.querySelector('[data-prestasi-school-panel]');
            const prestasiLoading = form.querySelector('[data-prestasi-loading]');
            const prestasiError = form.querySelector('[data-prestasi-error]');
            const prestasiSummary = form.querySelector('[data-prestasi-summary]');
            const prestasiDisclaimer = form.querySelector('[data-prestasi-disclaimer]');
            const prestasiSchoolList = form.querySelector('[data-prestasi-school-list]');
            const prestasiModalElement = document.getElementById('prestasiSchoolModal');
            const prestasiModalName = document.querySelector('[data-prestasi-modal-name]');
            const prestasiModalScore = document.querySelector('[data-prestasi-modal-score]');
            const prestasiModalCutoff = document.querySelector('[data-prestasi-modal-cutoff]');
            const prestasiModalQuota = document.querySelector('[data-prestasi-modal-quota]');
            const prestasiModalApplicants = document.querySelector('[data-prestasi-modal-applicants]');
            const prestasiModalOpportunity = document.querySelector('[data-prestasi-modal-opportunity]');
            const prestasiModalProgress = document.querySelector('[data-prestasi-modal-progress]');
            const prestasiModalSelect = document.querySelector('[data-prestasi-modal-select]');
            const reviewPath = form.querySelector('[data-review-path]');
            const reviewSchool = form.querySelector('[data-review-school]');
            const pathDescription = form.querySelector('[data-path-description]');
            const tkaPanel = form.querySelector('[data-tka-panel]');
            const supportPanel = form.querySelector('[data-support-panel]');
            const supportFile = form.querySelector('[data-support-file]');
            const parentSameAddress = form.querySelector('[data-parent-same-address]');
            const parentProvinsi = form.querySelector('[data-parent-provinsi]');
            const parentKabupaten = form.querySelector('[data-parent-kabupaten]');
            const parentKecamatan = form.querySelector('[data-parent-kecamatan]');
            const parentKelurahan = form.querySelector('[data-parent-kelurahan]');
            const parentDetailAddress = form.querySelector('[data-parent-detail-address]');
            const domisili = @json($domisili);
            let domicileMap = null;
            let domicileSchoolsLoaded = false;
            let domicilePayload = null;
            let domicileSchoolData = [];
            let schoolMarkers = new Map();
            let prestasiLoaded = false;
            let prestasiPayload = null;
            let prestasiSchoolData = [];
            let activePrestasiSchool = null;
            const prestasiModal = prestasiModalElement ? new bootstrap.Modal(prestasiModalElement) : null;

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

                fillParentKabupaten(domisili.alamat_kabupaten || 'Teluk Bintuni');
                fillParentKecamatan(domisili.alamat_kecamatan || '');
                fillParentKelurahan(domisili.alamat_kelurahan || '');

                if (parentDetailAddress) {
                    parentDetailAddress.value = domisili.alamat || '';
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

            const escapeHtml = function (value) {
                const element = document.createElement('div');
                element.textContent = String(value ?? '');
                return element.innerHTML;
            };

            const selectedSchoolName = function () {
                const selectedId = String(targetSchool?.value || '');
                const domicileSchool = domicileSchoolData.find(function (school) {
                    return String(school.id) === selectedId;
                });
                const prestasiSchool = prestasiSchoolData.find(function (school) {
                    return String(school.id) === selectedId;
                });

                return domicileSchool?.nama
                    || prestasiSchool?.nama
                    || targetSchool?.selectedOptions[0]?.textContent
                    || 'Belum dipilih';
            };

            const updateReview = function () {
                if (reviewPath) {
                    reviewPath.textContent = pathSelect?.selectedOptions[0]?.textContent || 'Belum dipilih';
                }

                if (reviewSchool) {
                    reviewSchool.textContent = selectedSchoolName();
                }
            };

            const selectDomicileSchool = function (schoolId) {
                if (! targetSchool) {
                    return;
                }

                targetSchool.value = String(schoolId);
                targetSchool.dataset.selected = String(schoolId);
                targetSchool.setCustomValidity('');
                targetSchool.classList.remove('is-invalid');
                domicileError?.classList.add('d-none');
                targetSchool.dispatchEvent(new Event('change', { bubbles: true }));

                domicileSchoolList?.querySelectorAll('[data-school-card]').forEach(function (card) {
                    const selected = String(card.dataset.schoolCard) === String(schoolId);
                    card.classList.toggle('selected', selected);
                    const button = card.querySelector('[data-select-school]');
                    if (button) {
                        button.textContent = selected ? 'Sekolah Dipilih' : 'Pilih Sekolah';
                        button.classList.toggle('btn-success', selected);
                        button.classList.toggle('btn-outline-primary', ! selected);
                    }
                });

                const marker = schoolMarkers.get(String(schoolId));
                if (marker && domicileMap) {
                    domicileMap.setView(marker.getLatLng(), Math.max(domicileMap.getZoom(), 13));
                    marker.openPopup();
                }

                updateReview();
            };

            const renderDomicileSchools = function (payload) {
                domicileSchoolData = payload.schools || [];
                schoolMarkers = new Map();

                targetSchool.innerHTML = '<option value="">Pilih sekolah dari peta atau daftar</option>';
                domicileSchoolData.forEach(function (school) {
                    const option = document.createElement('option');
                    option.value = school.id;
                    option.textContent = school.nama;
                    targetSchool.appendChild(option);
                });

                const preservedSelection = targetSchool.dataset.selected;
                if (preservedSelection && domicileSchoolData.some(school => String(school.id) === String(preservedSelection))) {
                    targetSchool.value = String(preservedSelection);
                }

                if (domicileSchoolList) {
                    domicileSchoolList.innerHTML = '';

                    domicileSchoolData.forEach(function (school) {
                        const card = document.createElement('article');
                        card.className = 'school-choice-card';
                        card.dataset.schoolCard = school.id;
                        card.innerHTML = `
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">${escapeHtml(school.nama)}</h6>
                                    <div class="small text-muted">${escapeHtml(school.alamat || 'Alamat sekolah belum tersedia')}</div>
                                </div>
                                <span class="badge text-bg-light align-self-start">Domisili</span>
                            </div>
                            <div class="school-quota-grid mb-3">
                                <div class="school-quota-item"><strong>${school.kuota}</strong><span>Kuota</span></div>
                                <div class="school-quota-item"><strong>${school.pendaftar}</strong><span>Pendaftar</span></div>
                                <div class="school-quota-item"><strong>${school.sisa_kuota}</strong><span>Sisa</span></div>
                            </div>
                            <button type="button" class="btn btn-outline-primary w-100" data-select-school="${school.id}">Pilih Sekolah</button>
                        `;
                        domicileSchoolList.appendChild(card);
                    });
                }

                if (domicileMapElement && window.L) {
                    if (domicileMap) {
                        domicileMap.remove();
                    }

                    domicileMap = L.map(domicileMapElement, { scrollWheelZoom: false });
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 18,
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(domicileMap);

                    const bounds = [];
                    if (payload.domisili?.latitude && payload.domisili?.longitude) {
                        const center = [payload.domisili.latitude, payload.domisili.longitude];
                        L.circle(center, {
                            radius: payload.domisili.radius_meters || 1800,
                            color: '#0788a8',
                            fillColor: '#38bdf8',
                            fillOpacity: .18,
                            weight: 2
                        }).bindPopup('Area perkiraan domisili: ' + escapeHtml(payload.domisili.label)).addTo(domicileMap);
                        bounds.push(center);
                    }

                    domicileSchoolData.forEach(function (school) {
                        if (! school.latitude || ! school.longitude) {
                            return;
                        }

                        const marker = L.marker([school.latitude, school.longitude]).addTo(domicileMap);
                        marker.bindPopup(`
                            <strong>${escapeHtml(school.nama)}</strong><br>
                            <span>${escapeHtml(school.alamat || '-')}</span><hr class="my-2">
                            Kuota: ${school.kuota}<br>
                            Pendaftar: ${school.pendaftar}<br>
                            Sisa kuota: ${school.sisa_kuota}
                        `);
                        marker.on('click', function () {
                            selectDomicileSchool(school.id);
                        });
                        schoolMarkers.set(String(school.id), marker);
                        bounds.push([school.latitude, school.longitude]);
                    });

                    if (bounds.length > 1) {
                        domicileMap.fitBounds(bounds, { padding: [35, 35], maxZoom: 13 });
                    } else if (bounds.length === 1) {
                        domicileMap.setView(bounds[0], 12);
                    } else {
                        domicileMap.setView([-2.5, 133.5], 8);
                    }

                    setTimeout(function () {
                        domicileMap?.invalidateSize();
                    }, 100);
                }

                if (targetSchool.value) {
                    selectDomicileSchool(targetSchool.value);
                }

                if (! domicileSchoolData.length && domicileError) {
                    domicileError.textContent = 'Belum ada sekolah yang ditetapkan untuk zonasi domisili Anda.';
                    domicileError.classList.remove('d-none');
                }
            };

            const loadDomicileSchools = async function () {
                if (domicileSchoolsLoaded || ! domicileSchoolPanel) {
                    if (domicilePayload) {
                        renderDomicileSchools(domicilePayload);
                    }
                    setTimeout(function () {
                        domicileMap?.invalidateSize();
                    }, 100);
                    return;
                }

                domicileLoading?.classList.remove('d-none');
                domicileError?.classList.add('d-none');

                try {
                    const response = await fetch(domicileSchoolPanel.dataset.endpoint, {
                        headers: { Accept: 'application/json' }
                    });
                    const payload = await response.json();

                    if (! response.ok) {
                        throw new Error(payload.message || 'Data sekolah domisili tidak dapat dimuat.');
                    }

                    domicileSchoolsLoaded = true;
                    domicilePayload = payload;
                    renderDomicileSchools(payload);
                } catch (error) {
                    if (domicileError) {
                        domicileError.textContent = error.message || 'Data sekolah domisili tidak dapat dimuat.';
                        domicileError.classList.remove('d-none');
                    }
                } finally {
                    domicileLoading?.classList.add('d-none');
                }
            };

            domicileSchoolList?.addEventListener('click', function (event) {
                const button = event.target.closest('[data-select-school]');
                if (button) {
                    selectDomicileSchool(button.dataset.selectSchool);
                }
            });

            const opportunityClass = function (level) {
                return level === 'high'
                    ? 'opportunity-high'
                    : (level === 'medium' ? 'opportunity-medium' : 'opportunity-low');
            };

            const opportunityTextClass = function (level) {
                return level === 'high'
                    ? 'opportunity-text-high'
                    : (level === 'medium' ? 'opportunity-text-medium' : 'opportunity-text-low');
            };

            const selectPrestasiSchool = function (schoolId) {
                targetSchool.value = String(schoolId);
                targetSchool.dataset.selected = String(schoolId);
                targetSchool.setCustomValidity('');
                targetSchool.classList.remove('is-invalid');
                prestasiError?.classList.add('d-none');
                targetSchool.dispatchEvent(new Event('change', { bubbles: true }));

                prestasiSchoolList?.querySelectorAll('[data-prestasi-card]').forEach(function (card) {
                    card.classList.toggle(
                        'selected',
                        String(card.dataset.prestasiCard) === String(schoolId)
                    );
                });
                updateReview();
            };

            const openPrestasiSchool = function (schoolId) {
                const school = prestasiSchoolData.find(item => String(item.id) === String(schoolId));
                if (! school || ! prestasiPayload?.student) {
                    return;
                }

                activePrestasiSchool = school;
                const level = school.peluang.level;
                prestasiModalName.textContent = school.nama;
                prestasiModalScore.textContent = Number(prestasiPayload.student.rata_rata).toFixed(2);
                prestasiModalCutoff.textContent = school.cutoff === null
                    ? 'Belum terbentuk'
                    : Number(school.cutoff).toFixed(2);
                prestasiModalQuota.textContent = school.kuota + ' kursi (' + school.kuota_persen + '%)';
                prestasiModalApplicants.textContent = school.pendaftar + ' siswa';
                prestasiModalOpportunity.textContent = school.peluang.percentage + '% · ' + school.peluang.label;
                prestasiModalOpportunity.className = opportunityTextClass(level);
                prestasiModalProgress.className = 'opportunity-progress-bar ' + opportunityClass(level);
                prestasiModalProgress.style.width = school.peluang.percentage + '%';
                prestasiModalSelect.textContent = String(targetSchool.value) === String(school.id)
                    ? 'Sekolah Dipilih'
                    : 'Pilih Sekolah';
                prestasiModalSelect.classList.toggle(
                    'btn-success',
                    String(targetSchool.value) === String(school.id)
                );
                prestasiModalSelect.classList.toggle(
                    'btn-primary',
                    String(targetSchool.value) !== String(school.id)
                );
                prestasiModal?.show();
            };

            const renderPrestasiSchools = function (payload) {
                prestasiPayload = payload;
                prestasiSchoolData = payload.schools || [];
                const student = payload.student;

                if (prestasiSummary) {
                    prestasiSummary.innerHTML = `
                        <div class="achievement-stat"><span>TKA Matematika</span><strong>${Number(student.matematika).toFixed(2)}</strong></div>
                        <div class="achievement-stat"><span>TKA Bahasa Indonesia</span><strong>${Number(student.bahasa_indonesia).toFixed(2)}</strong></div>
                        <div class="achievement-stat"><span>Rata-rata TKA</span><strong>${Number(student.rata_rata).toFixed(2)}</strong></div>
                        <div class="achievement-stat"><span>Peringkat Sementara</span><strong>#${student.peringkat} <small class="fs-6 text-muted">/ ${student.total_peserta}</small></strong></div>
                    `;
                }

                if (prestasiDisclaimer) {
                    prestasiDisclaimer.textContent = payload.disclaimer || '';
                }

                targetSchool.innerHTML = '<option value="">Pilih sekolah dari daftar perbandingan</option>';
                prestasiSchoolData.forEach(function (school) {
                    const option = document.createElement('option');
                    option.value = school.id;
                    option.textContent = school.nama;
                    targetSchool.appendChild(option);
                });

                const preservedSelection = targetSchool.dataset.selected;
                if (prestasiSchoolData.some(school => String(school.id) === String(preservedSelection))) {
                    targetSchool.value = String(preservedSelection);
                }

                if (prestasiSchoolList) {
                    prestasiSchoolList.innerHTML = '';
                    prestasiSchoolData.forEach(function (school) {
                        const level = school.peluang.level;
                        const card = document.createElement('article');
                        card.className = 'school-choice-card prestasi-school-card';
                        card.dataset.prestasiCard = school.id;
                        card.innerHTML = `
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">${escapeHtml(school.nama)}</h6>
                                    <div class="small text-muted">${escapeHtml(school.alamat)}</div>
                                </div>
                                <span class="fw-bold ${opportunityTextClass(level)}">${school.peluang.label}</span>
                            </div>
                            <div class="school-quota-grid mb-3">
                                <div class="school-quota-item"><strong>${school.kuota}</strong><span>Kuota (${school.kuota_persen}%)</span></div>
                                <div class="school-quota-item"><strong>${school.pendaftar}</strong><span>Pendaftar</span></div>
                                <div class="school-quota-item"><strong>${school.cutoff === null ? '-' : Number(school.cutoff).toFixed(2)}</strong><span>Cut-off</span></div>
                            </div>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Estimasi peluang</span><strong class="${opportunityTextClass(level)}">${school.peluang.percentage}%</strong>
                            </div>
                            <div class="opportunity-progress">
                                <div class="opportunity-progress-bar ${opportunityClass(level)}" style="width:${school.peluang.percentage}%"></div>
                            </div>
                            <div class="small text-primary fw-bold mt-3">Klik untuk melihat detail</div>
                        `;
                        prestasiSchoolList.appendChild(card);
                    });
                }

                if (targetSchool.value) {
                    selectPrestasiSchool(targetSchool.value);
                }
            };

            const loadPrestasiSchools = async function () {
                if (prestasiLoaded || ! prestasiSchoolPanel) {
                    if (prestasiPayload) {
                        renderPrestasiSchools(prestasiPayload);
                    }
                    return;
                }

                prestasiLoading?.classList.remove('d-none');
                prestasiError?.classList.add('d-none');

                try {
                    const response = await fetch(prestasiSchoolPanel.dataset.endpoint, {
                        headers: { Accept: 'application/json' }
                    });
                    const payload = await response.json();

                    if (! response.ok) {
                        throw new Error(payload.message || 'Data Jalur Prestasi tidak dapat dimuat.');
                    }

                    prestasiLoaded = true;
                    renderPrestasiSchools(payload);
                } catch (error) {
                    if (prestasiError) {
                        prestasiError.textContent = error.message || 'Data Jalur Prestasi tidak dapat dimuat.';
                        prestasiError.classList.remove('d-none');
                    }
                } finally {
                    prestasiLoading?.classList.add('d-none');
                }
            };

            prestasiSchoolList?.addEventListener('click', function (event) {
                const card = event.target.closest('[data-prestasi-card]');
                if (card) {
                    openPrestasiSchool(card.dataset.prestasiCard);
                }
            });

            prestasiModalSelect?.addEventListener('click', function () {
                if (activePrestasiSchool) {
                    selectPrestasiSchool(activePrestasiSchool.id);
                    prestasiModal?.hide();
                }
            });

            const refreshSchoolOptions = function () {
                if (! pathSelect || ! targetSchool) {
                    return;
                }

                const code = pathSelect.selectedOptions[0]?.dataset.code || '';
                tkaPanel?.classList.toggle('d-none', code !== 'prestasi');
                supportPanel?.classList.toggle('d-none', ! ['afirmasi', 'mutasi'].includes(code));
                if (supportFile) {
                    supportFile.required = ['afirmasi', 'mutasi'].includes(code) && ! @json((bool) $formulir->dokumen_pendukung);
                }
                if (supportTitle) {
                    supportTitle.textContent = code === 'afirmasi'
                        ? 'Dokumen Pendukung Afirmasi'
                        : 'Dokumen Pendukung Mutasi';
                }
                if (supportDescription) {
                    supportDescription.textContent = code === 'afirmasi'
                        ? 'Unggah dokumen yang membuktikan persyaratan afirmasi. Format PDF/gambar, maksimal 2 MB.'
                        : 'Unggah surat mutasi atau dokumen perpindahan tugas orang tua/wali. Format PDF/gambar, maksimal 2 MB.';
                }

                pathListContainer?.querySelectorAll('[data-choose-path]').forEach(function (button) {
                    const selected = button.dataset.choosePath === code;
                    button.classList.toggle('btn-primary', selected);
                    button.classList.toggle('btn-outline-primary', ! selected);
                });
            };

            const selectSchool = function (schoolId) {
                const school = schoolOptions.find(item => String(item.id) === String(schoolId));
                if (! school) {
                    return;
                }

                targetSchool.value = String(school.id);
                targetSchool.dataset.selected = String(school.id);
                targetSchool.setCustomValidity('');

                schoolChoiceList?.querySelectorAll('[data-school-choice]').forEach(function (card) {
                    const selected = String(card.dataset.schoolChoice) === String(school.id);
                    card.classList.toggle('selected', selected);
                    const button = card.querySelector('[data-choose-school]');
                    if (button) {
                        button.textContent = selected ? 'Sekolah Dipilih' : 'Pilih Sekolah';
                        button.classList.toggle('btn-success', selected);
                        button.classList.toggle('btn-outline-primary', ! selected);
                    }
                });

                // Show the main pathway selection container
                pathwaySelectionPanel?.classList.remove('d-none');

                if (school.eligible_domisili) {
                    // Inside zonasi: show inside notice, hide outside notice
                    zoneNoticeInside?.classList.remove('d-none');
                    zoneNoticeOutside?.classList.add('d-none');

                    // Show all 4 pathways
                    pathListContainer?.querySelectorAll('[data-pathway-button-wrapper]').forEach(function (wrapper) {
                        wrapper.classList.remove('d-none');
                    });

                    // Set default to domisili if not already set or invalid
                    const currentCode = pathSelect.selectedOptions[0]?.dataset.code || '';
                    if (currentCode === '') {
                        const domisiliOption = Array.from(pathSelect.options).find(
                            option => option.dataset.code === 'domisili'
                        );
                        pathSelect.value = domisiliOption?.value || '';
                    }
                } else {
                    // Outside zonasi: hide inside notice, show outside notice
                    zoneNoticeInside?.classList.add('d-none');
                    zoneNoticeOutside?.classList.remove('d-none');

                    // Show only 3 pathways (excluding domisili)
                    pathListContainer?.querySelectorAll('[data-pathway-button-wrapper]').forEach(function (wrapper) {
                        const isDomisili = wrapper.dataset.pathwayButtonWrapper === 'domisili';
                        wrapper.classList.toggle('d-none', isDomisili);
                    });

                    // Clear if current selected was domisili
                    const currentCode = pathSelect.selectedOptions[0]?.dataset.code || '';
                    if (currentCode === 'domisili') {
                        pathSelect.value = '';
                    }
                }

                refreshSchoolOptions();
            };

            schoolChoiceList?.addEventListener('click', function (event) {
                const button = event.target.closest('[data-choose-school]');
                if (button) {
                    selectSchool(button.dataset.chooseSchool);
                }
            });

            pathListContainer?.addEventListener('click', function (event) {
                const button = event.target.closest('[data-choose-path]');
                if (! button) {
                    return;
                }

                pathSelect.value = button.dataset.pathId;
                pathSelect.setCustomValidity('');
                refreshSchoolOptions();
            });

            if (targetSchool.value) {
                selectSchool(targetSchool.value);
            }
            refreshSchoolOptions();

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
                if (control === targetSchool && schoolChoiceList) {
                    return schoolChoiceList;
                }

                if (control === pathSelect && outsidePathList) {
                    return outsidePathList;
                }

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
                    setError(control, label + ' maksimal berukuran ' + Math.round(maxSize / 1048576) + ' MB.');
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
                    if (
                        control === targetSchool
                        && pathSelect?.selectedOptions[0]?.dataset.code === 'domisili'
                        && domicileError
                    ) {
                        domicileError.textContent = 'Pilih salah satu sekolah dari marker peta atau daftar sekolah.';
                        domicileError.classList.remove('d-none');
                    }
                    if (
                        control === targetSchool
                        && pathSelect?.selectedOptions[0]?.dataset.code === 'prestasi'
                        && prestasiError
                    ) {
                        prestasiError.textContent = 'Pilih salah satu sekolah dari card perbandingan Jalur Prestasi.';
                        prestasiError.classList.remove('d-none');
                    }
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

            const stepSections = Array.from(form.querySelectorAll('[data-form-step]'));
            const stepLinks = Array.from(form.querySelectorAll('[data-step-target]'));
            const previousStepButton = form.querySelector('[data-step-previous]');
            const nextStepButton = form.querySelector('[data-step-next]');
            const submitStepButton = form.querySelector('[data-step-submit]');
            let currentStep = 1;
            let highestStep = 1;

            const controlsForStep = function (step) {
                const section = form.querySelector('[data-form-step="' + step + '"]');
                if (! section) {
                    return [];
                }

                return Array.from(section.querySelectorAll('input, select, textarea')).filter(function (control) {
                    return ! control.disabled && ! control.readOnly && control.type !== 'hidden';
                });
            };

            const validateStep = function (step) {
                let valid = true;
                controlsForStep(step).forEach(function (control) {
                    if (! validateControl(control)) {
                        valid = false;
                    }
                });

                if (! valid) {
                    const firstInvalid = form.querySelector('[data-form-step="' + step + '"] .is-invalid');
                    firstInvalid?.focus({ preventScroll: false });
                }

                return valid;
            };

            const showStep = function (step) {
                currentStep = Math.min(3, Math.max(1, step));
                highestStep = Math.max(highestStep, currentStep);

                stepSections.forEach(function (section) {
                    section.hidden = Number(section.dataset.formStep) !== currentStep;
                });
                stepLinks.forEach(function (link) {
                    const target = Number(link.dataset.stepTarget);
                    link.classList.toggle('active', target === currentStep);
                    link.classList.toggle('completed', target < currentStep);
                    link.disabled = target > highestStep;
                });

                previousStepButton?.classList.toggle('d-none', currentStep === 1);
                nextStepButton?.classList.toggle('d-none', currentStep === 3);
                submitStepButton?.classList.toggle('d-none', currentStep !== 3);

                form.querySelector('[data-form-step="' + currentStep + '"]')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            };

            previousStepButton?.addEventListener('click', function () {
                showStep(currentStep - 1);
            });

            nextStepButton?.addEventListener('click', function () {
                if (validateStep(currentStep)) {
                    showStep(currentStep + 1);
                }
            });

            stepLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    const target = Number(link.dataset.stepTarget);

                    if (target <= highestStep && (target < currentStep || validateStep(currentStep))) {
                        showStep(target);
                    }
                });
            });

            showStep(1);

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
                        const invalidStep = Number(firstInvalid.closest('[data-form-step]')?.dataset.formStep || 1);
                        showStep(invalidStep);
                        setTimeout(function () {
                            firstInvalid.focus({ preventScroll: true });
                        }, 250);
                    }
                }
            });
        });
    </script>
</x-layouts.app>
