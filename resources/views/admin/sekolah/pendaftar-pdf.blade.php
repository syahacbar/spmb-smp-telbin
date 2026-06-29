<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="description" content="Laporan pendaftar SPMB SMP Kabupaten Teluk Bintuni untuk sekolah, berisi data calon murid, jalur penerimaan, nilai, dan status seleksi.">
    <title>Laporan Pendaftar - {{ $sekolah->nama }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        body {
            background: #fff;
            color: #111827;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            line-height: 1.25;
            padding: 0;
            margin: 0;
        }
        .header-section {
            border-bottom: 2px solid #111827;
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-title h1 {
            font-size: 14px;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
        }
        .header-title h2 {
            font-size: 11px;
            font-weight: 700;
            margin: 2px 0 0;
            color: #4b5563;
        }
        .header-meta {
            text-align: right;
            font-size: 8px;
            color: #4b5563;
        }
        .table-pdf {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table-pdf th {
            background: #f3f4f6;
            color: #111827;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 7px;
            border: 1.5px solid #9ca3af;
            padding: 4px 5px;
            text-align: left;
            vertical-align: middle;
        }
        .table-pdf td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: top;
        }
        .table-pdf tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge-pdf {
            display: inline-block;
            padding: 1.5px 4px;
            font-size: 6.5px;
            font-weight: 800;
            border-radius: 3px;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        
        .no-print-bar {
            background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #fff;
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar no-print">
    <div>
        <strong style="font-size: 11px;">Pratinjau PDF Cetak Laporan Pendaftar</strong>
    </div>
    <div>
        <button class="btn btn-sm btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak / Simpan ke PDF</button>
    </div>
</div>

<div class="p-3">
    <div class="header-section">
        <div class="header-title">
            <h1>Laporan Data Pendaftaran Calon Murid Baru</h1>
            <h2>{{ $sekolah->nama }} {{ $sekolah->npsn ? '(NPSN: '.$sekolah->npsn.')' : '' }}</h2>
        </div>
        <div class="header-meta">
            <div>Tahun Pelajaran: {{ $settings['tahun_pelajaran'] ?? '2026/2027' }}</div>
            <div>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIT</div>
            <div>Filter: 
                Jalur: <strong>{{ $jalurFilter ?: 'Semua' }}</strong> | 
                Status: <strong>{{ $statusFilter === 'submitted' ? 'Belum Diproses' : ($statusFilter ? ucfirst($statusFilter) : 'Semua') }}</strong>
            </div>
        </div>
    </div>

    <table class="table-pdf">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 140px;">Identitas Calon Siswa</th>
                <th style="width: 130px;">Biodata Diri</th>
                <th style="width: 130px;">Domisili Siswa</th>
                <th style="width: 140px;">Orang Tua / Wali</th>
                <th style="width: 120px;">Detail Pendaftaran</th>
                <th style="width: 75px;">Akademik (TKA)</th>
                <th style="width: 65px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($formulirs as $f)
                @php
                    $user = $f->pengguna;
                    $calonSiswa = $user?->calonSiswa;
                    $mat = $calonSiswa?->nilai_tka_matematika;
                    $bind = $calonSiswa?->nilai_tka_bahasa_indonesia;
                    $avg = ($mat !== null && $bind !== null)
                        ? number_format(((float) $mat + (float) $bind) / 2, 2)
                        : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong style="font-size: 8px; color: #111827;">{{ $f->nama }}</strong>
                        <div style="margin-top: 2px;">NISN: {{ $f->nisn }}</div>
                        <div>NIK: {{ $f->nik }}</div>
                    </td>
                    <td>
                        <div>TTL: {{ $f->tempat_lahir }}, {{ $f->tanggal_lahir?->translatedFormat('d M Y') ?? '-' }}</div>
                        <div>JK: {{ $f->jenis_kelamin }} | Agama: {{ $f->agama }}</div>
                        <div>HP: {{ preg_replace('/^62/', '0', $user?->telpon ?? '') }}</div>
                        <div>Email: {{ $user?->email ?: '-' }}</div>
                    </td>
                    <td>
                        <div>{{ $f->alamat }}</div>
                        <div style="margin-top: 2px; font-style: italic;">
                            Kel. {{ $f->alamat_kelurahan }}<br>
                            Kec. {{ $f->alamat_kecamatan }}<br>
                            Kab. {{ $f->alamat_kabupaten }}
                        </div>
                    </td>
                    <td>
                        <div><strong>Ayah:</strong> {{ $f->nama_ayah }} ({{ $f->pekerjaan_ayah }})</div>
                        <div style="margin-top: 1px;"><strong>Ibu:</strong> {{ $f->nama_ibu }} ({{ $f->pekerjaan_ibu }})</div>
                        <div style="margin-top: 1.5px; font-size: 7.5px;">HP Ortu: {{ $f->hp_ortu }}</div>
                        <div style="font-size: 7px; color: #4b5563;">Alamat: {{ $f->alamat_ortu ?: 'Sama dengan siswa' }}</div>
                    </td>
                    <td>
                        <div>Asal: {{ $f->asal_sekolah ?: '-' }}</div>
                        <div style="margin-top: 2px;">Jalur: <span style="font-weight: 700;">{{ $f->jalur?->nama ?? '-' }}</span></div>
                        <div style="font-size: 7px; color: #4b5563; margin-top: 2px;">Tgl Kirim: {{ $f->submitted_at?->translatedFormat('d/m/Y H:i') ?? '-' }}</div>
                    </td>
                    <td>
                        <div>MTK: <strong>{{ $mat !== null ? $mat : '-' }}</strong></div>
                        <div>B.IND: <strong>{{ $bind !== null ? $bind : '-' }}</strong></div>
                        <div style="margin-top: 2.5px; border-top: 1px solid #cbd5e1; padding-top: 1.5px;">Rerata: <strong style="color: #0369a1;">{{ $avg }}</strong></div>
                    </td>
                    <td>
                        @if($f->status === 'diterima')
                            <span class="badge-pdf badge-success">Diterima</span>
                        @elseif($f->status === 'ditolak')
                            <span class="badge-pdf badge-danger">Ditolak</span>
                        @else
                            <span class="badge-pdf badge-info">Belum Diproses</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">Tidak ada data pendaftar yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Otomatis trigger print dialog
        setTimeout(() => {
            window.print();
        }, 300);
    });
</script>
</body>
</html>
