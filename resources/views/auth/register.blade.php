<x-layouts.app title="Daftar Akun">
    @php
        $showAccountFields = old('nisn') && ! $errors->has('nisn');
    @endphp
    <div class="auth-page register-auth-page d-flex align-items-center py-4 py-lg-5">
        <div class="container">
            <div class="row align-items-center justify-content-center g-4 g-xl-5">
                <div class="col-lg-6 auth-copy">
                    <div class="auth-school-badge mb-4">
                        <img src="{{ asset('images/logobintuni.jpeg') }}" alt="Logo" class="auth-logo">
                        <div>
                            <div class="auth-kicker">Portal Resmi SPMB</div>
                            <div class="auth-school-name">SMK Negeri 1 Bintuni</div>
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

                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Registrasi Akun</div>
                                <h4 class="fw-bold mb-1">Masukkan NISN</h4>
                                <div class="text-muted small">Periksa NISN sebelum melanjutkan pengisian akun.</div>
                            </div>

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

                            <form method="post" action="{{ route('register.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">NISN</label>
                                    <div class="input-group input-group-lg">
                                        <input type="hidden" name="nisn" value="{{ old('nisn') }}" data-register-nisn-hidden>
                                        <input type="text" value="{{ old('nisn') }}" class="form-control" inputmode="numeric" maxlength="10" autocomplete="username" data-register-nisn required>
                                        <button class="btn btn-outline-primary" type="button" data-check-nisn-url="{{ route('register.check-nisn') }}" aria-label="Lanjutkan cek NISN">Selanjutnya &rarr;</button>
                                    </div>
                                </div>

                                <div class="alert {{ $errors->has('nisn') ? 'alert-danger' : 'alert-info d-none' }}" data-nisn-result>
                                    @if($errors->has('nisn'))
                                        {{ $errors->first('nisn') }}
                                    @else
                                        Masukkan NISN lalu klik Selanjutnya.
                                    @endif
                                </div>

                                <div data-account-fields class="{{ $showAccountFields ? '' : 'd-none' }}">
                                    <div class="mb-3">
                                        <label class="form-label">Nomor WhatsApp Aktif</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">+62</span>
                                            <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="form-control" inputmode="numeric" placeholder="81234567890" @if($showAccountFields) required @endif>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Kata Sandi</label>
                                        <div class="input-group input-group-lg password-toggle-group">
                                            <input type="password" name="password" id="register-password" class="form-control" autocomplete="new-password" @if($showAccountFields) required @endif>
                                            <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="register-password" aria-label="Lihat kata sandi" aria-pressed="false">
                                                <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                                <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Konfirmasi Kata Sandi</label>
                                        <div class="input-group input-group-lg password-toggle-group">
                                            <input type="password" name="password_confirmation" id="register-password-confirmation" class="form-control" autocomplete="new-password" @if($showAccountFields) required @endif>
                                            <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="register-password-confirmation" aria-label="Lihat konfirmasi kata sandi" aria-pressed="false">
                                                <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                                <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Captcha</label>
                                        <div class="captcha-box mb-2">
                                            <span class="text-muted small">Hitung</span>
                                            <span class="captcha-question">{{ session('register_captcha_question') }} = ?</span>
                                        </div>
                                        <input type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" @if($showAccountFields) required @endif>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-lg flex-fill">Daftar Akun</button>
                                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg flex-fill">Kembali</a>
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
        const accountFields = document.querySelector('[data-account-fields]');

        function setAccountFieldsEnabled(enabled) {
            if (! accountFields) {
                return;
            }

            accountFields.classList.toggle('d-none', ! enabled);
            accountFields.querySelectorAll('input').forEach((input) => {
                input.required = enabled;
            });

            if (nisnInput) {
                nisnInput.disabled = enabled;
            }

            if (checkButton) {
                checkButton.classList.toggle('d-none', enabled);
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

            setAccountFieldsEnabled(false);
            if (resultBox) {
                resultBox.className = 'alert alert-info d-none';
            }
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
                    showNisnResult('success', data.message);
                    setAccountFieldsEnabled(true);
                    accountFields?.querySelector('input[name="no_wa"]')?.focus();
                } else {
                    showNisnResult(data.type === 'error' ? 'danger' : 'warning', data.message);
                    setAccountFieldsEnabled(false);
                }
            } catch (error) {
                showNisnResult('danger', 'Pengecekan NISN belum berhasil. Silakan coba lagi.');
                setAccountFieldsEnabled(false);
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
