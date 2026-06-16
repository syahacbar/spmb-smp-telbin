<x-layouts.app title="Daftar Akun">
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
                    <p class="mt-3 mb-4">Lengkapi data pendaftaran menggunakan <strong>NISN</strong> yang sudah tersedia di database calon peserta didik, <strong>email aktif</strong>, dan <strong>nomor WhatsApp aktif</strong>.</p>

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

                    <p class="auth-note mt-4 mb-2">Sudah pernah mendaftar akun? Klik <a href="{{ route('status') }}">Cek Status SPMB</a>.</p>
                    <p class="auth-note mb-0">NISN belum tersedia? Hubungi panitia melalui <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener">WhatsApp {{ $panitiaWhatsapp }}</a>.</p>
                </div>

                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Registrasi Akun</div>
                                <h4 class="fw-bold mb-1">Buat akun baru</h4>
                                <div class="text-muted small">Isi data akun awal untuk masuk ke layanan SPMB.</div>
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
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" inputmode="numeric" maxlength="10" autocomplete="username" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Aktif</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" autocomplete="email" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nomor WhatsApp Aktif</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text">+62</span>
                                        <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="form-control" inputmode="numeric" placeholder="81234567890" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kata Sandi</label>
                                    <div class="input-group input-group-lg password-toggle-group">
                                        <input type="password" name="password" id="register-password" class="form-control" autocomplete="new-password" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="register-password" aria-label="Lihat kata sandi" aria-pressed="false">
                                            <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                            <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Kata Sandi</label>
                                    <div class="input-group input-group-lg password-toggle-group">
                                        <input type="password" name="password_confirmation" id="register-password-confirmation" class="form-control" autocomplete="new-password" required>
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
                                    <input type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" required>
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-lg flex-fill">Daftar Akun</button>
                                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg flex-fill">Kembali</a>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="{{ route('status') }}" class="fw-bold text-decoration-none">Cek Status SPMB</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
