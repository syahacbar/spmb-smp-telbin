<x-layouts.app :pengguna="$pengguna" title="Kuota Penerimaan – {{ $sekolah->nama }}">
    <style>
        .kuota-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(135deg, #063f35, #0b5d4b 55%, #0788a8);
            color: #fff;
            padding: 1.6rem;
            box-shadow: 0 18px 42px rgba(6, 63, 53, .22);
        }
        .kuota-hero::after {
            content: "";
            position: absolute;
            right: -4rem;
            bottom: -6rem;
            width: 16rem;
            height: 16rem;
            border: 2rem solid rgba(242, 184, 75, .13);
            border-radius: 50%;
        }
        .kuota-hero > * { position: relative; z-index: 1; }
        .kuota-kicker {
            color: #f2b84b;
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .kuota-card {
            border: 1px solid #d8e8e2;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 12px 30px rgba(16, 55, 47, .06);
            overflow: hidden;
        }
        .kuota-card .card-header {
            background: linear-gradient(90deg, #eef7f3, #fff);
            border-bottom: 1px solid #d8e8e2;
            padding: 1rem 1.25rem;
        }
        .jalur-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1fr 1fr;
            gap: 1rem;
            align-items: center;
            padding: 1.1rem 1.25rem;
            border-bottom: 1px solid #edf4f1;
        }
        .jalur-row:last-child { border-bottom: 0; }
        .jalur-row-header {
            background: #f8fafc;
            border-bottom: 1px solid #d8e8e2;
            padding: .6rem 1.25rem;
        }
        .jalur-row-header > * {
            color: #475467;
            font-size: .78rem;
            font-weight: 800;
            text-transform: uppercase;
        }
        .jalur-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: .6rem;
            font-weight: 900;
            font-size: .8rem;
        }
        .jalur-domisili { background: #e0f2fe; color: #0369a1; }
        .jalur-prestasi { background: #fef9c3; color: #854d0e; }
        .jalur-afirmasi { background: #dcfce7; color: #166534; }
        .jalur-mutasi   { background: #ede9fe; color: #6d28d9; }
        .kuota-input {
            width: 90px;
            text-align: center;
            font-weight: 800;
            font-size: 1.05rem;
            border-color: #b0d4c8;
        }
        .kuota-input:focus {
            border-color: #0b5d4b;
            box-shadow: 0 0 0 .2rem rgba(11, 93, 75, .15);
        }
        .sisa-bar-track {
            height: 8px;
            border-radius: 999px;
            background: #e5e7eb;
            overflow: hidden;
            min-width: 80px;
        }
        .sisa-bar-fill {
            height: 100%;
            border-radius: inherit;
            transition: width .4s ease;
        }
        .sisa-ok   { background: #16a34a; }
        .sisa-warn { background: #eab308; }
        .sisa-over { background: #dc2626; }
        @media (max-width: 767.98px) {
            .jalur-row, .jalur-row-header { grid-template-columns: 1fr 1fr; }
        }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Kuota Penerimaan</h3>
            <div class="text-muted">Atur jumlah siswa yang diterima per jalur pendaftaran</div>
        </div>
    </div>

    {{-- Hero --}}
    <div class="kuota-hero mb-4">
        <div class="kuota-kicker">{{ $sekolah->npsn ? 'NPSN '.$sekolah->npsn.' · ' : '' }}Kuota Penerimaan</div>
        <h2 class="fw-bold mt-1 mb-1">{{ $sekolah->nama }}</h2>
        <p class="mb-0" style="color:rgba(255,255,255,.78)">
            Tetapkan kuota per jalur pendaftaran untuk periode SPMB aktif.
            Kuota yang disimpan di sini akan ditampilkan kepada calon pendaftar.
        </p>
    </div>

    <form action="{{ route('sekolah.admin.kuota.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="kuota-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <div>
                    <div class="fw-bold">Kuota per Jalur Pendaftaran</div>
                    <div class="small text-muted">Total pendaftar dihitung dari formulir dengan status <em>final</em></div>
                </div>
                <button type="submit" class="btn btn-primary px-4">Simpan Kuota</button>
            </div>

            <div class="jalur-row jalur-row-header">
                <div>Jalur Pendaftaran</div>
                <div>Kuota Ditetapkan</div>
                <div>Sudah Mendaftar</div>
                <div>Sisa / Status</div>
            </div>

            @forelse($jalurs as $jalur)
                @php
                    $kode   = $jalur->kode;
                    $kuota  = (int) ($kuotas[$jalur->id] ?? 0);
                    $daftar = (int) ($pendaftarPerJalur[$jalur->id] ?? 0);
                    $sisa   = $kuota - $daftar;
                    $pct    = $kuota > 0 ? min(100, round(($daftar / $kuota) * 100)) : 0;
                    $fillClass = $pct >= 100 ? 'sisa-over' : ($pct >= 75 ? 'sisa-warn' : 'sisa-ok');
                    $badgeClass = match($kode) {
                        'domisili' => 'jalur-domisili',
                        'prestasi' => 'jalur-prestasi',
                        'afirmasi' => 'jalur-afirmasi',
                        'mutasi'   => 'jalur-mutasi',
                        default    => 'jalur-domisili',
                    };
                    $icon = match($kode) {
                        'domisili' => '🏡',
                        'prestasi' => '🏆',
                        'afirmasi' => '🤝',
                        'mutasi'   => '🔄',
                        default    => '📋',
                    };
                @endphp
                <div class="jalur-row">
                    <div class="d-flex align-items-center gap-3">
                        <span class="jalur-badge {{ $badgeClass }}">{{ $icon }}</span>
                        <div>
                            <div class="fw-bold">{{ $jalur->nama }}</div>
                            <div class="small text-muted">{{ $jalur->deskripsi }}</div>
                        </div>
                    </div>
                    <div>
                        <input type="number" name="kuota[{{ $jalur->id }}]" id="kuota_{{ $jalur->id }}"
                            class="form-control kuota-input" value="{{ old('kuota.'.$jalur->id, $kuota) }}"
                            min="0" max="9999" required aria-label="Kuota {{ $jalur->nama }}">
                        @error('kuota.'.$jalur->id)<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <span class="fw-bold fs-5">{{ $daftar }}</span>
                        <span class="text-muted small"> pendaftar</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="sisa-bar-track flex-grow-1">
                                <div class="sisa-bar-fill {{ $fillClass }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="fw-bold small {{ $sisa < 0 ? 'text-danger' : ($sisa === 0 ? 'text-warning' : 'text-success') }}">
                                @if($kuota === 0) <span class="text-muted">—</span>
                                @elseif($sisa < 0) Melebihi ({{ abs($sisa) }})
                                @elseif($sisa === 0) Penuh
                                @else Sisa {{ $sisa }}
                                @endif
                            </span>
                        </div>
                        <div class="small text-muted mt-1">{{ $pct }}% terisi</div>
                    </div>
                </div>
            @empty
                <div class="p-5 text-center text-muted">Belum ada jalur pendaftaran aktif.</div>
            @endforelse
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-5">Simpan Semua Kuota</button>
        </div>
    </form>
</x-layouts.app>
