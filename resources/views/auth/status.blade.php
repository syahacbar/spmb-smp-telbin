<x-layouts.app title="Cek Status SPMB">
    <div class="auth-page login-auth-page d-flex align-items-center py-4 py-lg-5">
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

                    <h1 class="fw-bold">Cek Status SPMB</h1>
                    <p class="mt-3 mb-4">Silakan masukkan NISN dan kode keamanan (Captcha) untuk mengetahui status pendaftaran akun SPMB.</p>

                    <div class="auth-info-grid">
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Cek apakah NISN sudah memiliki akun SPMB</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Lanjutkan ke login jika akun sudah terdaftar</span>
                        </div>
                        <div class="auth-feature">
                            <span class="auth-feature-mark">&#10003;</span>
                            <span>Daftar akun jika NISN tersedia tetapi belum terdaftar</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="card auth-panel">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <div class="text-muted small text-uppercase fw-bold">Cek Status</div>
                                <h4 class="fw-bold mb-1">Status Akun SPMB</h4>
                                <div class="text-muted small">Masukkan NISN untuk mengecek status akun.</div>
                            </div>

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <div class="fw-bold mb-1">Pengecekan belum berhasil.</div>
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session('status_result'))
                                <div class="alert {{ session('status_result') === 'registered' ? 'alert-success' : (session('status_result') === 'not_registered' ? 'alert-info' : 'alert-warning') }}">
                                    @if(session('status_result') === 'registered')
                                        <div class="fw-bold mb-1">NISN telah terdaftar di SPMB.</div>
                                        <div>Silakan login untuk melanjutkan proses pendaftaran.</div>
                                        <a href="{{ route('login') }}" class="btn btn-success btn-sm mt-3">LOGIN SPMB</a>
                                    @elseif(session('status_result') === 'not_registered')
                                        <div class="fw-bold mb-1">NISN belum terdaftar di SPMB.</div>
                                        <div>Silakan melakukan pendaftaran akun terlebih dahulu untuk mengikuti proses SPMB.</div>
                                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm mt-3">DAFTAR AKUN SPMB</a>
                                    @else
                                        <div class="fw-bold mb-1">NISN yang Anda masukkan tidak ditemukan.</div>
                                        <div>Pastikan NISN telah sesuai dan coba kembali.</div>
                                    @endif
                                </div>
                            @endif

                            <form method="post" action="{{ route('status.check') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">NISN</label>
                                    <input type="text" name="nisn" value="{{ old('nisn', session('status_nisn')) }}" class="form-control form-control-lg" inputmode="numeric" maxlength="10" autocomplete="username" required autofocus>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Captcha</label>
                                    <div class="captcha-box mb-2">
                                        <span class="text-muted small">Hitung</span>
                                        <span class="captcha-question">{{ session('status_captcha_question') }} = ?</span>
                                    </div>
                                    <input type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" required>
                                </div>

                                <button class="btn btn-primary btn-lg w-100">Cek Status</button>
                            </form>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Login SPMB</a>
                                <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Daftar akun</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
