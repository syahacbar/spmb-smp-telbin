@php
    $whatsapp = (string) ($whatsapp ?? config('services.spmb.panitia_whatsapp', '6281111110002'));
    $whatsappPhone = preg_replace('/\D+/', '', $whatsapp);
    $whatsappMessage = 'Halo Admin SPMB SMP Kabupaten Teluk Bintuni, saya ingin bertanya tentang pendaftaran SPMB.';
    $whatsappUrl = 'https://wa.me/'.$whatsappPhone.'?text='.rawurlencode($whatsappMessage);
    $hasNewLandingLogo = file_exists(public_path('landing/assets/logo_telbin_new.png'));
    $landingLogo = $hasNewLandingLogo
        ? asset('landing/assets/logo_telbin_new.png')
        : asset('images/logotelukbintuni.png');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPMB SMP Kabupaten Teluk Bintuni</title>
    <style>
        html {
            scroll-behavior: smooth;
            --telbin-forest: #0b5d4b;
            --telbin-forest-dark: #063f35;
            --telbin-lagoon: #0788a8;
            --telbin-gold: #f2b84b;
            --telbin-ink: #12372f;
            --telbin-soft: #eef7f3;
            --telbin-line: #cfe4dc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
        }

        body {
            background: #f3f8f5;
            color: var(--telbin-ink);
            font-family: Poppins, "Segoe UI", Arial, sans-serif;
        }

        a {
            text-decoration: none;
        }

        .hero {
            position: relative;
            min-height: 75vh;
            color: #ffffff;
            background:
                linear-gradient(105deg, rgba(3, 45, 38, .88) 0%, rgba(5, 92, 76, .66) 46%, rgba(7, 81, 93, .28) 100%),
                url("{{ asset('landing/assets/tugu7suku.png') }}") center 48%/cover;
            overflow: hidden;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px 40px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0;
            text-shadow: 0 2px 12px rgba(0, 0, 0, .35);
        }

        .brand-logo {
            display: inline-flex;
            width: 68px;
            height: 68px;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 18px;
            background: linear-gradient(145deg, rgba(11, 93, 75, .76), rgba(7, 136, 168, .48));
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, .24),
                0 12px 28px rgba(0, 0, 0, .24);
            backdrop-filter: blur(10px);
        }

        .brand img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            clip-path: polygon(10% 0, 90% 0, 97% 12%, 94% 68%, 82% 83%, 50% 100%, 18% 83%, 6% 68%, 3% 12%);
            filter: drop-shadow(0 5px 7px rgba(0, 0, 0, .32));
        }

        .brand-logo.is-transparent {
            border: 0;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
        }

        .brand-logo.is-transparent img {
            width: 66px;
            height: 66px;
            clip-path: none;
            filter: drop-shadow(0 7px 9px rgba(0, 0, 0, .34));
        }

        .menu {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .menu a {
            color: #ffffff;
            font-weight: 700;
            padding: 9px 8px;
        }

        .menu .login {
            border: 1px solid rgba(255, 255, 255, .9);
            border-radius: 10px;
            padding: 10px 16px;
        }

        .content {
            position: relative;
            z-index: 2;
            max-width: 610px;
            padding: 64px 0 130px 80px;
        }

        .content h1 {
            font-size: clamp(48px, 7vw, 86px);
            line-height: .92;
            font-weight: 900;
            letter-spacing: 0;
        }

        .yellow {
            color: var(--telbin-gold);
        }

        .content h3 {
            margin: 18px 0 0;
            font-size: 28px;
            font-weight: 800;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .hero-cta,
        .hero-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 52px;
            border-radius: 12px;
            font-weight: 900;
        }

        .hero-cta {
            background: var(--telbin-gold);
            color: var(--telbin-forest-dark);
            padding: 15px 24px;
            box-shadow: 0 16px 34px rgba(0, 0, 0, .24);
        }

        .hero-link {
            border: 1px solid rgba(255, 255, 255, .78);
            color: #ffffff;
            padding: 14px 18px;
            backdrop-filter: blur(8px);
        }

        .hero-countdown {
            position: absolute;
            right: 80px;
            top: 180px;
            z-index: 3;
            max-width: 420px;
        }

        .count {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .box {
            min-width: 90px;
            padding: 15px;
            border-radius: 14px;
            background: rgba(255, 255, 255, .15);
            text-align: center;
            backdrop-filter: blur(8px);
        }

        .box b {
            display: block;
            font-size: 30px;
            line-height: 1;
        }

        .section {
            padding: 58px 20px;
        }

        .wrap {
            max-width: 1200px;
            margin: auto;
        }

        h2 {
            margin-bottom: 24px;
            font-size: 40px;
            text-align: center;
        }

        #banner {
            padding-top: 48px;
            padding-bottom: 42px;
        }

        #banner .wrap {
            max-width: 1040px;
        }

        .banner-frame {
            overflow: hidden;
            border-radius: 16px;
            background: #ffffff;
            padding: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .10);
        }

        .banner-button {
            display: block;
            width: 100%;
            border: 0;
            background: transparent;
            cursor: zoom-in;
            padding: 0;
        }

        .banner-frame img {
            display: block;
            width: 100%;
            max-height: 620px;
            border-radius: 12px;
            object-fit: contain;
            transition: transform .3s ease;
        }

        .banner-frame img:hover {
            transform: scale(1.01);
        }

        .timeline {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .timeline .item,
        .card {
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .timeline .item {
            border-top: 5px solid var(--telbin-forest);
            padding: 22px;
            text-align: center;
            transition: transform .3s ease;
        }

        .timeline .item:hover,
        .quota-card:hover {
            transform: translateY(-6px);
        }

        .timeline .schedule-icon {
            display: inline-grid;
            place-items: center;
            width: 64px;
            height: 64px;
            margin-bottom: 12px;
            border-radius: 15px;
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .16);
        }

        .timeline .schedule-icon svg {
            width: 34px;
            height: 34px;
        }

        .schedule-icon.online {
            background: linear-gradient(135deg, var(--telbin-forest), var(--telbin-lagoon));
        }

        .schedule-icon.interview {
            background: linear-gradient(135deg, #0f766e, #65a30d);
        }

        .schedule-icon.announcement {
            background: linear-gradient(135deg, #d97706, var(--telbin-gold));
        }

        .schedule-icon.reregistration {
            background: linear-gradient(135deg, #16a34a, #14b8a6);
        }

        .schedule-icon.orientation {
            background: linear-gradient(135deg, var(--telbin-lagoon), var(--telbin-forest-dark));
        }

        .timeline h3,
        .timeline h4 {
            margin-bottom: 8px;
            font-size: 18px;
        }

        .timeline p,
        .card {
            font-size: 16px;
            line-height: 1.55;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            padding: 22px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        #persyaratan {
            background: linear-gradient(180deg, #ffffff 0%, var(--telbin-soft) 100%);
        }

        .requirements-layout {
            display: grid;
            grid-template-columns: minmax(260px, .9fr) minmax(320px, 1.4fr);
            gap: 24px;
            align-items: stretch;
        }

        .requirements-note {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
            min-height: 100%;
            border-radius: 18px;
            background: linear-gradient(145deg, var(--telbin-forest-dark), var(--telbin-forest));
            color: #ffffff;
            padding: 28px;
            box-shadow: 0 14px 34px rgba(6, 63, 53, .22);
        }

        .requirements-note h3 {
            margin-bottom: 12px;
            font-size: 26px;
            line-height: 1.2;
        }

        .requirements-note p {
            color: rgba(255, 255, 255, .86);
            font-size: 16px;
            line-height: 1.65;
        }

        .requirements-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            min-height: 42px;
            border-radius: 10px;
            background: var(--telbin-gold);
            color: var(--telbin-forest-dark);
            padding: 10px 14px;
            font-weight: 900;
        }

        .document-thumbs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .doc-thumb {
            overflow: hidden;
            min-height: 118px;
            border-radius: 14px;
            background: #ffffff;
            color: #10233f;
            box-shadow: 0 12px 24px rgba(0, 0, 0, .16);
        }

        .doc-preview {
            position: relative;
            display: grid;
            place-items: center;
            height: 84px;
            background: #f2f6fc;
        }

        .doc-paper {
            width: 54px;
            height: 66px;
            border-radius: 5px;
            background: #ffffff;
            padding: 8px;
            box-shadow: 0 8px 18px rgba(16, 35, 63, .18);
        }

        .doc-line {
            display: block;
            height: 5px;
            margin-bottom: 6px;
            border-radius: 999px;
            background: #c8d8ef;
        }

        .doc-line.short {
            width: 62%;
        }

        .doc-line.medium {
            width: 78%;
        }

        .family-card {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            width: 62px;
            height: 44px;
            border-radius: 6px;
            background: #ffffff;
            padding: 7px;
            box-shadow: 0 8px 18px rgba(16, 35, 63, .18);
        }

        .family-card span {
            border-radius: 4px;
            background: #dbeafe;
        }

        .photo-sample {
            width: 54px;
            height: 68px;
            border: 4px solid #ffffff;
            border-radius: 6px;
            background:
                radial-gradient(circle at 50% 26%, #f3c7a2 0 10px, transparent 11px),
                linear-gradient(#38bdf8 0 100%);
            box-shadow: 0 8px 18px rgba(16, 35, 63, .18);
        }

        .photo-sample::after {
            content: "";
            display: block;
            width: 34px;
            height: 26px;
            margin: 34px auto 0;
            border-radius: 15px 15px 5px 5px;
            background: #ffffff;
        }

        .doc-thumb b {
            display: block;
            padding: 8px;
            color: var(--telbin-forest);
            font-size: 13px;
            text-align: center;
        }

        .requirements-list {
            display: grid;
            gap: 14px;
        }

        .requirement-card {
            display: grid;
            grid-template-columns: 54px 1fr;
            gap: 16px;
            align-items: center;
            border: 1px solid var(--telbin-line);
            border-radius: 16px;
            background: #ffffff;
            padding: 18px;
            box-shadow: 0 8px 22px rgba(16, 35, 63, .07);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .requirement-card:hover {
            transform: translateY(-4px);
            border-color: #79b9a6;
            box-shadow: 0 14px 30px rgba(16, 35, 63, .12);
        }

        .requirement-number {
            display: grid;
            place-items: center;
            width: 54px;
            height: 54px;
            border-radius: 14px;
            background: #e4f3ed;
            color: var(--telbin-forest);
            font-size: 20px;
            font-weight: 900;
        }

        .requirement-card h3 {
            margin-bottom: 4px;
            font-size: 18px;
            line-height: 1.3;
        }

        .requirement-card p {
            color: #52647d;
            font-size: 15px;
            line-height: 1.55;
        }

        .quota-card {
            overflow: hidden;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
            color: #10233f;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .quota-card:hover {
            box-shadow: 0 16px 34px rgba(0, 0, 0, .14);
        }

        .quota-media {
            position: relative;
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #dbe7f5;
        }

        .quota-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s ease;
        }

        .quota-card:hover .quota-media img {
            transform: scale(1.04);
        }

        .quota-body {
            display: flex;
            min-height: 118px;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            padding: 18px;
        }

        .quota-card h3 {
            margin: 0;
            min-height: 46px;
            font-size: 18px;
            line-height: 1.3;
        }

        .quota-total {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 10px;
            background: #e4f3ed;
            color: var(--telbin-forest);
            font-size: 24px;
            font-weight: 900;
        }

        #cek-status {
            background: #ffffff;
        }

        .status-panel {
            display: grid;
            grid-template-columns: minmax(280px, .9fr) minmax(340px, 1fr);
            gap: 26px;
            align-items: stretch;
            max-width: 1080px;
            margin: auto;
        }

        .status-copy,
        .status-form-card {
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(16, 35, 63, .09);
        }

        .status-copy {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 28px;
            background: linear-gradient(135deg, var(--telbin-forest-dark), var(--telbin-lagoon));
            color: #ffffff;
            padding: 30px;
        }

        .status-copy h3 {
            margin-bottom: 12px;
            font-size: 28px;
            line-height: 1.2;
        }

        .status-copy p {
            color: rgba(255, 255, 255, .86);
            line-height: 1.65;
        }

        .status-steps {
            display: grid;
            gap: 12px;
        }

        .status-step {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
        }

        .status-step span {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .16);
            color: var(--telbin-gold);
        }

        .status-form-card {
            background: #f7fbf9;
            border: 1px solid var(--telbin-line);
            padding: 28px;
        }

        .status-alert {
            margin-bottom: 18px;
            border-radius: 14px;
            padding: 16px;
            line-height: 1.55;
        }

        .status-alert strong {
            display: block;
            margin-bottom: 4px;
        }

        .status-alert.success {
            background: #eaf8ef;
            color: #166534;
        }

        .status-alert.info {
            background: #e4f3ed;
            color: var(--telbin-forest-dark);
        }

        .status-alert.warning,
        .status-alert.error {
            background: #fff4e5;
            color: #92400e;
        }

        .status-alert ul {
            padding-left: 18px;
        }

        .status-result {
            display: none;
        }

        .status-result.is-visible {
            display: block;
        }

        .status-form {
            display: grid;
            gap: 16px;
        }

        .form-field label {
            display: block;
            margin-bottom: 8px;
            color: #10233f;
            font-weight: 800;
        }

        .form-field input {
            width: 100%;
            min-height: 52px;
            border: 1px solid #c8d8ef;
            border-radius: 12px;
            background: #ffffff;
            color: #10233f;
            padding: 12px 14px;
            font: inherit;
        }

        .form-field input:focus {
            outline: 3px solid rgba(11, 93, 75, .16);
            border-color: var(--telbin-forest);
        }

        .captcha-row {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 12px;
            align-items: end;
        }

        .captcha-chip {
            display: grid;
            align-content: center;
            min-height: 52px;
            border-radius: 12px;
            background: #10233f;
            color: #ffffff;
            padding: 10px 14px;
        }

        .captcha-chip span {
            color: rgba(255, 255, 255, .7);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .captcha-chip b {
            font-size: 18px;
        }

        .status-submit {
            min-height: 54px;
            border: 0;
            border-radius: 12px;
            background: var(--telbin-forest);
            color: #ffffff;
            cursor: pointer;
            font: inherit;
            font-weight: 900;
            box-shadow: 0 12px 24px rgba(6, 63, 53, .20);
        }

        .status-submit:hover {
            background: var(--telbin-forest-dark);
        }

        .status-submit:disabled {
            cursor: wait;
            opacity: .72;
        }

        .image-modal {
            position: fixed;
            inset: 0;
            z-index: 80;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(5, 18, 40, .88);
            padding: 24px;
        }

        .image-modal.is-open {
            display: flex;
        }

        .image-modal img {
            max-width: min(1120px, 96vw);
            max-height: 90vh;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(0, 0, 0, .42);
        }

        .image-modal-close {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 50%;
            background: rgba(255, 255, 255, .12);
            color: #ffffff;
            cursor: pointer;
            font-size: 28px;
            line-height: 1;
        }

        footer {
            background: var(--telbin-forest-dark);
            color: #ffffff;
            padding: 30px;
            text-align: center;
            font-weight: 700;
        }

        .wa {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 20;
            border-radius: 50px;
            background: #25d366;
            color: #ffffff;
            padding: 14px 18px;
            font-weight: 800;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .2);
        }

        @media (max-width: 900px) {
            nav {
                align-items: flex-start;
                flex-direction: column;
                padding: 14px 18px;
            }

            .brand {
                font-size: 16px;
            }

            .brand img {
                width: 50px;
                height: 50px;
            }

            .brand-logo {
                width: 58px;
                height: 58px;
                border-radius: 15px;
            }

            .menu {
                justify-content: center;
                width: 100%;
            }

            .menu a {
                font-size: 14px;
                padding: 7px 6px;
            }

            .hero {
                min-height: 680px;
                padding-bottom: 20px;
            }

            .content {
                max-width: 100%;
                padding: 32px 20px 20px;
                text-align: center;
            }

            .content h1 {
                font-size: 42px;
                line-height: 1.03;
            }

            .content h3 {
                font-size: 20px;
            }

            .hero-actions {
                justify-content: center;
            }

            .hero-countdown {
                position: relative;
                right: auto;
                top: auto;
                display: flex;
                justify-content: center;
                margin: 20px auto 0;
                padding: 0 20px;
            }

            .count {
                justify-content: center;
                gap: 8px;
            }

            .box {
                min-width: 78px;
                padding: 9px;
            }

            .box b {
                font-size: 22px;
            }

            .section {
                padding: 44px 15px;
            }

            h2 {
                font-size: 30px;
            }

            #banner {
                padding-top: 38px;
                padding-bottom: 32px;
            }

            .banner-frame img {
                max-height: none;
            }

            .requirements-layout {
                grid-template-columns: 1fr;
            }

            .requirements-note {
                padding: 22px;
            }

            .document-thumbs {
                grid-template-columns: repeat(3, minmax(96px, 1fr));
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .status-panel {
                grid-template-columns: 1fr;
            }

            .status-copy,
            .status-form-card {
                padding: 22px;
            }
        }

        @media (max-width: 540px) {
            .menu a,
            .menu .login {
                flex: 1 1 130px;
                text-align: center;
            }

            .wa {
                right: 15px;
                bottom: 15px;
                padding: 12px 16px;
                font-size: 14px;
            }

            .requirement-card {
                grid-template-columns: 44px 1fr;
                gap: 12px;
                padding: 15px;
            }

            .requirement-number {
                width: 44px;
                height: 44px;
                border-radius: 12px;
                font-size: 17px;
            }

            .hero-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .hero-cta,
            .hero-link {
                width: 100%;
            }

            .captcha-row {
                grid-template-columns: 1fr;
            }

            .image-modal {
                padding: 14px;
            }
        }
    </style>
</head>
<body>
    <section class="hero" id="beranda">
        <nav>
            <div class="brand">
                <span class="brand-logo {{ $hasNewLandingLogo ? 'is-transparent' : '' }}">
                    <img src="{{ $landingLogo }}" alt="Logo Kabupaten Teluk Bintuni">
                </span>
                <span>SPMB SMP KABUPATEN TELUK BINTUNI</span>
            </div>
            <div class="menu">
                <a href="#beranda">Beranda</a>
                <a href="#jadwal">Jadwal</a>
                <a href="#persyaratan">Persyaratan</a>
                <a href="#kuota">Kuota</a>
                <a href="#cek-status">Cek Status SPMB</a>
                <a class="login" href="{{ route('login') }}">Login</a>
            </div>
        </nav>

        <div class="content">
            <h1>SISTEM<br>PENERIMAAN<br><span class="yellow">MURID BARU</span></h1>
            <h3>TAHUN AJARAN 2026/2027</h3>
            <div class="hero-actions">
                <a class="hero-cta" href="{{ route('register') }}">Daftar Akun SPMB</a>
                <a class="hero-link" href="#cek-status">Cek Status</a>
            </div>
        </div>

        <div class="hero-countdown" aria-label="Hitung mundur pendaftaran">
            <div class="count">
                <div class="box"><b id="d">0</b>Hari</div>
                <div class="box"><b id="h">0</b>Jam</div>
                <div class="box"><b id="m">0</b>Menit</div>
                <div class="box"><b id="s">0</b>Detik</div>
            </div>
        </div>
    </section>

    <section class="section" id="banner">
        <div class="wrap">
            <h2>Informasi SPMB 2026</h2>
            <div class="banner-frame">
                <button class="banner-button" type="button" data-open-image="{{ asset('landing/assets/brosur-spmb-smp-teluk-bintuni-2026.png') }}" aria-label="Lihat brosur SPMB SMP Kabupaten Teluk Bintuni ukuran penuh">
                    <img src="{{ asset('landing/assets/brosur-spmb-smp-teluk-bintuni-2026.png') }}" alt="Brosur SPMB SMP Kabupaten Teluk Bintuni Tahun Ajaran 2026/2027">
                </button>
            </div>
        </div>
    </section>

    <section id="jadwal" class="section">
        <div class="wrap">
            <h2>Jadwal Pendaftaran</h2>
            <div class="timeline">
                <div class="item">
                    <div class="schedule-icon online" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="5" width="16" height="11" rx="2.2" fill="rgba(255,255,255,.92)"></rect>
                            <rect x="6.3" y="7.2" width="11" height="6.4" rx="1" fill="#2563eb"></rect>
                            <path d="M2.5 19h19l-2.1-3H4.6L2.5 19Z" fill="rgba(255,255,255,.95)"></path>
                            <circle cx="18.4" cy="8.2" r="2.8" fill="#facc15"></circle>
                            <path d="M17.2 8.2h2.4M18.4 7v2.4" stroke="#0f172a" stroke-width="1.4" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <h4>Pendaftaran Online</h4>
                    <p>1-3 Juli 2026</p>
                </div>
                <div class="item">
                    <div class="schedule-icon interview" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="8" y="3" width="8" height="12" rx="4" fill="rgba(255,255,255,.95)"></rect>
                            <path d="M5.5 11a6.5 6.5 0 0 0 13 0" stroke="#fdf2f8" stroke-width="2.2" stroke-linecap="round"></path>
                            <path d="M12 17.5V21" stroke="#fdf2f8" stroke-width="2.2" stroke-linecap="round"></path>
                            <path d="M8.5 21h7" stroke="#fdf2f8" stroke-width="2.2" stroke-linecap="round"></path>
                            <path d="M10.2 6.7h3.6M10.2 9.4h3.6" stroke="#7c3aed" stroke-width="1.4" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <h3>Tes Wawancara</h3>
                    <p>6 Juli 2026</p>
                </div>
                <div class="item">
                    <div class="schedule-icon announcement" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M4 10.5v3.8a2 2 0 0 0 2 2h2.2l2.3 4.2h3l-2.1-4.6 7.6-2.9V7.7l-9.4 3H6a2 2 0 0 0-2 1.8Z" fill="rgba(255,255,255,.96)"></path>
                            <path d="M19.2 8.5a4 4 0 0 1 0 4.2" stroke="#fff7ed" stroke-width="2" stroke-linecap="round"></path>
                            <path d="M8.2 10.8v5.3" stroke="#f97316" stroke-width="1.7" stroke-linecap="round"></path>
                            <circle cx="5.4" cy="16.1" r="1.8" fill="#0f172a" opacity=".18"></circle>
                        </svg>
                    </div>
                    <h3>Pengumuman</h3>
                    <p>11 Juli 2026</p>
                </div>
                <div class="item">
                    <div class="schedule-icon reregistration" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="5" y="5" width="14" height="16" rx="2.2" fill="rgba(255,255,255,.95)"></rect>
                            <path d="M9 3.5h6v4H9z" fill="#bbf7d0"></path>
                            <path d="m8.4 13 2.1 2.1 5.1-5.1" stroke="#16a34a" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M8.5 18h7" stroke="#0f766e" stroke-width="1.6" stroke-linecap="round"></path>
                        </svg>
                    </div>
                    <h3>Daftar Ulang</h3>
                    <p>13-14 Juli 2026</p>
                </div>
                <div class="item">
                    <div class="schedule-icon orientation" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="m3 8 9-4 9 4-9 4-9-4Z" fill="rgba(255,255,255,.96)"></path>
                            <path d="M7 10.3v4.4c0 1.6 2.2 3.1 5 3.1s5-1.5 5-3.1v-4.4" fill="#bfdbfe"></path>
                            <path d="M21 8v6" stroke="#fef3c7" stroke-width="2" stroke-linecap="round"></path>
                            <circle cx="21" cy="15.5" r="1.4" fill="#facc15"></circle>
                        </svg>
                    </div>
                    <h3>MPLS</h3>
                    <p>16-18 Juli 2026</p>
                </div>
            </div>
        </div>
    </section>

    <section id="persyaratan" class="section">
        <div class="wrap">
            <h2>Persyaratan</h2>
            <div class="requirements-layout">
                <div class="requirements-note">
                    <div>
                        <h3>Siapkan berkas sebelum mendaftar</h3>
                        <p>Pastikan seluruh dokumen sudah jelas terbaca agar proses verifikasi panitia berjalan lebih cepat.</p>
                    </div>
                    <div class="document-thumbs" aria-label="Contoh berkas persyaratan">
                        <div class="doc-thumb">
                            <div class="doc-preview">
                                <div class="family-card" aria-hidden="true">
                                    <span></span><span></span><span></span>
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                            <b>Kartu Keluarga</b>
                        </div>
                        <div class="doc-thumb">
                            <div class="doc-preview">
                                <div class="doc-paper" aria-hidden="true">
                                    <span class="doc-line"></span>
                                    <span class="doc-line medium"></span>
                                    <span class="doc-line"></span>
                                    <span class="doc-line short"></span>
                                </div>
                            </div>
                            <b>Ijazah</b>
                        </div>
                        <div class="doc-thumb">
                            <div class="doc-preview">
                                <div class="photo-sample" aria-hidden="true"></div>
                            </div>
                            <b>Pas Foto Seragam</b>
                        </div>
                    </div>
                    <span class="requirements-badge">4 Berkas Utama</span>
                </div>
                <div class="requirements-list">
                    <div class="requirement-card">
                        <div class="requirement-number">01</div>
                        <div>
                            <h3>Batas Usia</h3>
                            <p>Berusia maksimal 21 tahun pada 1 Juli 2026.</p>
                        </div>
                    </div>
                    <div class="requirement-card">
                        <div class="requirement-number">02</div>
                        <div>
                            <h3>Ijazah SD/Sederajat</h3>
                            <p>Melampirkan ijazah atau dokumen kelulusan SD/sederajat yang setara.</p>
                        </div>
                    </div>
                    <div class="requirement-card">
                        <div class="requirement-number">03</div>
                        <div>
                            <h3>Kartu Keluarga</h3>
                            <p>Melampirkan fotokopi Kartu Keluarga sebagai data identitas calon peserta didik.</p>
                        </div>
                    </div>
                    <div class="requirement-card">
                        <div class="requirement-number">04</div>
                        <div>
                            <h3>Pas Foto 3x4</h3>
                            <p>Menyiapkan pas foto ukuran 3x4 dengan tampilan yang rapi dan jelas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kuota" class="section">
        <div class="wrap">
            <h2>Jalur Pendaftaran</h2>
            <div class="grid">
                <div class="card"><h3>Jalur Domisili</h3><p>Pilihan SMP berdasarkan wilayah domisili yang telah diverifikasi melalui Kartu Keluarga.</p></div>
                <div class="card"><h3>Jalur Prestasi</h3><p>Pilihan lintas domisili dengan pemeringkatan berdasarkan nilai TKA.</p></div>
                <div class="card"><h3>Jalur Afirmasi</h3><p>Untuk keluarga tidak mampu, penyandang disabilitas, dan kelompok khusus sesuai ketentuan.</p></div>
                <div class="card"><h3>Jalur Mutasi</h3><p>Untuk calon murid yang mengikuti perpindahan tugas atau pekerjaan orang tua/wali.</p></div>
            </div>
        </div>
    </section>

    <section id="cek-status" class="section">
        <div class="wrap">
            <h2>Cek Status SPMB</h2>
            <div class="status-panel">
                <div class="status-copy">
                    <div>
                        <h3>Pantau status akun dari halaman ini</h3>
                        <p>Masukkan NISN dan jawab captcha sederhana. Hasil pengecekan akan tampil langsung di bagian form ini.</p>
                    </div>
                    <div class="status-steps">
                        <div class="status-step"><span>1</span> Isi NISN calon siswa</div>
                        <div class="status-step"><span>2</span> Jawab captcha</div>
                        <div class="status-step"><span>3</span> Lihat hasil status</div>
                    </div>
                </div>

                <div class="status-form-card">
                    <div id="status-result" class="status-result {{ $errors->status->any() || session('status_result') ? 'is-visible' : '' }}" aria-live="polite">
                    @if($errors->status->any())
                        <div class="status-alert error">
                            <strong>Pengecekan belum berhasil.</strong>
                            <ul>
                                @foreach($errors->status->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('status_result') === 'registered')
                        <div class="status-alert success">
                            <strong>NISN {{ session('status_nisn') }} sudah memiliki akun SPMB.</strong>
                            Silakan login untuk melanjutkan proses pendaftaran.
                        </div>
                    @elseif(session('status_result') === 'menunggu_verifikasi')
                        <div class="status-alert info">
                            <strong>Akun NISN {{ session('status_nisn') }} sedang menunggu verifikasi.</strong>
                            Data alamat dan Kartu Keluarga sedang diperiksa oleh Dinas Pendidikan.
                        </div>
                    @elseif(session('status_result') === 'terverifikasi')
                        <div class="status-alert success">
                            <strong>Akun NISN {{ session('status_nisn') }} sudah terverifikasi.</strong>
                            Akun sudah aktif. Silakan login untuk melanjutkan pendaftaran.
                        </div>
                    @elseif(session('status_result') === 'perlu_perbaikan')
                        <div class="status-alert warning">
                            <strong>Registrasi NISN {{ session('status_nisn') }} perlu diperbaiki.</strong>
                            Silakan login untuk membaca catatan dan memperbaiki data.
                            @if(session('status_note'))<div class="mt-2"><b>Catatan:</b> {{ session('status_note') }}</div>@endif
                        </div>
                    @elseif(session('status_result') === 'ditolak')
                        <div class="status-alert warning">
                            <strong>Registrasi NISN {{ session('status_nisn') }} ditolak.</strong>
                            @if(session('status_note'))<div><b>Catatan:</b> {{ session('status_note') }}</div>@endif
                        </div>
                    @elseif(session('status_result') === 'not_registered')
                        <div class="status-alert info">
                            <strong>NISN {{ session('status_nisn') }} tersedia di database calon siswa.</strong>
                            Silakan daftar akun SPMB melalui tombol daftar pada halaman ini.
                        </div>
                    @elseif(session('status_result') === 'inactive')
                        <div class="status-alert warning">
                            <strong>NISN {{ session('status_nisn') }} tidak tersedia pada whitelist aktif.</strong>
                            Tidak ditemukan pada whitelist calon peserta didik aktif. Silakan menghubungi panitia SPMB melalui WhatsApp.
                        </div>
                    @elseif(session('status_result') === 'not_found')
                        <div class="status-alert warning">
                            <strong>NISN {{ session('status_nisn') }} belum ditemukan.</strong>
                            Silakan hubungi panitia SPMB untuk pengecekan data calon peserta didik.
                        </div>
                    @endif
                    </div>

                    <form class="status-form" method="post" action="{{ route('status.check') }}" data-status-form>
                        @csrf
                        <div class="form-field">
                            <label for="status-nisn">NISN</label>
                            <input id="status-nisn" type="text" name="nisn" value="{{ old('nisn', session('status_nisn')) }}" inputmode="numeric" maxlength="10" autocomplete="username" placeholder="Masukkan 10 digit NISN" required>
                        </div>
                        <div class="captcha-row">
                            <div class="captcha-chip">
                                <span>Captcha</span>
                                <b data-captcha-question>{{ session('status_captcha_question') }} = ?</b>
                            </div>
                            <div class="form-field">
                                <label for="status-captcha">Jawaban</label>
                                <input id="status-captcha" type="number" name="captcha_answer" inputmode="numeric" placeholder="Hasil" required>
                            </div>
                        </div>
                        <button class="status-submit" type="submit">Cek Status Sekarang</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="image-modal" id="image-modal" aria-hidden="true">
        <button class="image-modal-close" type="button" aria-label="Tutup gambar">&times;</button>
        <img src="" alt="Brosur SPMB 2026 ukuran penuh">
    </div>

    <footer>2026 - Dinas Pendidikan Kabupaten Teluk Bintuni</footer>

    <a class="wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">WhatsApp Panitia</a>

    <script>
        const targetDate = new Date('2026-07-01T00:00:00').getTime();
        const dayElement = document.getElementById('d');
        const hourElement = document.getElementById('h');
        const minuteElement = document.getElementById('m');
        const secondElement = document.getElementById('s');

        function updateCountdown() {
            const remaining = targetDate - new Date().getTime();

            dayElement.innerText = Math.max(0, Math.floor(remaining / 86400000));
            hourElement.innerText = Math.max(0, Math.floor((remaining % 86400000) / 3600000));
            minuteElement.innerText = Math.max(0, Math.floor((remaining % 3600000) / 60000));
            secondElement.innerText = Math.max(0, Math.floor((remaining % 60000) / 1000));
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);

        const imageModal = document.getElementById('image-modal');
        const imageModalImg = imageModal?.querySelector('img');
        const imageModalClose = imageModal?.querySelector('.image-modal-close');

        document.querySelectorAll('[data-open-image]').forEach((button) => {
            button.addEventListener('click', () => {
                if (! imageModal || ! imageModalImg) {
                    return;
                }

                imageModalImg.src = button.dataset.openImage;
                imageModal.classList.add('is-open');
                imageModal.setAttribute('aria-hidden', 'false');
            });
        });

        function closeImageModal() {
            if (! imageModal || ! imageModalImg) {
                return;
            }

            imageModal.classList.remove('is-open');
            imageModal.setAttribute('aria-hidden', 'true');
            imageModalImg.src = '';
        }

        imageModalClose?.addEventListener('click', closeImageModal);
        imageModal?.addEventListener('click', (event) => {
            if (event.target === imageModal) {
                closeImageModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeImageModal();
            }
        });

        const statusForm = document.querySelector('[data-status-form]');
        const statusResult = document.getElementById('status-result');
        const captchaQuestion = document.querySelector('[data-captcha-question]');

        function renderStatusAlert(type, title, messages) {
            if (! statusResult) {
                return;
            }

            const alert = document.createElement('div');
            alert.className = `status-alert ${type}`;

            const strong = document.createElement('strong');
            strong.textContent = title;
            alert.appendChild(strong);

            if (Array.isArray(messages)) {
                const list = document.createElement('ul');
                messages.forEach((message) => {
                    const item = document.createElement('li');
                    item.textContent = message;
                    list.appendChild(item);
                });
                alert.appendChild(list);
            } else if (messages) {
                alert.appendChild(document.createTextNode(messages));
            }

            statusResult.replaceChildren(alert);
            statusResult.classList.add('is-visible');
        }

        statusForm?.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = statusForm.querySelector('.status-submit');
            const captchaInput = statusForm.querySelector('input[name="captcha_answer"]');
            const formData = new FormData(statusForm);

            submitButton.disabled = true;
            submitButton.textContent = 'Memeriksa...';

            try {
                const response = await fetch(statusForm.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });
                const data = await response.json();

                const statusMessage = data.note ? `${data.message} Catatan: ${data.note}` : (data.messages || data.message);
                renderStatusAlert(data.type || 'error', data.title || 'Pengecekan belum berhasil.', statusMessage);

                if (captchaQuestion && data.captcha_question) {
                    captchaQuestion.textContent = `${data.captcha_question} = ?`;
                }

                if (captchaInput) {
                    captchaInput.value = '';
                    captchaInput.focus();
                }
            } catch (error) {
                renderStatusAlert('error', 'Pengecekan belum berhasil.', 'Koneksi sedang tidak stabil. Silakan coba lagi.');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = 'Cek Status Sekarang';
            }
        });
    </script>
</body>
</html>
