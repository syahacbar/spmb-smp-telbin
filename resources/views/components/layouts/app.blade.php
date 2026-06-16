<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'SPMB SMKN 1 Bintuni' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --spmb-red: #b91c1c;
            --spmb-red-dark: #7f1d1d;
            --spmb-ink: #172033;
            --spmb-muted: #667085;
            --spmb-line: #e5e7eb;
            --spmb-soft: #f6f8fb;
            --spmb-sidebar: #101828;
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
        .sidebar-link.active { box-shadow: inset 3px 0 0 #ef4444; }
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
            background: #fee2e2;
            color: var(--spmb-red);
            font-weight: 800;
        }
        .form-control,
        .form-select,
        .input-group-text {
            border-color: #d0d5dd;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 .2rem rgba(239, 68, 68, .16);
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
            padding: .8rem;
            border: 1px solid transparent;
            border-radius: .6rem;
            color: var(--spmb-ink);
            text-decoration: none;
        }
        .registration-nav-link:hover {
            background: #f8fafc;
            border-color: #dbe4f0;
        }
        .registration-nav-link span {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 800;
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
        .upload-box-modern {
            display: flex;
            flex-direction: column;
            gap: .35rem;
            border-color: #dbe4f0;
            box-shadow: 0 10px 24px rgba(16, 24, 40, .05);
        }
        .uploaded-file {
            border: 1px dashed #bfdbfe;
            border-radius: .5rem;
            background: #eff6ff;
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
            border: 1px solid #bfdbfe;
            border-radius: .65rem;
            background: #eff6ff;
        }
        .same-address-callout .form-check-input {
            width: 1.15rem;
            height: 1.15rem;
            border-color: #2563eb;
        }
        .same-address-callout .form-check-input:checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .same-address-callout .form-check-label {
            color: #1e3a8a;
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
            background: #fee2e2;
            color: var(--spmb-red);
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
            border: 1px solid var(--spmb-line);
            border-radius: .5rem;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(8px);
            padding: .85rem;
            box-shadow: 0 -8px 24px rgba(16, 24, 40, .08);
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
                url("{{ asset('images/kop.jpg') }}") center/cover;
            z-index: -2;
        }
        .login-auth-page::before {
            background:
                linear-gradient(110deg, rgba(15, 23, 42, .86) 0%, rgba(30, 64, 175, .76) 48%, rgba(14, 116, 144, .56) 100%),
                url("{{ asset('images/login-vokasi-bg.png') }}") center/cover;
        }
        .register-auth-page::before {
            background:
                linear-gradient(110deg, rgba(15, 23, 42, .86) 0%, rgba(30, 64, 175, .74) 48%, rgba(14, 116, 144, .56) 100%),
                url("{{ asset('images/register-vokasi-bg.png') }}") center/cover;
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
            background: #1d4ed8;
            border-color: #1d4ed8;
        }
        .login-auth-page .btn-primary:hover,
        .register-auth-page .btn-primary:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        .login-auth-page a,
        .register-auth-page a {
            color: #1d4ed8;
        }
        .auth-panel {
            border: 0;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 28px 80px rgba(15, 23, 42, .28);
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
            background: rgba(15, 23, 42, .24);
            box-shadow: 0 16px 36px rgba(15, 23, 42, .18);
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
            background: rgba(15, 23, 42, .26);
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
            color: #1d4ed8;
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
            background: #f9fafb;
            color: #1d4ed8;
            border-color: #d0d5dd;
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
@unless(request()->routeIs('login', 'register', 'status'))
    <nav class="navbar">
        <div class="container-fluid flex-column flex-md-row align-items-stretch align-items-md-center gap-2 px-3 px-md-4">
            <a class="topbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logobintuni.jpeg') }}" alt="Logo" class="topbar-logo">
                <span>
                    <span class="topbar-brand-title">SPMB SMK Negeri 1 Bintuni</span>
                    <span class="topbar-brand-subtitle">Sistem Penerimaan Murid Baru</span>
                </span>
            </a>
            @isset($pengguna)
                @php
                    $namaPengguna = $pengguna->nama_pengguna ?: $pengguna->id_pengguna;
                    $fotoPengguna = $pengguna->formulirTerbaru?->foto_selfie;
                @endphp
                <div class="topbar-user">
                    <span class="topbar-avatar">
                        @if($fotoPengguna)
                            <img src="{{ asset($fotoPengguna) }}" class="topbar-avatar-photo" alt="Foto {{ $namaPengguna }}">
                        @else
                            <span class="topbar-avatar-icon" aria-hidden="true"></span>
                        @endif
                    </span>
                    <div class="d-none d-sm-block">
                        <div class="topbar-user-name">{{ $namaPengguna }}</div>
                        <div class="topbar-role">{{ $pengguna->level }}</div>
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

@isset($pengguna)
    <div class="container-fluid app-shell">
        <div class="row min-vh-100">
            <aside class="col-md-3 col-lg-2 sidebar p-3">
                <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dasbor</a>
                @if($pengguna->level === 'Administrator')
                    <a class="sidebar-link {{ request()->routeIs('admin.pendaftar') ? 'active' : '' }}" href="{{ route('admin.pendaftar') }}">Data Registrasi</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.pengguna') ? 'active' : '' }}" href="{{ route('admin.pengguna') }}">Data User</a>
                    <a class="sidebar-link {{ request()->routeIs('admin.pengaturan') ? 'active' : '' }}" href="{{ route('admin.pengaturan') }}">Pengaturan SPMB</a>
                @else
                    <a class="sidebar-link {{ request()->routeIs('formulir.create', 'formulir.edit', 'formulir.periksa') ? 'active' : '' }}" href="{{ route('formulir.create') }}">Formulir Registrasi</a>
                    <a class="sidebar-link {{ request()->routeIs('formulir.riwayat') ? 'active' : '' }}" href="{{ route('formulir.riwayat') }}">Riwayat Registrasi</a>
                @endif
            </aside>
            <main class="col-md-9 col-lg-10 p-4">
                @include('partials.flash')
                {{ $slot }}
            </main>
        </div>
    </div>
@else
    <main class="{{ request()->routeIs('login', 'register', 'status') ? '' : 'container py-5' }}">
        @unless(request()->routeIs('login', 'register', 'status'))
            @include('partials.flash')
        @endunless
        {{ $slot }}
    </main>
@endisset

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
