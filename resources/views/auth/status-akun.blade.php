<x-layouts.app title="Status Registrasi Akun">
    @php
        $status = $registrasi?->status ?? 'menunggu_verifikasi';
        $statusMeta = match ($status) {
            'terverifikasi' => ['success', 'Akun Terverifikasi', 'Akun sudah aktif dan dapat digunakan untuk melanjutkan pendaftaran.'],
            'perlu_perbaikan' => ['warning', 'Perlu Perbaikan', 'Periksa catatan Dinas Pendidikan lalu kirim ulang data yang benar.'],
            'ditolak' => ['danger', 'Registrasi Ditolak', 'Registrasi belum dapat disetujui. Hubungi Admin Dinas bila memerlukan penjelasan.'],
            default => ['info', 'Menunggu Verifikasi', 'Alamat domisili dan Kartu Keluarga sedang diperiksa oleh Dinas Pendidikan.'],
        };
    @endphp

    <div class="container py-4 py-lg-5" style="max-width: 960px">
        @include('partials.flash')

        <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
            <div>
                <div class="text-muted small text-uppercase fw-bold">Registrasi Akun SPMB</div>
                <h2 class="fw-bold mb-1">Status Akun Calon Murid</h2>
                <div class="text-muted">NISN {{ $pengguna->id_pengguna }}</div>
            </div>
            <form method="post" action="{{ route('akun.logout') }}">
                @csrf
                <button class="btn btn-outline-secondary">Keluar</button>
            </form>
        </div>

        <div class="alert alert-{{ $statusMeta[0] }} p-4">
            <h4 class="fw-bold">{{ $statusMeta[1] }}</h4>
            <div>{{ $statusMeta[2] }}</div>
            @if($registrasi?->catatan_verifikasi)
                <hr>
                <div class="fw-bold mb-1">Catatan Dinas Pendidikan:</div>
                <div>{{ $registrasi->catatan_verifikasi }}</div>
            @endif
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header fw-bold">Identitas dari Whitelist</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><div class="text-muted small">Nama</div><strong>{{ $calonSiswa?->nama }}</strong></div>
                    <div class="col-md-6"><div class="text-muted small">Tempat, Tanggal Lahir</div><strong>{{ $calonSiswa?->tempat_lahir }}, {{ $calonSiswa?->tanggal_lahir?->translatedFormat('d F Y') }}</strong></div>
                    <div class="col-md-6"><div class="text-muted small">Asal Sekolah</div><strong>{{ $calonSiswa?->asal_sekolah }}</strong></div>
                    <div class="col-md-6"><div class="text-muted small">Nomor WhatsApp</div><strong>{{ $pengguna->telpon }}</strong></div>
                </div>
            </div>
        </div>

        @if($status === 'terverifikasi')
            <a href="{{ route('login') }}" class="btn btn-success btn-lg">Login dan Lanjutkan Pendaftaran</a>
        @elseif($status === 'perlu_perbaikan')
            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="fw-bold">Perbaiki Data Registrasi</div>
                    <div class="small text-muted">KK tidak wajib diunggah ulang jika dokumen sebelumnya sudah benar.</div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('akun.perbaikan') }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        @method('put')
                        <div class="col-md-6">
                            <label class="form-label">Distrik/Kecamatan</label>
                            <select name="kecamatan_id" class="form-select" data-kecamatan required>
                                <option value="">Pilih distrik</option>
                                @foreach($kecamatanOptions as $kecamatan)
                                    <option value="{{ $kecamatan->id }}" @selected((string) old('kecamatan_id', $registrasi->kecamatan_id) === (string) $kecamatan->id)>{{ $kecamatan->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kelurahan/Kampung</label>
                            <select name="kelurahan_id" class="form-select" data-kelurahan required>
                                <option value="">Pilih kampung</option>
                                @foreach($kelurahanOptions as $kelurahan)
                                    <option value="{{ $kelurahan->id }}" data-kecamatan="{{ $kelurahan->kecamatan_id }}" @selected((string) old('kelurahan_id', $registrasi->kelurahan_id) === (string) $kelurahan->id)>{{ $kelurahan->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Detail Alamat</label>
                            <textarea name="detail_alamat" class="form-control" rows="3" required>{{ old('detail_alamat', $registrasi->detail_alamat) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nomor WhatsApp</label>
                            <div class="input-group"><span class="input-group-text">+62</span><input name="no_wa" class="form-control" value="{{ old('no_wa', preg_replace('/^62/', '', $pengguna->telpon)) }}" required></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ganti Kartu Keluarga</label>
                            <input type="file" name="kartu_keluarga" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                        </div>
                        <div class="col-12"><button class="btn btn-primary">Kirim Ulang untuk Verifikasi</button></div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script>
        const kecamatan = document.querySelector('[data-kecamatan]');
        const kelurahan = document.querySelector('[data-kelurahan]');
        const options = kelurahan ? Array.from(kelurahan.querySelectorAll('option[data-kecamatan]')) : [];
        function filterKelurahan() {
            options.forEach(option => {
                option.hidden = option.dataset.kecamatan !== kecamatan.value;
                option.disabled = option.hidden;
            });
            if (kelurahan?.selectedOptions[0]?.disabled) kelurahan.value = '';
        }
        kecamatan?.addEventListener('change', filterKelurahan);
        filterKelurahan();
    </script>
</x-layouts.app>
