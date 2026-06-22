<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SPMB SMP Kabupaten Teluk Bintuni' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if(request()->routeIs('admin.pengguna', 'admin.pendaftar', 'admin.pengaturan', 'sekolah.admin.pendaftar'))
        <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    @endif
    @if(request()->routeIs('admin.sekolah-zonasi'))
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    @endif
    <style>
        :root {
            --spmb-red: #b91c1c;
            --spmb-red-dark: #7f1d1d;
            --spmb-ink: #172033;
            --spmb-muted: #667085;
            --spmb-line: #e5e7eb;
            --spmb-soft: #f6f8fb;
            --spmb-sidebar: #101828;
            --telbin-forest: #0b5d4b;
            --telbin-forest-dark: #063f35;
            --telbin-lagoon: #0788a8;
            --telbin-gold: #f2b84b;
            --telbin-soft: #eef7f3;
            --telbin-line: #cfe4dc;
        }
        body { background: var(--spmb-soft); color: var(--spmb-ink); }
        .navbar {
            min-height: 68px;
            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid var(--spmb-line);
            box-shadow: 0 10px 30px rgba(16, 24, 40, .06);
            backdrop-filter: blur(12px);
        }
        .topbar-brand {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            color: var(--spmb-ink);
            text-decoration: none;
        }
        .topbar-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: .28rem;
            border: 1px solid #dbe4f0;
            box-shadow: 0 8px 18px rgba(16, 24, 40, .08);
        }
        .topbar-brand-title {
            display: block;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0;
        }
        .topbar-brand-subtitle {
            display: block;
            color: var(--spmb-muted);
            font-size: .78rem;
            font-weight: 600;
            line-height: 1.2;
        }
        .topbar-user {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .35rem;
            border: 1px solid #e4e7ec;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(16, 24, 40, .05);
        }
        .topbar-avatar {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 800;
            overflow: hidden;
        }
        .topbar-avatar-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .topbar-avatar-icon {
            width: 21px;
            height: 21px;
            background: currentColor;
            display: inline-block;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21a8 8 0 0 0-16 0'/%3E%3Ccircle cx='12' cy='7' r='4'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .topbar-user-name {
            max-width: 220px;
            color: var(--spmb-ink);
            font-weight: 800;
            line-height: 1.15;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .topbar-role {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .72rem;
            font-weight: 800;
            padding: .1rem .5rem;
        }
        .topbar-logout {
            border-radius: 999px;
            padding-inline: .9rem;
        }
        .sidebar-link { color: #cbd5e1; display: flex; align-items: center; gap: .65rem; padding: .72rem .85rem; text-decoration: none; border-radius: .5rem; font-weight: 600; }
        .sidebar-link:hover, .sidebar-link.active { background: #243044; color: #fff; }
        .sidebar-link.active { box-shadow: inset 3px 0 0 var(--telbin-gold); }
        .document-preview-frame {
            width: 100%;
            height: min(72vh, 760px);
            border: 0;
            border-radius: .5rem;
            background: #f8fafc;
        }
        .document-preview-image {
            display: block;
            max-width: 100%;
            max-height: 72vh;
            margin: auto;
            border-radius: .5rem;
        }
        .download-icon {
            width: 1rem;
            height: 1rem;
            margin-right: .35rem;
            vertical-align: -.15rem;
        }
        .app-shell { min-height: calc(100vh - 56px); }
        .sidebar { background: var(--spmb-sidebar); }
        .card { border: 1px solid var(--spmb-line); border-radius: .5rem; }
        .card-header { background: #fff; border-bottom-color: var(--spmb-line); }
        .table td, .table th { vertical-align: middle; }
        .table thead th { color: #475467; font-size: .82rem; text-transform: uppercase; }
        .btn { border-radius: .45rem; font-weight: 600; }
        .btn-primary { background: var(--spmb-red); border-color: var(--spmb-red); }
        .btn-primary:hover { background: var(--spmb-red-dark); border-color: var(--spmb-red-dark); }
        .btn-danger { background: var(--spmb-red); border-color: var(--spmb-red); }
        .btn-danger:hover { background: var(--spmb-red-dark); border-color: var(--spmb-red-dark); }
        .page-title { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.25rem; }
        .page-title h3 { margin: 0; }
        .stat-card { min-height: 132px; }
        .stat-icon { width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; border-radius: .5rem; background: #fee2e2; color: var(--spmb-red); font-weight: 800; }
        .stat-icon-shape {
            width: 24px;
            height: 24px;
            display: inline-block;
            background: currentColor;
        }
        .stat-icon-final {
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z'/%3E%3Cpath d='M14 2v4a2 2 0 0 0 2 2h4'/%3E%3Cpath d='m9 15 2 2 4-4'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .stat-icon-draft {
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'/%3E%3Cpath d='M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84.84-2.873a2 2 0 0 1 .506-.852z'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .stat-icon-waiting {
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='9' cy='7' r='4'/%3E%3Cpolyline points='16 11 18 13 22 9'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .stat-icon-student {
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M22 10v6M2 10l10-5 10 5-10 5z'/%3E%3Cpath d='M6 12v5c3 3 9 3 12 0v-5'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .muted-label { color: var(--spmb-muted); font-size: .9rem; }
        .admin-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid #dbe4f0;
            border-radius: .75rem;
            background: #fff;
            padding: 1.25rem;
            box-shadow: 0 14px 34px rgba(16, 24, 40, .07);
        }
        .admin-hero-meta {
            min-width: 180px;
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
            padding: .85rem 1rem;
            text-align: right;
        }
        .admin-hero-meta span {
            display: block;
            color: #1d4ed8;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .admin-hero-meta strong {
            display: block;
            color: #1e3a8a;
            font-size: 1.15rem;
            line-height: 1.2;
        }
        .program-overview {
            overflow: hidden;
        }
        .program-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .program-interest-columns {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            align-items: start;
        }
        .program-interest-panel {
            min-width: 0;
            border: 1px solid #e4e7ec;
            border-radius: .75rem;
            background: #f8fafc;
            padding: 1rem;
        }
        .program-interest-title {
            color: var(--spmb-ink);
            font-size: 1rem;
            font-weight: 900;
            line-height: 1.2;
        }
        .program-interest-subtitle {
            color: var(--spmb-muted);
            font-size: .86rem;
            font-weight: 600;
            margin: .25rem 0 1rem;
        }
        .program-interest-list {
            display: grid;
            gap: .8rem;
        }
        .program-stat-item {
            min-width: 0;
            --program-accent: #b91c1c;
            --program-soft: #fee2e2;
            --program-ink: #7f1d1d;
            border: 1px solid color-mix(in srgb, var(--program-accent) 28%, #ffffff);
            border-radius: .75rem;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--program-soft) 70%, #ffffff) 0%, #ffffff 52%),
                #fff;
            padding: 1.15rem;
            box-shadow: 0 10px 24px rgba(16, 24, 40, .05);
        }
        .program-stat-visual {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: .65rem;
            background: var(--program-accent);
            color: #fff;
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: 0;
            box-shadow: 0 10px 20px color-mix(in srgb, var(--program-accent) 22%, transparent);
        }
        .program-stat-name {
            min-height: 0;
            color: var(--program-ink);
            font-size: 1rem;
            font-weight: 800;
            line-height: 1.25;
        }
        .program-stat-total {
            min-width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .7rem;
            background: #fff;
            color: var(--program-accent);
            font-size: 1.45rem;
            font-weight: 900;
            border: 1px solid color-mix(in srgb, var(--program-accent) 24%, #ffffff);
        }
        .program-stat-track {
            height: 10px;
            border-radius: 999px;
            background: #eef2f7;
            overflow: hidden;
        }
        .program-stat-bar {
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--program-accent), color-mix(in srgb, var(--program-accent) 58%, #ffffff));
        }
        .program-quota-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin: .85rem 0 .45rem;
            color: var(--spmb-muted);
            font-size: .84rem;
            font-weight: 700;
        }
        .program-quota-line strong {
            color: var(--program-accent);
            font-size: .95rem;
            font-weight: 900;
        }
        .program-stat-detail {
            display: flex;
            justify-content: space-between;
            gap: .5rem;
            color: var(--spmb-muted);
            font-size: .84rem;
            font-weight: 700;
            margin-top: .75rem;
        }
        .program-akl {
            --program-accent: #0f766e;
            --program-soft: #ccfbf1;
            --program-ink: #134e4a;
        }
        .program-tkr {
            --program-accent: #b91c1c;
            --program-soft: #fee2e2;
            --program-ink: #7f1d1d;
        }
        .program-tkj {
            --program-accent: #2563eb;
            --program-soft: #dbeafe;
            --program-ink: #1e3a8a;
        }
        .program-dkv {
            --program-accent: #7c3aed;
            --program-soft: #ede9fe;
            --program-ink: #4c1d95;
        }
        .program-tsm {
            --program-accent: #c2410c;
            --program-soft: #ffedd5;
            --program-ink: #7c2d12;
        }
        .admin-stat-card {
            --stat-accent: #b91c1c;
            --stat-soft: #fee2e2;
            --stat-ink: #7f1d1d;
            border-color: #dbe4f0;
            background:
                linear-gradient(135deg, color-mix(in srgb, var(--stat-soft) 72%, #ffffff) 0%, #ffffff 58%),
                #fff;
            box-shadow: 0 12px 28px rgba(16, 24, 40, .06) !important;
            position: relative;
            overflow: hidden;
        }
        .admin-stat-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: var(--stat-accent);
        }
        .admin-stat-card .display-6 {
            color: var(--stat-ink);
        }
        .admin-stat-card .stat-icon {
            background: var(--stat-accent);
            color: #fff;
            box-shadow: 0 10px 20px color-mix(in srgb, var(--stat-accent) 22%, transparent);
        }
        .admin-stat-card a {
            color: var(--stat-accent);
        }
        .stat-final {
            --stat-accent: #16a34a;
            --stat-soft: #dcfce7;
            --stat-ink: #14532d;
        }
        .stat-draft {
            --stat-accent: #d97706;
            --stat-soft: #fef3c7;
            --stat-ink: #78350f;
        }
        .stat-waiting {
            --stat-accent: #2563eb;
            --stat-soft: #dbeafe;
            --stat-ink: #1e3a8a;
        }
        .stat-student {
            --stat-accent: #7c3aed;
            --stat-soft: #ede9fe;
            --stat-ink: #4c1d95;
        }
        .admin-task-list {
            display: grid;
            gap: .75rem;
        }
        .admin-task-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border: 1px solid #e4e7ec;
            border-radius: .65rem;
            background: #fff;
            color: var(--spmb-ink);
            padding: .9rem 1rem;
            text-decoration: none;
        }
        .admin-task-list a:hover {
            border-color: #bfdbfe;
            background: #f8fafc;
        }
        .admin-task-list strong {
            display: block;
            font-weight: 800;
        }
        .admin-task-list span {
            color: var(--spmb-muted);
            font-size: .88rem;
            text-align: right;
        }
        .admin-summary-panel,
        .admin-work-panel {
            min-height: 100%;
        }
        .admin-ratio {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: .75rem;
        }
        .admin-ratio:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .admin-ratio span {
            color: var(--spmb-muted);
            font-weight: 700;
        }
        .admin-ratio strong {
            color: var(--spmb-ink);
            font-size: 1.2rem;
            font-weight: 900;
        }
        .doc-thumb { width: 86px; height: 86px; object-fit: cover; border-radius: .35rem; border: 1px solid #e5e7eb; }
        .form-section {
            overflow: hidden;
        }
        .form-section .card-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.15rem;
        }
        .section-number {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #e4f3ed;
            color: var(--telbin-forest);
            font-weight: 800;
        }
        .form-control,
        .form-select,
        .input-group-text {
            border-color: #d0d5dd;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: var(--telbin-forest);
            box-shadow: 0 0 0 .2rem rgba(11, 93, 75, .16);
        }
        .upload-box {
            min-height: 100%;
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            padding: 1rem;
            background: #fff;
        }
        .registration-shell {
            display: grid;
            grid-template-columns: minmax(220px, 280px) minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
        }
        .registration-nav {
            position: sticky;
            top: 1rem;
            display: grid;
            gap: .65rem;
            border: 1px solid var(--spmb-line);
            border-radius: .75rem;
            background: #fff;
            padding: .85rem;
            box-shadow: 0 10px 28px rgba(16, 24, 40, .06);
        }
        .registration-nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            width: 100%;
            padding: .8rem;
            border: 1px solid transparent;
            border-radius: .6rem;
            background: transparent;
            color: var(--spmb-ink);
            text-decoration: none;
            text-align: left;
        }
        .registration-nav-link:hover {
            background: var(--telbin-soft);
            border-color: var(--telbin-line);
        }
        .registration-nav-link.active {
            border-color: rgba(11, 93, 75, .3);
            background: var(--telbin-soft);
            color: var(--telbin-forest-dark);
        }
        .registration-nav-link.completed span {
            background: #dcfce7;
            color: #15803d;
        }
        .registration-nav-link span {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #e5e7eb;
            color: #475467;
            font-weight: 800;
        }
        .registration-nav-link.active span {
            background: var(--telbin-forest);
            color: #fff;
        }
        .registration-nav-link strong {
            display: block;
            line-height: 1.15;
        }
        .registration-nav-link small {
            color: var(--spmb-muted);
        }
        .registration-content {
            min-width: 0;
        }
        .registration-content section {
            scroll-margin-top: 1rem;
        }
        .form-step[hidden] { display: none !important; }
        .domicile-map {
            min-height: 390px;
            border: 1px solid var(--spmb-line);
            border-radius: .8rem;
            background: #eef2f6;
            overflow: hidden;
        }
        .school-choice-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }
        .school-choice-card {
            border: 1px solid var(--telbin-line);
            border-radius: .8rem;
            background: #fff;
            padding: 1rem;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .school-choice-card:hover {
            border-color: var(--telbin-forest);
            box-shadow: 0 6px 18px rgba(11, 93, 75, .12);
            transform: translateY(-2px);
        }
        .school-choice-card.selected {
            border-color: var(--telbin-forest);
            box-shadow: 0 0 0 3px rgba(11, 93, 75, .12);
            transform: translateY(-1px);
        }
        .achievement-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
        }
        .achievement-stat {
            border: 1px solid #dbe4f0;
            border-radius: .75rem;
            background: linear-gradient(145deg, #fff, #f8fafc);
            padding: .9rem;
        }
        .achievement-stat span {
            display: block;
            color: var(--spmb-muted);
            font-size: .78rem;
            font-weight: 700;
        }
        .achievement-stat strong {
            display: block;
            margin-top: .2rem;
            font-size: 1.35rem;
            font-weight: 900;
        }
        .opportunity-progress {
            height: .65rem;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
        }
        .opportunity-progress-bar {
            height: 100%;
            border-radius: inherit;
        }
        .opportunity-high { background: #16a34a; }
        .opportunity-medium { background: #eab308; }
        .opportunity-low { background: #dc2626; }
        .opportunity-text-high { color: #15803d; }
        .opportunity-text-medium { color: #a16207; }
        .opportunity-text-low { color: #b91c1c; }
        .prestasi-school-card { cursor: pointer; }
        .school-quota-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .5rem;
        }
        .school-quota-item {
            border-radius: .55rem;
            background: #f8fafc;
            padding: .65rem;
            text-align: center;
        }
        .school-quota-item strong,
        .school-quota-item span { display: block; }
        .school-quota-item span {
            color: var(--spmb-muted);
            font-size: .72rem;
        }
        .review-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }
        .review-summary-item {
            border: 1px solid var(--spmb-line);
            border-radius: .65rem;
            background: #f8fafc;
            padding: .85rem;
        }
        .upload-box-modern {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            border-color: #dbe4f0;
            box-shadow: 0 10px 24px rgba(16, 24, 40, .05);
        }
        .uploaded-file {
            border: 1px dashed var(--telbin-line);
            border-radius: .5rem;
            background: var(--telbin-soft);
            padding: .75rem;
        }
        .document-hint {
            color: var(--spmb-muted);
            font-size: .82rem;
            line-height: 1.35;
        }
        .upload-file-control .btn {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            white-space: nowrap;
        }
        .upload-file-name {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: var(--spmb-muted);
            background: #fff;
        }
        .address-group {
            border: 1px solid #dbe4f0;
            border-radius: .65rem;
            background: #f8fafc;
            padding: 1rem;
        }
        .same-address-callout {
            margin: 0;
            padding: .9rem 1rem;
            border: 1px solid var(--telbin-line);
            border-radius: .65rem;
            background: var(--telbin-soft);
        }
        .same-address-callout .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            border-color: var(--telbin-forest);
        }
        .same-address-callout .form-check-input:checked {
            background-color: var(--telbin-forest);
            border-color: var(--telbin-forest);
        }
        .same-address-callout .form-check-label {
            color: var(--telbin-forest-dark);
        }
        .history-card {
            overflow: hidden;
        }
        .history-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.25rem;
            border-bottom: 1px solid var(--spmb-line);
            background: #fff;
        }
        .history-number {
            color: var(--spmb-ink);
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0;
        }
        .history-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 340px);
            gap: 0;
        }
        .history-main {
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }
        .history-side {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            border-left: 1px solid var(--spmb-line);
            background: #f8fafc;
            padding: 1.25rem;
        }
        .history-section,
        .history-side-panel {
            border: 1px solid #e4e7ec;
            border-radius: .65rem;
            background: #fff;
            padding: 1rem;
        }
        .history-section-title {
            color: #475467;
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: .85rem;
        }
        .history-identity {
            display: flex;
            align-items: center;
            gap: .85rem;
        }
        .history-avatar {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: var(--telbin-soft);
            color: var(--telbin-forest);
            font-weight: 800;
        }
        .history-name {
            font-size: 1.1rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .history-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .85rem;
        }
        .history-grid div {
            min-width: 0;
        }
        .history-grid span {
            display: block;
            color: var(--spmb-muted);
            font-size: .78rem;
            font-weight: 700;
            margin-bottom: .2rem;
        }
        .history-grid strong {
            display: block;
            color: var(--spmb-ink);
            font-size: .95rem;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }
        .history-grid-full {
            grid-column: 1 / -1;
        }
        .program-pill {
            border: 1px solid #bfdbfe;
            border-radius: .55rem;
            background: #eff6ff;
            color: #1e3a8a;
            font-weight: 800;
            padding: .7rem .8rem;
        }
        .program-pill + .program-pill {
            margin-top: .55rem;
        }
        .document-list {
            display: grid;
            gap: .5rem;
        }
        .document-list a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e4e7ec;
            border-radius: .5rem;
            background: #fff;
            color: var(--spmb-ink);
            font-weight: 700;
            padding: .65rem .75rem;
            text-decoration: none;
        }
        .document-list a::after {
            content: "Lihat";
            color: #1d4ed8;
            font-size: .78rem;
            font-weight: 800;
        }
        .history-timeline {
            display: grid;
            gap: .75rem;
        }
        .history-timeline div {
            display: grid;
            grid-template-columns: 18px 1fr;
            gap: .55rem;
        }
        .history-timeline span {
            width: 12px;
            height: 12px;
            margin-top: .25rem;
            border-radius: 50%;
            background: #16a34a;
            box-shadow: 0 0 0 4px #dcfce7;
        }
        .history-timeline .muted span {
            background: #94a3b8;
            box-shadow: 0 0 0 4px #e2e8f0;
        }
        .history-timeline p {
            margin: 0;
            font-weight: 700;
            line-height: 1.3;
        }
        .history-timeline small {
            color: var(--spmb-muted);
            font-weight: 600;
        }
        .sticky-actions {
            position: sticky;
            bottom: 0;
            z-index: 5;
            border: 1px solid var(--telbin-line);
            border-radius: .65rem;
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(8px);
            padding: .9rem 1rem;
            box-shadow: 0 -6px 24px rgba(11, 93, 75, .1);
        }
        .auth-page {
            min-height: calc(100vh - 56px);
            position: relative;
            overflow: hidden;
        }
        .auth-page::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(185, 28, 28, .94), rgba(127, 29, 29, .84)),
                url("{{ asset('landing/assets/background1.png') }}") center/cover;
            z-index: -2;
        }
        .login-auth-page::before {
            background:
                linear-gradient(105deg, rgba(3, 45, 38, .92) 0%, rgba(5, 92, 76, .76) 48%, rgba(7, 136, 168, .48) 100%),
                url("{{ asset('landing/assets/background1.png') }}") center/cover;
        }
        .register-auth-page::before {
            background:
                linear-gradient(105deg, rgba(3, 45, 38, .92) 0%, rgba(5, 92, 76, .76) 48%, rgba(7, 136, 168, .48) 100%),
                url("{{ asset('landing/assets/background2.png') }}") center/cover;
        }
        .login-auth-page,
        .register-auth-page {
            min-height: 100vh;
        }
        .auth-page::after {
            content: "";
            position: absolute;
            inset: auto -10% -30% -10%;
            height: 52%;
            background: #f6f8fb;
            transform: skewY(-4deg);
            transform-origin: left top;
            z-index: -1;
        }
        .login-auth-page::after,
        .register-auth-page::after {
            display: none;
        }
        .login-auth-page .btn-primary,
        .register-auth-page .btn-primary {
            background: var(--telbin-forest);
            border-color: var(--telbin-forest);
        }
        .login-auth-page .btn-primary:hover,
        .register-auth-page .btn-primary:hover,
        .login-auth-page .btn-primary:focus,
        .register-auth-page .btn-primary:focus {
            background: var(--telbin-forest-dark);
            border-color: var(--telbin-forest-dark);
        }
        .login-auth-page a,
        .register-auth-page a {
            color: var(--telbin-forest);
        }
        .login-auth-page a.btn-primary,
        .register-auth-page a.btn-primary,
        .login-auth-page a.btn-primary:hover,
        .register-auth-page a.btn-primary:hover {
            color: #fff;
        }
        .auth-panel {
            border: 0;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 28px 80px rgba(3, 45, 38, .3);
            backdrop-filter: blur(12px);
        }
        .auth-logo {
            width: 86px;
            height: 86px;
            object-fit: contain;
            border-radius: 50%;
            background: #fff;
            padding: .5rem;
            box-shadow: 0 12px 28px rgba(16, 24, 40, .18);
        }
        .auth-school-badge {
            display: inline-flex;
            align-items: center;
            gap: .95rem;
            padding: .7rem 1rem .7rem .7rem;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: .75rem;
            background: rgba(6, 63, 53, .38);
            box-shadow: 0 16px 36px rgba(3, 45, 38, .22);
            backdrop-filter: blur(10px);
        }
        .auth-school-badge .auth-logo {
            width: 58px;
            height: 58px;
            box-shadow: none;
        }
        .auth-kicker {
            color: rgba(255, 255, 255, .72);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .auth-school-name {
            color: #fff;
            font-weight: 800;
            line-height: 1.15;
        }
        .auth-copy {
            color: rgba(255, 255, 255, .88);
        }
        .auth-copy h1 {
            color: #fff;
            font-size: clamp(1.75rem, 3vw, 2.55rem);
            line-height: 1.12;
            margin: 0;
        }
        .auth-info-grid {
            display: grid;
            gap: .85rem;
            max-width: 34rem;
        }
        .auth-feature {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
            color: rgba(255, 255, 255, .9);
            font-size: .95rem;
            padding: .85rem .95rem;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: .65rem;
            background: rgba(6, 63, 53, .34);
            backdrop-filter: blur(10px);
        }
        .auth-feature-mark {
            min-width: 28px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            font-weight: 800;
            font-size: .78rem;
        }
        .auth-note {
            max-width: 34rem;
            color: rgba(255, 255, 255, .9);
        }
        .auth-note a {
            color: #fff;
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: .2rem;
        }
        .captcha-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            background: #f9fafb;
            padding: .75rem .9rem;
        }
        .captcha-question {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--telbin-forest);
            letter-spacing: 0;
        }
        .password-toggle-group .form-control {
            border-right: 0;
        }
        .password-toggle {
            width: 56px;
            min-width: 56px;
            border-color: #d0d5dd;
            color: #475467;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .password-toggle:hover,
        .password-toggle:focus {
            background: var(--telbin-soft);
            color: var(--telbin-forest);
            border-color: #79b9a6;
        }
        .login-auth-page .form-control:focus,
        .login-auth-page .form-select:focus,
        .register-auth-page .form-control:focus,
        .register-auth-page .form-select:focus {
            border-color: var(--telbin-forest);
            box-shadow: 0 0 0 .2rem rgba(11, 93, 75, .16);
        }
        .login-auth-page .btn-outline-primary,
        .register-auth-page .btn-outline-primary {
            color: var(--telbin-forest);
            border-color: var(--telbin-forest);
        }
        .login-auth-page .btn-outline-primary:hover,
        .register-auth-page .btn-outline-primary:hover,
        .login-auth-page .btn-outline-primary:focus,
        .register-auth-page .btn-outline-primary:focus {
            background: var(--telbin-forest);
            border-color: var(--telbin-forest);
            color: #fff;
        }
        .login-auth-page .text-primary,
        .register-auth-page .text-primary {
            color: var(--telbin-forest) !important;
        }
        .login-auth-page .captcha-box {
            border-color: var(--telbin-line);
            background: var(--telbin-soft);
        }
        .password-icon {
            width: 22px;
            height: 22px;
            background: currentColor;
            display: inline-block;
        }
        .password-icon-eye {
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M2.06 12.35a1 1 0 0 1 0-.7C3.78 7.35 7.95 4 12 4s8.22 3.35 9.94 7.65a1 1 0 0 1 0 .7C20.22 16.65 16.05 20 12 20s-8.22-3.35-9.94-7.65Z'/%3E%3Ccircle cx='12' cy='12' r='3'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .password-icon-eye-off {
            display: none;
            mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m15 18-.72-3.25'/%3E%3Cpath d='M2 8a10.45 10.45 0 0 0 20 0'/%3E%3Cpath d='m20 15-1.73-2.05'/%3E%3Cpath d='m4 15 1.73-2.05'/%3E%3Cpath d='m9 18 .72-3.25'/%3E%3C/svg%3E") center/contain no-repeat;
        }
        .password-toggle[aria-pressed="true"] .password-icon-eye {
            display: none;
        }
        .password-toggle[aria-pressed="true"] .password-icon-eye-off {
            display: inline-block;
        }
        @media (max-width: 1199.98px) {
            .program-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .program-interest-columns {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 767.98px) {
            .sidebar { min-height: auto !important; }
            .page-title { align-items: flex-start; flex-direction: column; }
            main { padding: 1rem !important; }
            .navbar { min-height: auto; }
            .topbar-user {
                width: 100%;
                justify-content: space-between;
                border-radius: .75rem;
                margin-top: .75rem;
            }
            .topbar-user-name { max-width: 150px; }
            .registration-shell { grid-template-columns: 1fr; }
            .registration-nav {
                position: static;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .registration-nav-link { align-items: flex-start; }
            .school-choice-grid,
            .review-summary { grid-template-columns: 1fr; }
            .achievement-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .domicile-map { min-height: 320px; }
            .sticky-actions { margin-left: -1rem; margin-right: -1rem; border-radius: 0; border-left: 0; border-right: 0; }
            .auth-page { min-height: calc(100vh - 56px); }
            .login-auth-page,
            .register-auth-page { min-height: 100vh; }
            .auth-copy h1 { font-size: 1.6rem; }
            .auth-page::after { height: 64%; }
            .auth-school-badge { width: 100%; }
            .admin-hero {
                align-items: flex-start;
                flex-direction: column;
            }
            .admin-hero-meta {
                width: 100%;
                text-align: left;
            }
            .program-stat-grid {
                grid-template-columns: 1fr;
            }
            .admin-task-list a {
                align-items: flex-start;
                flex-direction: column;
            }
            .admin-task-list span {
                text-align: left;
            }
            .history-header {
                align-items: flex-start;
                flex-direction: column;
            }
            .history-layout {
                grid-template-columns: 1fr;
            }
            .history-side {
                border-left: 0;
                border-top: 1px solid var(--spmb-line);
            }
            .history-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
@unless(request()->routeIs('login', 'register', 'akun.status'))
    <nav class="navbar">
        <div class="container-fluid flex-column flex-md-row align-items-stretch align-items-md-center gap-2 px-3 px-md-4">
            <a class="topbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logotelukbintuni.png') }}" alt="Logo Kabupaten Teluk Bintuni" class="topbar-logo">
                <span>
                    <span class="topbar-brand-title">SPMB SMP Kabupaten Teluk Bintuni</span>
                    <span class="topbar-brand-subtitle">Sistem Penerimaan Murid Baru</span>
                </span>
            </a>
            @isset($pengguna)
                @php
                    $namaPengguna = $pengguna->nama_pengguna ?: $pengguna->id_pengguna;
                    $formulirPengguna = $pengguna->formulirTerbaru;
                    $fotoPengguna = $formulirPengguna?->foto_selfie;
                @endphp
                <div class="topbar-user">
                    <span class="topbar-avatar">
                        @if($fotoPengguna)
                            <img src="{{ $formulirPengguna->berkasUrl('foto_selfie') }}" class="topbar-avatar-photo" alt="Foto {{ $namaPengguna }}">
                        @else
                            <span class="topbar-avatar-icon" aria-hidden="true"></span>
                        @endif
                    </span>
                    <div class="d-none d-sm-block">
                        <div class="topbar-user-name">{{ $namaPengguna }}</div>
                        <div class="topbar-role">{{ $pengguna->roleLabel() }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="post" class="mb-0">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm topbar-logout" data-confirm="Apakah anda yakin akan keluar?">Logout</button>
                    </form>
                </div>
            @endisset
        </div>
    </nav>
@endunless

@if(isset($pengguna) && ! request()->routeIs('akun.status'))
    <div class="container-fluid app-shell">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 sidebar p-3">
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dasbor</a>
                @if($pengguna->isAdminDinas())
                    <a class="sidebar-link {{ request()->routeIs('admin.pengguna', 'admin.verifikasi-akun.*') ? 'active' : '' }}" href="{{ route('admin.pengguna', ['status' => 'menunggu_verifikasi']) }}">Verifikasi Akun</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.pendaftar') ? 'active' : '' }}" href="{{ route('admin.pendaftar') }}">Data Pendaftaran</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.sekolah-zonasi') ? 'active' : '' }}" href="{{ route('admin.sekolah-zonasi') }}">Sekolah & Zonasi</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}" href="{{ route('admin.pengaturan') }}">Pengaturan SPMB</a>
                @elseif($pengguna->isAdminSekolah())
                    <!-- <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dasbor</a> -->
                    <a class="sidebar-link {{ request()->routeIs('sekolah.admin.pendaftar') ? 'active' : '' }}" href="{{ route('sekolah.admin.pendaftar') }}">Data Pendaftar</a>
                    <a class="sidebar-link {{ request()->routeIs('sekolah.admin.kuota') ? 'active' : '' }}" href="{{ route('sekolah.admin.kuota') }}">Kuota Penerimaan</a>
                    <a class="sidebar-link {{ request()->routeIs('sekolah.admin.profil') ? 'active' : '' }}" href="{{ route('sekolah.admin.profil') }}">Profil Sekolah</a>
                @else
                    <span class="sidebar-link active">Dashboard Calon Murid</span>
                @endif
            </aside>
            <main class="col-md-9 col-lg-10 p-4">
                @include('partials.flash')
                {{ $slot }}
            </main>
        </div>
    </div>
@else
    <main class="{{ request()->routeIs('login', 'register', 'akun.status') ? '' : 'container py-5' }}">
        @unless(request()->routeIs('login', 'register', 'akun.status'))
            @include('partials.flash')
        @endunless
        {{ $slot }}
    </main>
@endif

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="confirmModalMessage">
                Apakah anda yakin?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmModalButton">Ya, lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentPreviewTitle">Preview Berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body bg-light" id="documentPreviewBody"></div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="documentDownloadButton">
                    <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M12 3v12"></path>
                        <path d="m7 10 5 5 5-5"></path>
                        <path d="M5 21h14"></path>
                    </svg>
                    Unduh Berkas
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@if(request()->routeIs('admin.pengguna', 'admin.pendaftar', 'admin.pengaturan', 'admin.sekolah-zonasi', 'sekolah.admin.pendaftar'))
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@endif
@if(request()->routeIs('admin.pengguna', 'admin.pendaftar', 'admin.pengaturan', 'sekolah.admin.pendaftar'))
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
@endif
@if(request()->routeIs('admin.sekolah-zonasi'))
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalElement = document.getElementById('confirmModal');
        const modalMessage = document.getElementById('confirmModalMessage');
        const modalButton = document.getElementById('confirmModalButton');

        if (! modalElement || ! modalMessage || ! modalButton) {
            return;
        }

        const confirmModal = new bootstrap.Modal(modalElement);
        let confirmedTarget = null;

        document.querySelectorAll('[data-confirm]').forEach(function (element) {
            element.addEventListener('click', function (event) {
                if (element.dataset.confirmed === 'true') {
                    element.dataset.confirmed = 'false';
                    return;
                }

                event.preventDefault();
                confirmedTarget = element;
                modalMessage.textContent = element.dataset.confirm || 'Apakah anda yakin?';
                confirmModal.show();
            });
        });

        modalButton.addEventListener('click', function () {
            if (! confirmedTarget) {
                return;
            }

            confirmedTarget.dataset.confirmed = 'true';
            confirmModal.hide();
            confirmedTarget.click();
        });

        const documentModalElement = document.getElementById('documentPreviewModal');
        const documentModalTitle = document.getElementById('documentPreviewTitle');
        const documentModalBody = document.getElementById('documentPreviewBody');
        const documentDownloadButton = document.getElementById('documentDownloadButton');
        const documentModal = documentModalElement ? new bootstrap.Modal(documentModalElement) : null;

        document.querySelectorAll('[data-document-preview]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (! documentModal || ! documentModalTitle || ! documentModalBody || ! documentDownloadButton) {
                    return;
                }

                event.preventDefault();
                const title = link.dataset.documentTitle || 'Preview Berkas';
                const type = link.dataset.documentType || 'pdf';
                const url = link.href;

                documentModalTitle.textContent = title;
                documentDownloadButton.href = link.dataset.documentDownload || url;
                documentDownloadButton.setAttribute('download', '');
                documentModalBody.replaceChildren();

                if (type === 'image') {
                    const image = document.createElement('img');
                    image.src = url;
                    image.alt = title;
                    image.className = 'document-preview-image';
                    documentModalBody.appendChild(image);
                } else {
                    const frame = document.createElement('iframe');
                    frame.src = url + '#toolbar=0&navpanes=0';
                    frame.title = title;
                    frame.className = 'document-preview-frame';
                    documentModalBody.appendChild(frame);
                }

                documentModal.show();
            });
        });

        documentModalElement?.addEventListener('hidden.bs.modal', function () {
            documentModalBody?.replaceChildren();
        });

        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.passwordToggle);

                if (! input) {
                    return;
                }

                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Lihat password');
                button.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
            });
        });
    });
</script>
</body>
</html>
