<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Kartu Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            background: #eef2f7;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.3;
        }
        .print-actions {
            max-width: 210mm;
            margin: 18px auto 10px;
        }
        .card-print {
            width: 190mm;
            min-height: 277mm;
            margin: 0 auto 24px;
            background: #fff;
            border: 1px solid #1f2937;
            padding: 8mm;
            position: relative;
        }
        .kop-logo {
            width: 62px;
            height: 62px;
            object-fit: contain;
        }
        .kop {
            border-bottom: 3px double #111827;
            padding-bottom: 8px;
            display: grid;
            grid-template-columns: 66px 1fr 66px;
            align-items: center;
            gap: 8px;
        }
        .kop-title {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.18;
            white-space: nowrap;
        }
        .kop-school {
            font-size: 18px;
            font-weight: 900;
            line-height: 1.18;
        }
        .kop-meta {
            font-size: 11px;
            font-weight: 700;
            font-style: italic;
            line-height: 1.25;
        }
        .card-title {
            padding: 6px 10px 4px;
            margin: 10px 0;
            text-align: center;
        }
        .registration-number {
            border: 1px solid #111827;
            padding: 6px 9px;
            font-weight: 800;
            background: #fff;
        }
        .photo-box {
            width: 35mm;
            height: 46mm;
            border: 1px solid #111827;
            object-fit: cover;
            background: #f9fafb;
        }
        .section-label {
            font-weight: 800;
            margin: 9px 0 5px;
            padding: 5px 8px;
            background: #172033;
            color: #fff;
            border-radius: 2px;
        }
        .info-table {
            margin-bottom: 0;
        }
        .info-table th,
        .info-table td {
            padding: 5px 7px;
            border-color: #cbd5e1;
            vertical-align: top;
        }
        .info-table th {
            width: 36%;
            background: #f8fafc;
            font-weight: 800;
        }
        .compact-table th {
            width: 22%;
        }
        .program-choice {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 800;
        }
        .program-check {
            width: 17px;
            height: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #16a34a;
            color: #fff;
            font-size: 11px;
            font-weight: 900;
        }
        .schedule-table th {
            width: 28%;
        }
        .signature-area {
            display: grid;
            grid-template-columns: 1fr 62mm;
            gap: 10mm;
            align-items: end;
            margin-top: 10px;
        }
        .signature-box {
            text-align: center;
            justify-self: end;
            width: 58mm;
            min-height: 35mm;
        }
        .signature-caption {
            line-height: 1.25;
        }
        .signature-image-wrap {
            height: 18mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 2px 0 1px;
        }
        .signature-image {
            max-height: 18mm;
            max-width: 42mm;
            object-fit: contain;
        }
        .signature-name {
            display: inline-block;
            min-width: 42mm;
            padding-top: 3px;
            font-weight: 800;
        }
        .notes {
            border: 1px solid #111827;
            padding: 7px 9px;
            margin-top: 8px;
            background: #fff;
        }
        .notes-title {
            font-weight: 900;
            margin-bottom: 3px;
        }
        .notes ol {
            margin: 0;
            padding-left: 18px;
        }
        .notes li {
            margin-bottom: 2px;
        }
        .system-note {
            color: #475467;
            font-size: 10px;
        }
        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .card-print {
                width: auto;
                min-height: auto;
                margin: 0;
                border: 0;
                padding: 0;
            }
            a {
                color: inherit;
                text-decoration: none;
            }
        }
    </style>
</head>
<body>
@php
    $settings = $settings ?? \App\Models\PengaturanSpmb::allSettings();
    $tahunPendaftaran = $settings['tahun_pendaftaran'] ?? '2026';
    $tahunPelajaran = $settings['tahun_pelajaran'] ?? '2026/2027';
    $nomorUrutPendaftaran = (int) $formulir->id;
    $nomorPendaftaran = 'SPMB-'.$tahunPendaftaran.'-'.str_pad((string) $nomorUrutPendaftaran, 3, '0', STR_PAD_LEFT);
    $nomorRuang = max(1, (int) ceil($nomorUrutPendaftaran / 25));
    $ruangTes = 'Ruang-'.$nomorRuang;
    $formatTanggalIndonesia = function ($date): string {
        if (! $date) {
            return '-';
        }

        $bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $tanggal = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);

        return $tanggal->format('d').' '.$bulan[(int) $tanggal->format('n')].' '.$tanggal->format('Y');
    };
    $formatTanggalWaktuIndonesia = function ($date) use ($formatTanggalIndonesia): string {
        if (! $date) {
            return '-';
        }

        $tanggal = $date instanceof \Carbon\CarbonInterface ? $date : \Carbon\Carbon::parse($date);

        return $formatTanggalIndonesia($tanggal).' '.$tanggal->format('H:i').' WIT';
    };
    $tanggalTes = $settings['tanggal_tes'] ?? '06 Juli 2026';
    $waktuTes = $settings['waktu_tes'] ?? '08.00 WIT s.d. selesai';
    $tempatTes = $settings['tempat_tes'] ?? 'SMK Negeri 1 Bintuni';
    $kepalaNama = $settings['kepala_nama'] ?? 'Panitia SPMB';
    $kepalaNip = $settings['kepala_nip'] ?? '';
    $kepalaJabatan = $settings['kepala_jabatan'] ?? 'Panitia SPMB';
    $kepalaTtdPath = $settings['kepala_ttd_path'] ?? 'images/ttdketua.png';
    $catatanKartu = collect(preg_split('/\r\n|\r|\n/', $settings['catatan_kartu'] ?? ''))
        ->map(fn (string $line) => trim($line))
        ->filter()
        ->values();
