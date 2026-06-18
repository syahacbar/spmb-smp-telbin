<x-layouts.app title="Login SPMB">
    <div class="auth-page login-auth-page d-flex align-items-center py-4 py-lg-5">
        <div class="container">
            <div class="row align-items-center justify-content-center g-4 g-xl-5">
                <div class="col-lg-6 auth-copy">
                    <div class="auth-school-badge mb-4">
                        <img src="{{ asset('images/logotelukbintuni.png') }}" alt="Logo Kabupaten Teluk Bintuni" class="auth-logo">
                        <div>
                            <div class="auth-kicker">Portal Resmi SPMB</div>
                            <div class="auth-school-name">SMP Kabupaten Teluk Bintuni</div>
                        </div>
                    </div>

                    <h1 class="fw-bold">Sistem Penerimaan Murid Baru (SPMB)</h1>
                    <p class="mt-3 mb-4">Selamat datang di portal pendaftaran SMP se-Kabupaten Teluk Bintuni.</p>

                    <div class="auth-info-grid">
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Login menggunakan NISN dan kata sandi</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Lengkapi biodata dan unggah dokumen persyaratan</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Cetak kartu pendaftaran sebagai bukti registrasi</span>
                        </div>
                    </div>

                    <p class="auth-note mb-0">
                        Mengalami kendala login? Hubungi panitia melalui
                        <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener"><strong>WhatsApp {{ $panitiaWhatsapp }}</strong></a>
                        untuk verifikasi akun.
                    </p>
                </div>

                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Login SPMB</div>
                                <h4 class="fw-bold mb-1">Selamat datang</h4>
                                <div class="text-muted small mb-3">Apakah anda belum punya akun SPMB? Klik daftar.</div>
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm">Daftar Akun SPMB</a>
                            </div>

                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if(session('warning'))
                                <div class="alert alert-warning">{{ session('warning') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <div class="fw-bold mb-1">Login belum berhasil.</div>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="small text-muted mb-3">Jika anda sudah punya akun, masukkan NISN dan kata sandi.</div>

                            <form method="post" action="{{ route('login.store') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">NISN</label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" inputmode="numeric" autocomplete="username" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <div class="input-group input-group-lg password-toggle-group">
                                        <input type="password" name="password" id="login-password" class="form-control" autocomplete="current-password" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="login-password" aria-label="Lihat password" aria-pressed="false">
                                            <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                            <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Captcha</label>
                                    <div class="captcha-box mb-2">
                                        <span class="text-muted small">Hitung</span>
                                        <span class="captcha-question">{{ session('login_captcha_question') }} = ?</span>
                                    </div>
                                    <input type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" required>
                                </div>

                                <button class="btn btn-outline-primary btn-lg w-100">Masuk</button>
                            </form>

                            <div class="text-center mt-4">
                                <a href="{{ route('landing') }}" class="small text-muted text-decoration-none">&larr; Kembali ke halaman beranda</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
