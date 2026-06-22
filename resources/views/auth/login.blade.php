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

                    <!-- Alur Pendaftaran (Gambar) -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold text-white mb-0" style="font-size: 1.1rem;">Alur Pendaftaran</h5>
                            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.75rem;">Klik untuk memperbesar</span>
                        </div>
                        <div class="bg-white p-2 rounded shadow-lg overflow-hidden d-flex align-items-center justify-content-center" 
                             style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; border: 2px solid rgba(255,255,255,0.3);"
                             onclick="openModal(this.querySelector('img').src)"
                             onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 8px 30px rgba(0,0,0,0.35)';"
                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.15)';"
                             title="Klik untuk memperbesar">
                            <img src="{{ asset('ALUR.jpeg') }}" 
                                 onerror="this.onerror=null; this.src='{{ asset('images/alur_pendaftaran.jpeg') }}';" 
                                 alt="Alur Pendaftaran SPMB" 
                                 class="img-fluid rounded" 
                                 style="object-fit: contain; width: 100%; max-height: 420px;">
                        </div>
                    </div>

                    <!-- Unduh Juknis -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-white mb-2" style="font-size: 1.1rem;">Petunjuk Teknis (Juknis)</h5>
                        <p class="small text-white-50 mb-3">Silakan unduh dokumen Petunjuk Teknis SPMB Tahun 2026 Kabupaten Teluk Bintuni untuk panduan lengkap pendaftaran.</p>
                        <a href="{{ asset('juknis_spmb_tahun_2026_teluk_bintuni.pdf') }}" 
                           class="btn btn-warning fw-bold d-inline-flex align-items-center gap-2 px-4 py-2.5 shadow-sm" 
                           download 
                           style="border-radius: 0.5rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-file-earmark-pdf-fill" viewBox="0 0 16 16">
                                <path d="M5.523 12.424c.14-.082.293-.162.459-.238a7.878 7.878 0 0 1-.45.606c-.28.33-.52.625-.718.847a.5.5 0 0 0 .03.677c.224.209.497.188.762-.054.21-.193.425-.464.652-.779.161-.225.326-.475.49-.75.485-.06.967-.109 1.439-.145.496-.03.977-.045 1.437-.045 1.485 0 2.461.408 2.747 1.102a.5.5 0 0 0 .9-.166c.208-.74-.015-1.545-.633-2.174-.302-.307-.71-.502-1.192-.587a19.645 19.645 0 0 0-3.522-.176 11.728 11.728 0 0 0-1.636-.773c-.25-.104-.49-.205-.72-.303a31.317 31.317 0 0 0-1.137-1.15c-.29-.279-.587-.565-.888-.856-.452-.437-.908-.82-1.353-1.127a3.188 3.188 0 0 0-1.745-.689 1.5 1.5 0 0 0-1.6 1.096c-.139.492.01.996.347 1.417.305.38.711.74 1.18 1.082.026.02.05.039.076.058.4.3.882.59 1.416.864.237.121.49.24.76.357.084.037.17.073.252.11a11.603 11.603 0 0 0 1.152 2.308c-.29.56-.59 1.101-.9 1.625-.315.537-.627 1.037-.93 1.49-.282.423-.556.82-.823 1.176a.5.5 0 0 0 .079.68c.216.175.49.162.748-.04.232-.181.479-.452.738-.79A13.783 13.783 0 0 0 5.52 12.42z"/>
                                <path d="M8 5a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            </svg>
                            Unduh Juknis (PDF)
                        </a>
                    </div>

                    <p class="auth-note mb-0">
                        Mengalami kendala login? Hubungi panitia melalui
                        <a href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener"><strong>WhatsApp {{ $panitiaWhatsapp }}</strong></a>
                        untuk verifikasi akun.
                    </p>
                </div>

                <div class="col-md-8 col-lg-6 col-xl-4">
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

                            <div class="small text-muted mb-1">Jika anda sudah punya akun, masukkan NISN dan kata sandi.</div>
                            <div class="small text-muted mb-3">Admin Dinas dan Sekolah dapat menggunakan username pada kolom yang sama.</div>

                            <form method="post" action="{{ route('login.store') }}">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">NISN / Username</label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" autocomplete="username" required autofocus>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">Password</label>
                                    <div class="input-group input-group-lg password-toggle-group">
                                        <input type="password" name="password" id="login-password" class="form-control" autocomplete="current-password" required>
                                        <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="login-password" aria-label="Lihat password" aria-pressed="false">
                                            <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                            <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-2">
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

    <!-- Modal Fullscreen Pop-up -->
    <div id="imageModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.9); align-items: center; justify-content: center; cursor: zoom-out;" onclick="closeModal()">
        <div style="position: relative; max-width: 95%; max-height: 95%;">
            <span style="position: absolute; top: -45px; right: 10px; color: #fff; font-size: 35px; font-weight: bold; cursor: pointer; user-select: none;">&times;</span>
            <img id="modalImage" style="max-width: 100%; max-height: 90vh; border-radius: 0.5rem; box-shadow: 0 4px 25px rgba(0,0,0,0.6); transition: transform 0.2s ease-in-out;">
        </div>
    </div>

    <script>
        function openModal(imgSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = imgSrc;
            document.body.style.overflow = 'hidden'; // Disable background scroll
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Enable background scroll
        }
    </script>
</x-layouts.app>
