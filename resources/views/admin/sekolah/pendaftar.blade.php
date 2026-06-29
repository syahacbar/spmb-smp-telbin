<x-layouts.app :pengguna="$pengguna" title="Data Pendaftar – {{ $sekolah->nama }}">
    <style>
        .pendaftar-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(135deg, #063f35, #0b5d4b 55%, #0788a8);
            color: #fff;
            padding: 1.6rem;
            box-shadow: 0 18px 42px rgba(6, 63, 53, .22);
        }
        .pendaftar-hero::after {
            content: "";
            position: absolute;
            right: -4rem;
            bottom: -6rem;
            width: 16rem;
            height: 16rem;
            border: 2rem solid rgba(242, 184, 75, .13);
            border-radius: 50%;
        }
        .pendaftar-hero > * { position: relative; z-index: 1; }
        .hero-kicker {
            color: #f2b84b;
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .summary-pill-group {
            display: flex;
            flex-wrap: wrap;
            gap: .6rem;
            margin-top: .85rem;
        }
        .summary-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: 999px;
            padding: .35rem .85rem;
            font-size: .82rem;
            font-weight: 700;
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,.2);
            backdrop-filter: blur(4px);
            text-decoration: none;
            transition: background .2s;
        }
        .summary-pill:hover, .summary-pill.active {
            background: rgba(255,255,255,.28);
            color: #fff;
        }
        .summary-pill .pill-count {
            background: rgba(255,255,255,.9);
            color: #0b5d4b;
            border-radius: 999px;
            padding: 0 .5rem;
            font-weight: 900;
        }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .75rem;
            padding: 1rem 1.25rem;
            background: #f8fafc;
            border-bottom: 1px solid #d8e8e2;
            border-radius: 1rem 1rem 0 0;
        }
        .filter-btn {
            border-radius: 999px;
            padding: .35rem 1rem;
            font-weight: 700;
            font-size: .85rem;
            border: 1px solid #b0d4c8;
            background: #fff;
            color: #0b5d4b;
            text-decoration: none;
            transition: all .15s;
        }
        .filter-btn:hover { background: #e4f3ed; border-color: #0b5d4b; color: #063f35; }
        .filter-btn.active { background: #0b5d4b; border-color: #0b5d4b; color: #fff; }
        .pendaftar-table-wrap {
            border: 1px solid #d8e8e2;
            border-radius: 1rem;
            box-shadow: 0 12px 30px rgba(16, 55, 47, .06);
            overflow: hidden;
        }
        .table > thead th { color: #475467; font-size: .79rem; text-transform: uppercase; }
        .jalur-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            border-radius: 999px;
            padding: .2rem .65rem;
            font-size: .78rem;
            font-weight: 700;
        }
        .chip-domisili { background: #e0f2fe; color: #0369a1; }
        .chip-prestasi { background: #fef9c3; color: #854d0e; }
        .chip-afirmasi { background: #dcfce7; color: #166534; }
        .chip-mutasi   { background: #ede9fe; color: #6d28d9; }
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: .78rem;
            font-weight: 900;
            background: #fef9c3;
            color: #854d0e;
        }
        .rank-badge.top3 { background: #f2b84b; color: #7c2d12; }
        .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #d8e8e2;
        }
        .avatar-placeholder-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e4f3ed;
            color: #0b5d4b;
            font-weight: 800;
            font-size: .85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .dt-search label, .dt-length label { color: #667085; font-weight: 700; }
        .dt-search input, .dt-length select, .column-filter {
            border-color: #d0d5dd;
            border-radius: .45rem;
        }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data Pendaftar</h3>
            <div class="text-muted">Calon murid yang memilih {{ $sekolah->nama }}</div>
        </div>
    </div>

    {{-- Hero with filter pills --}}
    <div class="pendaftar-hero mb-4">
        <div class="hero-kicker">{{ $sekolah->npsn ? 'NPSN '.$sekolah->npsn.' · ' : '' }}Data Pendaftar</div>
        <h2 class="fw-bold mt-1 mb-1">{{ $sekolah->nama }}</h2>

        <div class="summary-pill-group">
            <a href="{{ route('sekolah.admin.pendaftar', array_filter(['status' => $statusFilter])) }}"
               class="summary-pill {{ $jalurFilter === '' ? 'active' : '' }}">
                Semua Jalur
                <span class="pill-count">{{ $formulirs->count() }}</span>
            </a>
            @foreach($jalurs as $jalur)
                @php $count = (int)($countPerJalur[$jalur->id] ?? 0); @endphp
                <a href="{{ route('sekolah.admin.pendaftar', array_filter(['jalur' => $jalur->kode, 'status' => $statusFilter])) }}"
                   class="summary-pill {{ $jalurFilter === $jalur->kode ? 'active' : '' }}">
                    {{ $jalur->nama }}
                    <span class="pill-count">{{ $count }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="pendaftar-table-wrap">
        <div class="filter-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-bold text-muted small">Filter status:</span>
                <a href="{{ route('sekolah.admin.pendaftar', array_filter(['jalur' => $jalurFilter])) }}"
                   class="filter-btn {{ $statusFilter === '' ? 'active' : '' }}">Semua</a>
                <a href="{{ route('sekolah.admin.pendaftar', array_filter(['jalur' => $jalurFilter, 'status' => 'submitted'])) }}"
                   class="filter-btn {{ $statusFilter === 'submitted' ? 'active' : '' }}">Belum Diproses</a>
                <a href="{{ route('sekolah.admin.pendaftar', array_filter(['jalur' => $jalurFilter, 'status' => 'diterima'])) }}"
                   class="filter-btn {{ $statusFilter === 'diterima' ? 'active' : '' }}">Diterima</a>
                <a href="{{ route('sekolah.admin.pendaftar', array_filter(['jalur' => $jalurFilter, 'status' => 'ditolak'])) }}"
                   class="filter-btn {{ $statusFilter === 'ditolak' ? 'active' : '' }}">Ditolak</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('sekolah.admin.pendaftar.ekspor', array_filter(['jalur' => $jalurFilter, 'status' => $statusFilter])) }}" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1.5" title="Unduh data pendaftar ke file Excel/CSV">
                    <i class="bi bi-file-earmark-excel-fill"></i> Ekspor Excel
                </a>
                <a href="{{ route('sekolah.admin.pendaftar.pdf', array_filter(['jalur' => $jalurFilter, 'status' => $statusFilter])) }}" target="_blank" class="btn btn-sm btn-danger fw-bold d-inline-flex align-items-center gap-1.5" title="Unduh data pendaftar ke file PDF / Cetak">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                </a>
            </div>
        </div>

        <div class="table-responsive p-3">
            <table id="pendaftarTable" class="table table-hover align-middle mb-0 w-100">
                <thead>
                    <tr>
                        <th style="width:46px">No</th>
                        <th>Nama / NISN</th>
                        <th>Asal Sekolah</th>
                        <th>Jalur</th>
                        <th>Nilai TKA</th>
                        <th>Peringkat Prestasi</th>
                        <th>Status</th>
                        <th>Tanggal Dikirim</th>
                        <th class="text-center" style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formulirs as $formulir)
                        @php
                            $siswa    = $formulir->pengguna?->calonSiswa;
                            $matScore = $siswa?->nilai_tka_matematika;
                            $bIndScore= $siswa?->nilai_tka_bahasa_indonesia;
                            $rataScore= ($matScore !== null && $bIndScore !== null)
                                ? number_format(((float)$matScore + (float)$bIndScore) / 2, 2)
                                : '-';
                            $rank     = $prestasiRanks[$formulir->id] ?? null;
                            $jalurKode= $formulir->jalur?->kode ?? '';
                            $chipClass= match($jalurKode) {
                                'domisili' => 'chip-domisili',
                                'prestasi' => 'chip-prestasi',
                                'afirmasi' => 'chip-afirmasi',
                                'mutasi'   => 'chip-mutasi',
                                default    => 'chip-domisili',
                            };
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($formulir->foto_selfie)
                                        <a href="{{ $formulir->berkasUrl('foto_selfie') }}" target="_blank" title="Lihat foto penuh">
                                            <img src="{{ $formulir->berkasUrl('foto_selfie') }}" class="avatar-sm" alt="Foto {{ $formulir->nama }}">
                                        </a>
                                    @else
                                        <div class="avatar-placeholder-sm">{{ strtoupper(substr($formulir->nama, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark">{{ $formulir->nama }}</div>
                                        <div class="small text-muted mb-1">{{ $formulir->nisn }}</div>
                                        @if($formulir->dokumen_pendukung)
                                            <a href="{{ $formulir->berkasUrl('dokumen_pendukung') }}" target="_blank"
                                               class="badge bg-light text-primary text-decoration-none border" style="font-size: 0.72rem;">
                                                <i class="bi bi-file-earmark-pdf"></i> Lihat Berkas
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $formulir->asal_sekolah ?: '-' }}</td>
                            <td>
                                <span class="jalur-chip {{ $chipClass }}">{{ $formulir->jalur?->nama ?? '-' }}</span>
                            </td>
                            <td data-order="{{ $rataScore === '-' ? -1 : $rataScore }}">
                                @if($rataScore !== '-')
                                    <div class="fw-bold">{{ $rataScore }}</div>
                                    <div class="small text-muted">Mat: {{ $matScore }} | B.Ind: {{ $bIndScore }}</div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td data-order="{{ $rank ?? 9999 }}">
                                @if($rank !== null)
                                    <span class="rank-badge {{ $rank <= 3 ? 'top3' : '' }}">#{{ $rank }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($formulir->status === 'diterima')
                                    <span class="badge text-bg-success">Diterima</span>
                                @elseif($formulir->status === 'ditolak')
                                    <span class="badge text-bg-danger">Ditolak</span>
                                @elseif($formulir->status === 'submitted')
                                    <span class="badge text-bg-info">Belum Diproses</span>
                                @else
                                    <span class="badge text-bg-warning">Draft</span>
                                @endif
                            </td>
                            <td data-order="{{ $formulir->submitted_at?->timestamp ?? 0 }}">
                                {{ $formulir->submitted_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($formulir->status === 'submitted')
                                    @if($tombolAktif)
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <form action="{{ route('sekolah.admin.pendaftar.terima', $formulir) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin MENERIMA pendaftar ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success fw-bold d-inline-flex align-items-center gap-1 py-1 px-2" title="Terima Pendaftar" style="font-size: 0.8rem;">
                                                    <i class="bi bi-check-circle-fill"></i> Terima
                                                </button>
                                            </form>
                                            <form action="{{ route('sekolah.admin.pendaftar.tolak', $formulir) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK pendaftar ini?')">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger fw-bold d-inline-flex align-items-center gap-1 py-1 px-2" title="Tolak Pendaftar" style="font-size: 0.8rem;">
                                                    <i class="bi bi-x-circle-fill"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center gap-1" data-bs-toggle="tooltip" title="Tombol Terima/Tolak tidak aktif. Hubungi Admin Dinas Pendidikan.">
                                            <button type="button" class="btn btn-sm btn-secondary fw-bold py-1 px-2 disabled" style="font-size: 0.8rem; opacity:.55; cursor:not-allowed;" aria-disabled="true">
                                                <i class="bi bi-lock-fill"></i> Terkunci
                                            </button>
                                        </div>
                                    @endif
                                @elseif($formulir->status === 'diterima' || $formulir->status === 'ditolak')
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <span class="{{ $formulir->status === 'diterima' ? 'text-success' : 'text-danger' }} fw-bold small">
                                            <i class="bi {{ $formulir->status === 'diterima' ? 'bi-check-lg' : 'bi-x-lg' }}"></i> 
                                            {{ $formulir->status === 'diterima' ? 'Diterima' : 'Ditolak' }}
                                        </span>
                                        @if($tombolAktif)
                                        <form action="{{ route('sekolah.admin.pendaftar.reset', $formulir) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan keputusan status pendaftar ini?')">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-xs btn-outline-secondary py-0.5 px-1.5 small fw-semibold" style="font-size: 0.72rem; padding: 1px 6px; border-radius: 4px;">
                                                Batalkan
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        {{-- DataTables akan menampilkan pesan emptyTable. Jangan render row colspan karena dibaca sebagai data. --}}
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emptyTableMessage = @json($jalurFilter
                ? 'Belum ada pendaftar untuk jalur '.($jalurs->firstWhere('kode', $jalurFilter)?->nama ?? $jalurFilter).'.'
                : 'Belum ada calon murid yang memilih sekolah ini.'
            );

            // Bootstrap tooltips
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                new bootstrap.Tooltip(el);
            });

            const tableEl = document.getElementById('pendaftarTable');
            if (! tableEl || ! window.DataTable) return;

            new DataTable(tableEl, {
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [0, 8] },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ pendaftar',
                    infoEmpty: 'Tidak ada data',
                    infoFiltered: '(difilter dari _MAX_ total)',
                    zeroRecords: 'Tidak ada pendaftar yang sesuai pencarian',
                    emptyTable: emptyTableMessage,
                    paginate: { first: 'Awal', last: 'Akhir', next: 'Berikutnya', previous: 'Sebelumnya' },
                },
            });
        });
    </script>
</x-layouts.app>
