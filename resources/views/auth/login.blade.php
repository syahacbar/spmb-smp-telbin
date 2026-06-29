<x-layouts.app title="Login SPMB">
    <style>
        .login-auth-page {
            --login-forest: #075946;
            --login-forest-dark: #033c34;
            --login-sea: #0788a8;
            --login-gold: #ffc928;
            --login-blue: #0f4f94;
            --login-ink: #10233f;
            --login-muted: #52647d;
            min-height: 100vh;
            padding: 0;
            background:
                linear-gradient(180deg, rgba(226, 246, 251, .38) 0%, rgba(255, 255, 255, .18) 46%, rgba(4, 63, 53, .88) 100%),
                url("{{ asset('landing/assets/background1.webp') }}") center/cover fixed;
            color: var(--login-ink);
            overflow: hidden;
        }

        .login-auth-page::before,
        .login-auth-page::after {
            display: none;
        }

        .login-shell {
            position: relative;
            min-height: 100vh;
            padding-bottom: 28px;
        }

        .login-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255, 255, 255, .8) 0%, rgba(255, 255, 255, .4) 44%, rgba(255, 255, 255, .08) 100%),
                radial-gradient(circle at 70% 12%, rgba(255, 255, 255, .88), transparent 32%);
            pointer-events: none;
        }

        .login-hero {
            position: relative;
            z-index: 2;
            display: grid;
            justify-items: center;
            gap: 6px;
            min-height: 226px;
            padding: 22px clamp(18px, 4vw, 52px) 26px;
            text-align: center;
            color: var(--login-forest);
        }

        .login-hero::before {
            content: "";
            position: absolute;
            inset: 16px 50%;
            width: min(720px, 86vw);
            transform: translateX(-50%);
            border-radius: 999px;
            background:
                radial-gradient(circle at 50% 18%, rgba(255, 255, 255, .9), rgba(255, 255, 255, .28) 42%, transparent 70%),
                linear-gradient(90deg, transparent, rgba(7, 136, 168, .13), transparent);
            filter: blur(1px);
            pointer-events: none;
            z-index: -1;
        }

        .login-hero-logo {
            width: 82px;
            height: 82px;
            object-fit: contain;
            filter: drop-shadow(0 12px 16px rgba(3, 45, 38, .2));
        }

        .login-hero-welcome {
            color: var(--login-forest);
            font-family: "Brush Script MT", "Segoe Script", cursive;
            font-size: clamp(2.35rem, 5vw, 4rem);
            font-weight: 700;
            line-height: .95;
            text-shadow:
                0 2px 0 rgba(255, 255, 255, .82),
                0 12px 24px rgba(6, 63, 53, .18);
        }

        .login-hero-kicker {
            margin-top: 1px;
            color: #075946;
            font-size: clamp(1.1rem, 2.2vw, 1.85rem);
            font-weight: 1000;
            line-height: 1.05;
            text-transform: uppercase;
            text-shadow:
                0 2px 0 rgba(255, 255, 255, .88),
                0 12px 24px rgba(6, 63, 53, .16);
        }

        .login-hero-title {
            color: #0b5d4b;
            font-size: clamp(1rem, 1.8vw, 1.45rem);
            font-weight: 900;
            line-height: 1.2;
            text-shadow: 0 2px 0 rgba(255, 255, 255, .78);
        }

        .login-year-badge {
            display: inline-flex;
            margin-top: 5px;
            border: 1px solid rgba(255, 255, 255, .55);
            border-radius: 999px;
            background: var(--login-gold);
            color: #07382f;
            padding: 7px 16px;
            font-weight: 900;
            line-height: 1;
            box-shadow: 0 12px 22px rgba(242, 184, 75, .22);
        }

        .login-content {
            position: relative;
            z-index: 3;
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .quick-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0;
            margin: 12px 0 0;
            border: 1px solid rgba(207, 228, 220, .88);
            border-radius: 12px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 10px 22px rgba(16, 35, 63, .08);
            overflow: hidden;
            backdrop-filter: blur(12px);
        }

        .quick-info-item {
            display: grid;
            grid-template-columns: 40px 1fr;
            gap: 10px;
            align-items: center;
            width: 100%;
            min-height: 66px;
            padding: 10px 12px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: var(--login-ink);
            font: inherit;
            text-align: left;
            text-decoration: none;
            border-right: 1px solid #d9e7e4;
            cursor: pointer;
        }

        .quick-info-item:hover,
        .quick-info-item:focus {
            background: rgba(238, 247, 243, .72);
            outline: 0;
        }

        .quick-info-item:last-child {
            border-right: 0;
        }

        .quick-info-item:nth-child(2n) {
            border-right: 0;
        }

        .quick-info-item:nth-child(n + 3) {
            border-top: 1px solid #d9e7e4;
        }

        .quick-info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: #ffffff;
            font-size: 1.25rem;
            box-shadow: 0 10px 20px rgba(16, 35, 63, .15);
        }

        .quick-info-title {
            display: block;
            color: var(--login-ink);
            font-size: .88rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .quick-info-text {
            display: block;
            margin-top: 3px;
            color: #344054;
            font-size: .74rem;
            line-height: 1.4;
        }

        .login-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) minmax(340px, .96fr);
            gap: 16px;
            align-items: stretch;
        }

        .flow-card,
        .login-card {
            border: 1px solid rgba(207, 228, 220, .95);
            border-radius: 18px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 18px 36px rgba(16, 35, 63, .18);
            backdrop-filter: blur(14px);
        }

        .flow-card {
            padding: 24px 28px 18px;
        }

        .section-title-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 44px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--login-forest-dark), var(--login-forest));
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 900;
            text-transform: uppercase;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .16);
        }

        .flow-image-button {
            display: block;
            width: 100%;
            margin-top: 10px;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: zoom-in;
        }

        .flow-image-frame {
            display: grid;
            place-items: center;
            overflow: hidden;
            min-height: 330px;
            border: 1px solid #9fb8e8;
            border-radius: 10px;
            background: #ffffff;
            padding: 10px;
        }

        .flow-image-frame img {
            width: 100%;
            max-height: 430px;
            object-fit: contain;
            border-radius: 7px;
        }

        .zoom-hint {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            color: #344054;
            font-size: .88rem;
        }

        .login-card {
            padding: 24px 28px 18px;
        }

        .login-card-heading {
            text-align: center;
            margin-bottom: 16px;
        }

        .login-card-heading h1 {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 0;
            color: var(--login-forest);
            font-size: 1.35rem;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .login-card-heading p {
            margin: 7px 0 0;
            color: var(--login-muted);
            font-size: .85rem;
        }

        .login-card-note {
            margin: 0 0 14px;
            color: var(--login-muted);
            font-size: .9rem;
            line-height: 1.45;
            text-align: center;
        }

        .login-entry-note {
            display: grid;
            gap: 7px;
            margin: 16px 0 14px;
            border: 1px solid #d9e7e4;
            border-radius: 10px;
            background: rgba(238, 247, 243, .82);
            padding: 12px 14px;
            color: #344054;
            font-size: .88rem;
            line-height: 1.45;
        }

        .login-entry-note strong {
            color: var(--login-forest);
            font-weight: 900;
        }

        .login-card .form-label {
            margin-bottom: 5px;
            color: #172033;
            font-size: .86rem;
            font-weight: 900;
        }

        .login-card .form-control,
        .login-card .input-group .btn {
            min-height: 42px;
            border-color: #cdd7e3;
            border-radius: 8px;
            font-size: .95rem;
        }

        .login-card .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .login-card .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .login-card .password-toggle-group {
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }

        .login-card .password-toggle-group .input-with-icon {
            display: block;
            flex: 1 1 auto;
            min-width: 0;
        }

        .login-card .password-toggle-group .password-toggle {
            flex: 0 0 56px;
            width: 56px;
            min-width: 56px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon .bi {
            position: absolute;
            left: 13px;
            top: 50%;
            z-index: 2;
            transform: translateY(-50%);
            color: #667085;
            font-size: 1rem;
            pointer-events: none;
        }

        .input-with-icon .form-control {
            padding-left: 42px;
        }

        .captcha-row-login {
            display: grid;
            grid-template-columns: .9fr 1fr;
            gap: 10px;
        }

        .captcha-box {
            min-height: 42px;
            justify-content: center;
            border-color: #b7dfd0 !important;
            border-radius: 8px;
            background: #dff5ea !important;
            padding: 8px 12px;
        }

        .captcha-question {
            color: var(--login-forest);
            font-size: 1rem;
            font-weight: 900;
        }

        .login-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 44px;
            border: 0;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--login-forest-dark), var(--login-forest));
            color: #ffffff;
            font-size: 1rem;
            font-weight: 900;
            text-transform: uppercase;
            box-shadow: 0 12px 24px rgba(6, 63, 53, .22);
        }

        .login-divider {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 12px;
            align-items: center;
            margin: 13px 0;
            color: #667085;
            font-size: .84rem;
        }

        .login-divider::before,
        .login-divider::after {
            content: "";
            height: 1px;
            background: #d0d5dd;
        }

        .register-link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 42px;
            border: 1px solid rgba(255, 255, 255, .55);
            border-radius: 8px;
            color: #0f3f85 !important;
            font-weight: 900;
            text-decoration: none;
            text-transform: uppercase;
            background: var(--login-gold);
        }

        .back-home-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #667085 !important;
            font-size: .82rem;
            text-decoration: none;
        }

        .image-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(0, 0, 0, .9);
            cursor: zoom-out;
        }

        .image-modal.is-open {
            display: flex;
        }

        .image-modal-dialog {
            position: relative;
            max-width: min(1120px, 96vw);
            max-height: 92vh;
        }

        .image-modal-close {
            position: absolute;
            top: -46px;
            right: 0;
            border: 0;
            background: transparent;
            color: #ffffff;
            font-size: 36px;
            font-weight: 900;
            line-height: 1;
        }

        .image-modal img {
            display: block;
            max-width: 100%;
            max-height: 90vh;
            border-radius: 10px;
            background: #ffffff;
            box-shadow: 0 18px 50px rgba(0, 0, 0, .55);
        }

        .info-modal {
            position: fixed;
            inset: 0;
            z-index: 9998;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(3, 45, 38, .66);
            backdrop-filter: blur(7px);
        }

        .info-modal.is-open {
            display: flex;
        }

        .info-modal-dialog {
            width: min(620px, 94vw);
            overflow: hidden;
            border: 1px solid rgba(207, 228, 220, .95);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(3, 45, 38, .32);
        }

        .info-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 20px 10px;
            background: linear-gradient(135deg, var(--login-forest-dark), var(--login-forest));
            color: 0 24px 70px rgba(3, 45, 38, .32);
        }

        .info-modal-title {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .info-modal-close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            flex: 0 0 auto;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            color: 0 24px 70px rgba(3, 45, 38, .32);
            font-size: 1.35rem;
            line-height: 1;
        }

        .info-modal-body {
            display: grid;
            gap: 12px;
            padding: 8px 20px 20px;
        }

        .info-list {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .info-list li {
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: 10px;
            align-items: start;
            border: 1px solid #d9e7e4;
            border-radius: 10px;
            background: #f8fbfa;
            padding: 10px 12px;
            color: #344054;
            font-size: .92rem;
            line-height: 1.45;
        }

        .info-list span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: #e4f3ed;
            color: var(--login-forest);
            font-weight: 900;
        }

        .info-list strong {
            display: block;
            color: var(--login-ink);
            font-weight: 900;
        }

        .login-auth-page .alert {
            border-radius: 8px;
            font-size: .86rem;
            padding: 10px 12px;
        }

        @media (max-width: 1199.98px) {
            .login-main-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 767.98px) {
            .login-shell {
                padding-bottom: 0;
            }

            .login-hero {
                min-height: auto;
                padding: 18px 14px 22px;
            }

            .login-hero-logo {
                width: 64px;
                height: 64px;
            }

            .login-hero-kicker,
            .login-hero-title {
                max-width: 22rem;
            }

            .login-content {
                margin-top: 0;
                padding: 0 14px 18px;
            }

            .quick-info {
                grid-template-columns: 1fr;
            }

            .quick-info-item {
                border-right: 0;
                border-bottom: 1px solid #d9e7e4;
            }

            .quick-info-item:nth-child(n + 3) {
                border-top: 0;
            }

            .quick-info-item:last-child {
                border-bottom: 0;
            }

            .flow-image-frame {
                min-height: 210px;
            }

            .login-card {
                padding: 20px 16px 16px;
            }

            .captcha-row-login {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="auth-page login-auth-page">
        <div class="login-shell">
            <section class="login-hero" aria-label="Portal SPMB SMP Kabupaten Teluk Bintuni">
                <img class="login-hero-logo" src="{{ asset('landing/assets/logo_telbin_new.webp') }}" alt="Logo Kabupaten Teluk Bintuni">
                <div class="login-hero-welcome">Selamat Datang</div>
                <div class="login-hero-kicker">di Portal Resmi SPMB Online</div>
                <div class="login-hero-title">Jenjang SMP di Kabupaten Teluk Bintuni</div>
                <div class="login-year-badge">Tahun Ajaran 2026/2027</div>
            </section>

            <div class="login-content">
                <div class="login-main-grid">
                    <section class="flow-card" aria-labelledby="alur-title">
                        <h2 class="section-title-bar" id="alur-title"><i class="bi bi-clipboard-check-fill"></i> Alur Pendaftaran SPMB</h2>
                        <button class="flow-image-button" type="button" data-open-image="{{ asset('images/alur_pendaftaran.webp') }}" title="Klik untuk memperbesar">
                            <span class="flow-image-frame">
                                <img src="{{ asset('images/alur_pendaftaran.webp') }}" alt="Alur Pendaftaran SPMB">
                            </span>
                        </button>
                        <div class="zoom-hint"><i class="bi bi-search"></i> Klik gambar untuk memperbesar</div>

                        <div class="quick-info" aria-label="Informasi pendaftaran">
                            <button class="quick-info-item" type="button" data-info-modal-target="jadwalSpmbModal">
                                <span class="quick-info-icon" style="background: linear-gradient(135deg, #1d4ed8, #f59e0b);"><i class="bi bi-calendar2-week"></i></span>
                                <span>
                                    <span class="quick-info-title">Jadwal Kegiatan SPMB</span>
                                    <span class="quick-info-text">Lihat rangkaian kegiatan SPMB TA 2026/2027</span>
                                </span>
                            </button>
                            <button class="quick-info-item" type="button" data-info-modal-target="jalurPenerimaanModal">
                                <span class="quick-info-icon" style="background: linear-gradient(135deg, #f97316, #0f766e);"><i class="bi bi-buildings"></i></span>
                                <span>
                                    <span class="quick-info-title">Jalur Penerimaan</span>
                                    <span class="quick-info-text">Domisili, Prestasi, Afirmasi dan Mutasi</span>
                                </span>
                            </button>
                            <a class="quick-info-item" href="{{ route('juknis.download') }}" download="juknis-spmb-smp-teluk-bintuni-2026.pdf">
                                <span class="quick-info-icon" style="background: linear-gradient(135deg, #1d4ed8, #0f4f94);"><i class="bi bi-file-earmark-text"></i></span>
                                <span>
                                    <span class="quick-info-title">Petunjuk Teknis (Juknis)</span>
                                    <span class="quick-info-text">Unduh dokumen resmi sebagai panduan</span>
                                </span>
                            </a>
                            <a class="quick-info-item" href="{{ $panitiaWhatsappUrl }}" target="_blank" rel="noopener">
                                <span class="quick-info-icon" style="background: linear-gradient(135deg, #22c55e, #16a34a);"><i class="bi bi-whatsapp"></i></span>
                                <span>
                                    <span class="quick-info-title">Bantuan & Informasi</span>
                                    <span class="quick-info-text">Hubungi panitia melalui WhatsApp</span>
                                </span>
                            </a>
                        </div>
                    </section>

                    <section class="login-card" aria-labelledby="login-title">
                        <div class="login-card-heading">
                        <h2 class="section-title-bar" id="login-title"><i class="bi bi-shield-lock-fill"></i> Akses SPMB Online</h2>

                        </div>

                        <p class="login-card-note">Belum punya akun? Klik tombol <strong>Buat Akun</strong> dibawah ini</p>
                        <a href="{{ route('register') }}" class="register-link-btn w-100"><i class="bi bi-person-plus-fill"></i> Buat Akun</a>

                        <div class="login-entry-note">
                            <div>Jika sudah memiliki akun, silakan masuk menggunakan <strong>NISN</strong> dan kata sandi Anda.</div>
                            <div>Khusus Admin Dinas dan Sekolah, silakan menggunakan <strong>username</strong> pada kolom yang sama.</div>
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

                        <form method="post" action="{{ route('login.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="login-nisn">NISN / Username</label>
                                <div class="input-with-icon">
                                    <i class="bi bi-person"></i>
                                    <input id="login-nisn" type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" autocomplete="username" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="login-password">Password</label>
                                <div class="input-group input-group-lg password-toggle-group">
                                    <span class="input-with-icon flex-grow-1">
                                        <i class="bi bi-lock"></i>
                                        <input type="password" name="password" id="login-password" class="form-control" autocomplete="current-password" required>
                                    </span>
                                    <button class="btn btn-outline-secondary password-toggle" type="button" data-password-toggle="login-password" aria-label="Lihat password" aria-pressed="false">
                                        <span class="password-icon password-icon-eye" aria-hidden="true"></span>
                                        <span class="password-icon password-icon-eye-off" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="login-captcha">Captcha</label>
                                <div class="captcha-row-login">
                                    <div class="captcha-box">
                                        <span class="captcha-question">{{ session('login_captcha_question') }} = ?</span>
                                    </div>
                                    <input id="login-captcha" type="number" name="captcha_answer" class="form-control form-control-lg" inputmode="numeric" placeholder="Masukkan hasil" required>
                                </div>
                            </div>

                            <button class="login-submit w-100" type="submit"><i class="bi bi-lock-fill"></i> Masuk</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('landing') }}" class="back-home-link"><i class="bi bi-arrow-left"></i> Kembali ke halaman beranda</a>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>

    <div class="image-modal" id="imageModal" aria-hidden="true">
        <div class="image-modal-dialog">
            <button class="image-modal-close" type="button" aria-label="Tutup gambar">&times;</button>
            <img id="modalImage" src="" alt="Alur Pendaftaran SPMB ukuran penuh">
        </div>
    </div>

    <div class="info-modal" id="jadwalSpmbModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="jadwalSpmbTitle">
        <div class="info-modal-dialog">
            <div class="info-modal-header">
                <h2 class="info-modal-title" id="jadwalSpmbTitle">Jadwal Kegiatan SPMB</h2>            
                <button class="info-modal-close" type="button" data-info-modal-close aria-label="Tutup informasi">&times;</button>
            </div>
            <div class="info-modal-body">
                <ul class="info-list">
                    <li><span>1</span><div><strong>Pendaftaran Online : 01 - 06 Juli 2026</strong>Pengisian akun dan data calon murid melalui portal SPMB.</div></li>
                    <li><span>2</span><div><strong>Verifikasi Berkas : 07 - 09 Juli 2026</strong>Proses pemeriksaan dan validasi data serta dokumen persyaratan oleh panitia.</div></li>
                    <li><span>3</span><div><strong>Pengolahan Data Pendaftar : 10 Juli 2026</strong>Proses seleksi dan pengolahan data sesuai jalur penerimaan yang dipilih.</div></li>
                    <li><span>4</span><div><strong>Pengumuman Hasil : 11 Juli 2026</strong>Hasil seleksi diumumkan melalui sistem SPMB.</div></li>
                    <li><span>5</span><div><strong>Daftar Ulang : 13 Juli 2026</strong>Proses daftar ulang bagi calon murid yang dinyatakan diterima sesuai ketentuan yang berlaku.</div></li>
                    <li><span>6</span><div><strong>Masa Pengenalan Lingkungan Sekolah : 15 - 17 Juli 2026</strong>Kegiatan pengenalan lingkungan sekolah bagi peserta didik baru yang telah diterima.</div></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="info-modal" id="jalurPenerimaanModal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="jalurPenerimaanTitle">
        <div class="info-modal-dialog">
            <div class="info-modal-header">
                <h2 class="info-modal-title" id="jalurPenerimaanTitle">Jalur Penerimaan</h2>
                <button class="info-modal-close" type="button" data-info-modal-close aria-label="Tutup informasi">&times;</button>
            </div>
            <div class="info-modal-body">
                <ul class="info-list">
                    <li><span>1</span><div><strong>Domisili</strong>Ditujukan bagi calon murid yang memilih sekolah sesuai dengan domisili pada Kartu Keluarga (KK) yang telah diverifikasi.</div></li>
                    <li><span>2</span><div><strong>Prestasi</strong>Ditujukan bagi calon murid yang memilih sekolah di luar domisili. Pemeringkatan berdasarkan nilai TKA.</div></li>
                    <li><span>3</span><div><strong>Afirmasi</strong>Ditujukan bagi calon murid dari keluarga tidak mampu, penyandang disabilitas dan/atau kelompok khusus lainnya sesuai ketentuan. Wajib unggah surat keterangan yang valid.</div></li>
                    <li><span>4</span><div><strong>Mutasi</strong>Ditujukan bagi calon murid yang orang tuanya mengalami perpindahan tugas/pekerjaan. Wajib unggah surat keterangan yang valid</div></li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalClose = imageModal?.querySelector('.image-modal-close');

        document.querySelectorAll('[data-open-image]').forEach((button) => {
            button.addEventListener('click', () => {
                if (! imageModal || ! modalImage) {
                    return;
                }

                const image = button.querySelector('img');
                modalImage.src = image?.currentSrc || button.dataset.openImage;
                imageModal.classList.add('is-open');
                imageModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            });
        });

        function closeModal() {
            if (! imageModal || ! modalImage) {
                return;
            }

            imageModal.classList.remove('is-open');
            imageModal.setAttribute('aria-hidden', 'true');
            modalImage.src = '';
            document.body.style.overflow = '';
        }

        modalClose?.addEventListener('click', closeModal);
        imageModal?.addEventListener('click', (event) => {
            if (event.target === imageModal) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal();
                closeInfoModals();
            }
        });

        const infoModals = document.querySelectorAll('.info-modal');

        function openInfoModal(modal) {
            if (! modal) {
                return;
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeInfoModals() {
            infoModals.forEach((modal) => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            });

            if (! imageModal?.classList.contains('is-open')) {
                document.body.style.overflow = '';
            }
        }

        document.querySelectorAll('[data-info-modal-target]').forEach((button) => {
            button.addEventListener('click', () => {
                openInfoModal(document.getElementById(button.dataset.infoModalTarget));
            });
        });

        document.querySelectorAll('[data-info-modal-close]').forEach((button) => {
            button.addEventListener('click', closeInfoModals);
        });

        infoModals.forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeInfoModals();
                }
            });
        });
    </script>
</x-layouts.app>