@endphp

<div class="print-actions no-print d-flex justify-content-between align-items-center">
    <a href="{{ route('formulir.riwayat') }}" class="btn btn-outline-secondary">Kembali</a>
    <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<main class="card-print">
    <div class="kop">
        <img src="{{ asset('images/logotelukbintuni.png') }}" class="kop-logo" alt="Logo Kabupaten Teluk Bintuni">
        <div class="text-center">
            <div class="kop-title">PEMERINTAH KABUPATEN TELUK BINTUNI</div>
            <div class="kop-title">DINAS PENDIDIKAN, KEBUDAYAAN, PEMUDA DAN OLAHRAGA</div>
            <div class="kop-school">SMK NEGERI 1 BINTUNI</div>
            <div class="kop-meta">Alamat: Jl. Manimeri - Bintuni | Website: https://smkn1bintuni.sch.id | Email: smknegeri1bintuni@gmail.com | Kode Pos: 98143</div>
        </div>
        <img src="{{ asset('images/logobintuni.jpeg') }}" class="kop-logo" alt="Logo SMK Negeri 1 Bintuni">
    </div>

    <div class="card-title">
        <div class="fw-bold fs-6">KARTU TANDA PENDAFTARAN <br> SISTEM PENERIMAAN MURID BARU TAHUN PELAJARAN {{ $tahunPelajaran }}</div>
    </div>

    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
        <div class="registration-number">No. Pendaftaran: {{ $nomorPendaftaran }}</div>
    </div>

    <div class="row g-3 align-items-start">
        <div class="col-9">
            <div class="section-label">Identitas Peserta</div>
            <table class="table table-bordered info-table">
                <tbody>
                <tr>
                    <th>NISN</th>
                    <td>{{ $formulir->nisn }}</td>
                </tr>
                <tr>
                    <th>Nama Lengkap</th>
                    <td>{{ $formulir->nama }}</td>
                </tr>
                <tr>
                    <th>Tempat, Tanggal Lahir</th>
                    <td>{{ $formulir->tempat_lahir }}, {{ $formatTanggalIndonesia($formulir->tanggal_lahir) }}</td>
                </tr>
                <tr>
                    <th>Jenis Kelamin</th>
                    <td>{{ $formulir->jenis_kelamin }}</td>
                </tr>
                <tr>
                    <th>Asal Sekolah</th>
                    <td>{{ $formulir->asal_sekolah }}</td>
                </tr>
                <tr>
                    <th>No HP / WA</th>
                    <td>{{ $formulir->hp }}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-3 text-center">
            <div class="section-label">Pas Foto</div>
            <img src="{{ $formulir->berkasUrl('foto_selfie') }}" class="photo-box" alt="Foto peserta">
        </div>
    </div>

    <div class="section-label">Pilihan Program Keahlian</div>
    <table class="table table-bordered info-table compact-table">
        <tbody>
        <tr>
            <td>
                <span class="program-choice"><span class="program-check">&#10003;</span>{{ $formulir->program_keahlian_1 }}</span>
            </td>
            <td>
                <span class="program-choice"><span class="program-check">&#10003;</span>{{ $formulir->program_keahlian_2 }}</span>
            </td>
        </tr>
        </tbody>
    </table>

    <div class="section-label">Jadwal Wawancara & Pemetaan Jurusan</div>
    <table class="table table-bordered info-table schedule-table">
        <tbody>
        <tr>
            <th>Tanggal</th>
            <td>{{ $tanggalTes }}</td>
        </tr>
        <tr>
            <th>Waktu</th>
            <td>{{ $waktuTes }}</td>
        </tr>
        <tr>
            <th>Tempat</th>
            <td>{{ $tempatTes }}</td>
        </tr>
        <tr>
            <th>Ruang</th>
            <td>{{ $ruangTes }}</td>
        </tr>
        </tbody>
    </table>

    <div class="signature-area">
        <div class="system-note">
            Kartu ini dicetak dari sistem SPMB SMK Negeri 1 Bintuni dan digunakan sebagai bukti pendaftaran saat mengikuti tahap wawancara dan pemetaan jurusan.
        </div>
        <div class="signature-box">
            <div class="signature-caption">Bintuni, {{ $formatTanggalIndonesia(now()) }}</div>
            <div class="signature-caption">{{ $kepalaJabatan }}</div>
            <div class="signature-image-wrap">
                @if($kepalaTtdPath)
                    <img src="{{ asset($kepalaTtdPath) }}" class="signature-image" alt="Tanda tangan {{ $kepalaNama }}">
                @endif
            </div>
            <div class="signature-name">{{ $kepalaNama }}</div>
            @if($kepalaNip)
                <div>NIP. {{ $kepalaNip }}</div>
            @endif
        </div>
    </div>

    <div class="notes">
        <div class="notes-title">Perhatian:</div>
        <ol>
            @forelse($catatanKartu as $catatan)
                <li>{{ $catatan }}</li>
            @empty
                <li>Peserta wajib mengikuti tahap wawancara dan pemetaan jurusan sesuai jadwal yang tercantum pada kartu ini.</li>
            @endforelse
        </ol>
    </div>
</main>
</body>
</html>
