<x-layouts.app title="Daftar Akun">
    @php
        $showAccountFields = old('nisn') && ! $errors->has('nisn');
    @endphp
    <style>
        .register-auth-page .register-shell {
            max-width: 1240px;
        }
        .register-auth-page .register-panel .card-body {
            padding: 1.5rem;
        }
        .register-account-grid {
            display: grid;
            gap: .85rem;
        }
        .register-section {
            border: 1px solid var(--telbin-line);
            border-radius: .8rem;
            background: #fff;
            padding: 1rem;
        }
        .register-section + .register-section {
            margin-top: .85rem;
        }
        .register-section-title {
            display: flex;
            align-items: center;
            gap: .65rem;
            margin-bottom: .85rem;
            color: #0f172a;
            font-size: .9rem;
            font-weight: 800;
        }
        .register-section-number {
            display: inline-flex;
            width: 1.75rem;
            height: 1.75rem;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #e4f3ed;
            color: var(--telbin-forest);
            font-size: .78rem;
        }
        .student-summary {
            border: 1px solid #bbf7d0;
            border-radius: .8rem;
            background: #f0fdf4;
            padding: .9rem 1rem;
        }
        .student-summary-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) repeat(2, minmax(0, 1fr));
            gap: .75rem 1rem;
        }
        .student-summary-item {
            min-width: 0;
        }
        .student-summary-item.student-school {
            grid-column: span 2;
        }
        .student-summary-label {
            display: block;
            margin-bottom: .12rem;
            color: #64748b;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .student-summary-value {
            display: block;
            overflow-wrap: anywhere;
            color: #166534;
            font-size: .9rem;
        }
        .register-location-note {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            background: #e4f3ed;
            color: var(--telbin-forest);
            padding: .35rem .65rem;
            font-size: .78rem;
            font-weight: 700;
        }
        .register-auth-page .form-label {
            margin-bottom: .35rem;
            font-size: .82rem;
            font-weight: 700;
        }
        .register-auth-page .form-control-lg,
        .register-auth-page .form-select-lg,
        .register-auth-page .input-group-lg > .form-control,
        .register-auth-page .input-group-lg > .form-select,
        .register-auth-page .input-group-lg > .input-group-text,
        .register-auth-page .input-group-lg > .btn {
            min-height: 46px;
            padding-top: .55rem;
            padding-bottom: .55rem;
            font-size: .95rem;
        }
        .register-auth-page textarea.form-control {
            min-height: 108px;
        }
        .register-actions {
            position: sticky;
            bottom: .75rem;
            z-index: 5;
            margin-top: 1rem;
            border: 1px solid var(--telbin-line);
            border-radius: .8rem;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 12px 30px rgba(6, 63, 53, .12);
            padding: .75rem;
            backdrop-filter: blur(10px);
        }
        .student-confirmation {
            border: 1px solid #a8d5c6;
            border-radius: .9rem;
            background: #f7fbf9;
            padding: 1rem;
        }
        .student-confirmation-message {
            border-left: 4px solid var(--telbin-forest);
            border-radius: .4rem;
            background: var(--telbin-soft);
            color: var(--telbin-forest-dark);
            padding: .75rem .85rem;
            font-size: .9rem;
        }
        .student-confirmation-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }
        @media (min-width: 992px) {
            .register-auth-page .auth-copy {
                position: sticky;
                top: 2rem;
            }
            .register-auth-page.is-account-stage {
                align-items: flex-start !important;
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
            .register-auth-page.is-account-stage .register-shell {
                max-width: 1240px;
            }
            .register-auth-page.is-account-stage .register-panel .card-body {
                padding: 1rem 1.25rem;
            }
            .register-auth-page.is-account-stage .register-heading {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
                margin-bottom: .75rem !important;
            }
            .register-auth-page.is-account-stage .register-heading .text-muted:last-child {
                display: none;
            }
            .register-auth-page.is-account-stage .register-heading h4 {
                margin: 0 !important;
            }
            .register-auth-page.is-account-stage .nisn-field {
                display: grid;
                grid-template-columns: 120px minmax(0, 1fr);
                align-items: center;
                gap: 1rem;
                margin-bottom: .65rem !important;
            }
            .register-auth-page.is-account-stage .nisn-field .form-label {
                margin: 0;
            }
            .register-auth-page.is-account-stage [data-nisn-result] {
                margin-bottom: .65rem;
                padding: .55rem .8rem;
                font-size: .84rem;
            }
            .register-auth-page.is-account-stage .student-summary {
                margin-bottom: .65rem !important;
                padding: .65rem .85rem;
            }
            .register-auth-page.is-account-stage .student-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: .5rem 1rem;
            }
            .register-auth-page.is-account-stage .student-summary .form-text {
                display: none;
            }
            .register-auth-page.is-account-stage .register-account-grid {
                grid-template-columns: 1fr;
            }
            .register-auth-page.is-account-stage .register-section {
                margin-top: 0;
                padding: .8rem;
            }
            .register-auth-page.is-account-stage .register-section-title {
                margin-bottom: .65rem;
            }
            .register-auth-page.is-account-stage .register-section .row {
                --bs-gutter-x: .75rem;
                --bs-gutter-y: .65rem;
            }
            .register-auth-page.is-account-stage textarea.form-control {
                min-height: 82px;
                height: 82px;
            }
            .register-auth-page.is-account-stage .form-text {
                margin-top: .2rem;
                font-size: .72rem;
            }
            .register-auth-page.is-account-stage .register-actions {
                position: static;
                justify-content: flex-end;
                margin-top: .65rem;
                padding: .55rem;
            }
            .register-auth-page.is-account-stage .register-actions .btn {
                flex: 0 0 auto !important;
                min-width: 180px;
                padding-top: .55rem;
                padding-bottom: .55rem;
                font-size: .95rem;
            }
        }
        @media (max-width: 767.98px) {
            .student-summary-grid {
                grid-template-columns: 1fr;
            }
            .student-summary-item.student-school {
                grid-column: auto;
            }
            .register-actions {
                position: static;
            }
        }
    </style>
    <div class="auth-page register-auth-page d-flex align-items-center py-4 py-lg-5 {{ $showAccountFields ? 'is-account-stage' : '' }}" data-register-page>
        <div class="container register-shell">
            <div class="row align-items-start justify-content-center g-4 g-xl-5">
                <div class="col-lg-4 auth-copy">
                    <div class="auth-school-badge mb-4">
                        <img src="{{ asset('images/logotelukbintuni.png') }}" alt="Logo Kabupaten Teluk Bintuni" class="auth-logo">
                        <div>
                            <div class="auth-kicker">Portal Resmi SPMB</div>
                            <div class="auth-school-name">SMP Kabupaten Teluk Bintuni</div>
                        </div>
                    </div>

                    <h1 class="fw-bold">Daftar Akun SPMB</h1>
                    <p class="mt-3 mb-4">Mulai dengan memeriksa <strong>NISN</strong>. Jika tersedia di database calon peserta didik, lanjutkan membuat akun menggunakan nomor WhatsApp dan kata sandi.</p>

                    <div class="auth-info-grid">
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Sistem memeriksa NISN sebelum akun dibuat</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Simpan kata sandi untuk login berikutnya</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Tunggu proses verifikasi akun oleh admin</span>
                        </div>
                    </div>

                    <p class="auth-note mt-4 mb-2">Sudah memiliki akun? Klik <a href="{{ route('login') }}">Login SPMB</a>.</p>
                    <p class="auth-note mb-0">NISN belum tersedia? Hubungi panitia melalui <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener">WhatsApp {{ $panitiaWhatsapp }}</a>.</p>
                </div>

                <div class="col-md-10 col-lg-8 register-form-column">
                    <div class="card auth-panel register-panel">
                        <div class="card-body">
                            <div class="mb-4 register-heading">
                                <div class="text-muted small text-uppercase fw-bold">Registrasi Akun</div>
                                <h4 class="fw-bold mb-1">Masukkan NISN</h4>
                                <div class="text-muted small">Periksa NISN sebelum melanjutkan pengisian akun.</div>
                            </div>

                            @unless($registrationServiceOpen ?? true)
                                <div class="alert alert-warning">
                                    <div class="fw-bold mb-1">Layanan pendaftaran sedang tutup.</div>
                                    <div>{{ $registrationServiceStatus['message'] ?? 'Silakan kembali pada jam pelayanan pendaftaran.' }}</div>
                                </div>
                            @endunless

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <div class="fw-bold mb-1">Pendaftaran belum dapat diproses.</div>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    @if($errors->has('nisn'))
                                        <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger mt-3">Hubungi Panitia via WhatsApp</a>
                                    @endif
                                </div>
                            @endif

                            <form method="post" action="{{ route('register.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3 nisn-field">
                                    <label class="form-label">NISN</label>
                                    <div class="input-group input-group-lg">
                                        <input type="hidden" name="nisn" value="{{ old('nisn') }}" data-register-nisn-hidden>
                                        <input type="text" value="{{ old('nisn') }}" class="form-control" inputmode="numeric" maxlength="10" autocomplete="username" data-register-nisn required @disabled(! ($registrationServiceOpen ?? true))>
                                        <button class="btn btn-outline-primary" type="button" data-check-nisn-url="{{ route('register.check-nisn') }}" aria-label="Lanjutkan cek NISN" @disabled(! ($registrationServiceOpen ?? true))>Selanjutnya &rarr;</button>
                                    </div>
                                </div>

                                <div class="alert {{ $errors->has('nisn') ? 'alert-danger' : 'alert-info d-none' }}" data-nisn-result>
                                    @if($errors->has('nisn'))
                                        {{ $errors->first('nisn') }}
                                    @else
                                        Masukkan NISN lalu klik Selanjutnya.
                                    @endif
                                </div>

                                <div class="student-confirmation d-none" data-confirmation-stage>
                                    <div class="student-summary mb-3">
                                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                            <div>
                                                <div class="small text-uppercase fw-bold text-primary">Konfirmasi Data Calon Siswa</div>
                                                <div class="fw-bold mt-1">Apakah data berikut sudah benar?</div>
                                            </div>
                                            <span class="badge text-bg-success">Terverifikasi Whitelist</span>
                                        </div>
                                        <div class="student-summary-grid">
                                            <div class="student-summary-item">
                                                <span class="student-summary-label">Nama</span>
                                                <strong class="student-summary-value" data-student-name>{{ $calonSiswa?->nama }}</strong>
                                            </div>
                                            <div class="student-summary-item">
                                                <span class="student-summary-label">Tempat Lahir</span>
                                                <strong class="student-summary-value" data-student-birthplace>{{ $calonSiswa?->tempat_lahir }}</strong>
                                            </div>
                                            <div class="student-summary-item">
                                                <span class="student-summary-label">Tanggal Lahir</span>
                                                <strong class="student-summary-value" data-student-birthdate>{{ $calonSiswa?->tanggal_lahir?->translatedFormat('d F Y') }}</strong>
                                            </div>
                                            <div class="student-summary-item student-school">
                                                <span class="student-summary-label">Asal Sekolah</span>
                                                <strong class="student-summary-value" data-student-school>{{ $calonSiswa?->asal_sekolah }}</strong>
                                            </div>
                                        </div>
                                        <div class="form-text mt-2">Identitas berasal dari whitelist resmi dan tidak dapat diubah.</div>
                                    </div>

                                    <div class="student-confirmation-message mb-3">
                                        Jika data sudah benar, silakan lanjut ke pengisian alamat domisili dan data akun. Jika terdapat kesalahan data, hubungi panitia agar dapat diperiksa terlebih dahulu.
                                    </div>

                                    <div class="student-confirmation-actions">
                                        <button type="button" class="btn btn-primary btn-lg" data-confirm-student>
                                            Data Benar, Lanjutkan
                                        </button>
                                        <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener" class="btn btn-outline-danger btn-lg">
                                            Data Salah, Hubungi Panitia
                                        </a>
                                        <button type="button" class="btn btn-link text-secondary" data-change-nisn>Ganti NISN</button>
                                    </div>
                                </div>

                                <div data-account-fields class="{{ $showAccountFields ? '' : 'd-none' }}">
                                    <div class="register-account-grid">
                                    <section class="register-section">
                                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-3">
                                            <div class="register-section-title mb-0">
                                                <span class="register-section-number">1</span>
                                                Domisili dan Kartu Keluarga
                                            </div>
                                            <span class="register-location-note">Kabupaten Teluk Bintuni</span>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Distrik/Kecamatan</label>
                                                <select name="kecamatan_id" class="form-select form-select-lg" data-kecamatan @if($showAccountFields) required @endif>
                                                    <option value="">Pilih distrik</option>
                                                    @foreach($kecamatanOptions as $kecamatan)
                                                        <option value="{{ $kecamatan->id }}" @selected((string) old('kecamatan_id') === (string) $kecamatan->id)>{{ $kecamatan->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Kelurahan/Kampung</label>
                                                <select name="kelurahan_id" class="form-select form-select-lg" data-kelurahan data-selected="{{ old('kelurahan_id') }}" @if($showAccountFields) required @endif>
                                                    <option value="">Pilih kampung</option>
                                                    @foreach($kelurahanOptions as $kelurahan)
                                                        <option value="{{ $kelurahan->id }}" data-kecamatan="{{ $kelurahan->kecamatan_id }}" @selected((string) old('kelurahan_id') === (string) $kelurahan->id)>{{ $kelurahan->nama }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label">Detail Alamat Domisili</label>
                                                <textarea name="detail_alamat" class="form-control" rows="3" placeholder="Jalan, RT/RW, patokan, atau detail alamat lainnya" @if($showAccountFields) required @endif>{{ old('detail_alamat') }}</textarea>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Upload Kartu Keluarga</label>
                                                <input type="file" name="kartu_keluarga" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" @if($showAccountFields) required @endif>
                                                <div class="form-text">PDF/gambar maksimal 4 MB untuk verifikasi domisili.</div>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="register-section">
                                        <div class="register-section-title">
                                            <span class="register-section-number">2</span>
                                            Kontak dan Keamanan Akun
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <label class="form-label">Nomor WhatsApp Aktif</label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text">+62</span>
                                                    <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="form-control" inputmode="numeric" placeholder="81234567890" @if($showAccountFields) required @endif>
                                                </div>
                                                <div class="form-text">Dipakai panitia untuk menghubungi calon siswa.</div>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Captcha</label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text fw-bold text-primary">{{ session('register_captcha_question') }} =</span>
                                                    <input type="number" name="captcha_answer" class="form-control" inputmode="numeric" placeholder="Hasil" @if($showAccountFields) required @endif>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Buat Kata Sandi</label>
                                                <div class="input-group input-group-lg password-toggle-group">
                                                    <input type="password" name="password" id="register-password" class="form-control" autocomplete="new-password" placeholder="Minimal 8 karakter" @if($showAccountFields) required @endif>
                                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="register-password" aria-label="Lihat kata sandi" aria-pressed="false">
                                                        <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                                        <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Konfirmasi Kata Sandi</label>
                                                <div class="input-group input-group-lg password-toggle-group">
                                                    <input type="password" name="password_confirmation" id="register-password-confirmation" class="form-control" autocomplete="new-password" placeholder="Ulangi kata sandi" @if($showAccountFields) required @endif>
                                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="register-password-confirmation" aria-label="Lihat konfirmasi kata sandi" aria-pressed="false">
                                                        <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                                        <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </section>
                                    </div>

                                    <div class="register-actions d-flex gap-2">
                                        <button class="btn btn-primary btn-lg flex-fill" @disabled(! ($registrationServiceOpen ?? true))>Daftar Akun</button>
                                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const nisnInput = document.querySelector('[data-register-nisn]');
        const nisnHidden = document.querySelector('[data-register-nisn-hidden]');
        const checkButton = document.querySelector('[data-check-nisn-url]');
        const resultBox = document.querySelector('[data-nisn-result]');
        const confirmationStage = document.querySelector('[data-confirmation-stage]');
        const confirmStudentButton = document.querySelector('[data-confirm-student]');
        const changeNisnButton = document.querySelector('[data-change-nisn]');
        const accountFields = document.querySelector('[data-account-fields]');
        const registerPage = document.querySelector('[data-register-page]');
        const studentName = document.querySelector('[data-student-name]');
        const studentBirthplace = document.querySelector('[data-student-birthplace]');
        const studentBirthdate = document.querySelector('[data-student-birthdate]');
        const studentSchool = document.querySelector('[data-student-school]');
        const kecamatanSelect = document.querySelector('[data-kecamatan]');
        const kelurahanSelect = document.querySelector('[data-kelurahan]');
        const kelurahanOptions = kelurahanSelect ? Array.from(kelurahanSelect.querySelectorAll('option[data-kecamatan]')) : [];

        function filterKelurahan() {
            const kecamatanId = kecamatanSelect?.value || '';
            kelurahanOptions.forEach((option) => {
                option.hidden = option.dataset.kecamatan !== kecamatanId;
                option.disabled = option.hidden;
            });

            const selected = kelurahanSelect?.selectedOptions[0];
            if (selected?.disabled) {
                kelurahanSelect.value = '';
            }
        }

        kecamatanSelect?.addEventListener('change', filterKelurahan);
        filterKelurahan();

        function setAccountFieldsEnabled(enabled) {
            if (! accountFields) {
                return;
            }

            accountFields.classList.toggle('d-none', ! enabled);
            registerPage?.classList.toggle('is-account-stage', enabled);
            accountFields.querySelectorAll('input, select, textarea').forEach((field) => {
                if (! field.disabled) {
                    field.required = enabled;
                }
            });

            if (nisnInput) {
                nisnInput.disabled = enabled;
            }

            if (checkButton) {
                checkButton.classList.toggle('d-none', enabled);
            }
        }

        function setConfirmationStage(enabled) {
            confirmationStage?.classList.toggle('d-none', ! enabled);

            if (nisnInput) {
                nisnInput.disabled = enabled;
            }

            if (checkButton) {
                checkButton.classList.toggle('d-none', enabled);
            }
        }

        function resetRegistrationStages() {
            setConfirmationStage(false);
            setAccountFieldsEnabled(false);
            registerPage?.classList.remove('is-account-stage');

            if (nisnInput) {
                nisnInput.disabled = false;
            }

            if (checkButton) {
                checkButton.classList.remove('d-none');
            }
        }

        function showNisnResult(type, message) {
            if (! resultBox) {
                return;
            }

            resultBox.className = `alert alert-${type}`;
            resultBox.textContent = message;
        }

        nisnInput?.addEventListener('input', () => {
            if (nisnHidden) {
                nisnHidden.value = nisnInput.value;
            }

            resetRegistrationStages();
            if (resultBox) {
                resultBox.className = 'alert alert-info d-none';
            }
        });

        confirmStudentButton?.addEventListener('click', () => {
            setConfirmationStage(false);
            setAccountFieldsEnabled(true);
            accountFields?.querySelector('select[name="kecamatan_id"]')?.focus();
        });

        changeNisnButton?.addEventListener('click', () => {
            resetRegistrationStages();
            if (resultBox) {
                resultBox.className = 'alert alert-info d-none';
            }
            nisnInput?.focus();
            nisnInput?.select();
        });

        checkButton?.addEventListener('click', async () => {
            const token = document.querySelector('input[name="_token"]')?.value || '';

            checkButton.disabled = true;
            checkButton.textContent = 'Memeriksa...';

            try {
                if (nisnHidden) {
                    nisnHidden.value = nisnInput?.value || '';
                }

                const response = await fetch(checkButton.dataset.checkNisnUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ nisn: nisnHidden?.value || nisnInput?.value || '' }),
                });
                const data = await response.json();

                if (data.ok) {
                    if (studentName) studentName.textContent = data.student?.nama || '-';
                    if (studentBirthplace) studentBirthplace.textContent = data.student?.tempat_lahir || '-';
                    if (studentBirthdate) studentBirthdate.textContent = data.student?.tanggal_lahir || '-';
                    if (studentSchool) studentSchool.textContent = data.student?.asal_sekolah || '-';
                    if (resultBox) {
                        resultBox.className = 'alert alert-info d-none';
                    }
                    setAccountFieldsEnabled(false);
                    setConfirmationStage(true);
                    confirmStudentButton?.focus();
                } else {
                    showNisnResult(data.type === 'error' ? 'danger' : 'warning', data.message);
                    resetRegistrationStages();
                }
            } catch (error) {
                showNisnResult('danger', 'Pengecekan NISN belum berhasil. Silakan coba lagi.');
                resetRegistrationStages();
            } finally {
                checkButton.disabled = false;
                checkButton.textContent = 'Selanjutnya →';
            }
        });

        if (accountFields && ! accountFields.classList.contains('d-none')) {
            if (nisnHidden && nisnInput) {
                nisnHidden.value = nisnInput.value;
            }

            setAccountFieldsEnabled(true);
        }
    </script>
</x-layouts.app>
