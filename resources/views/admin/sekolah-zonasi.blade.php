<x-layouts.app :pengguna="$pengguna" title="Sekolah dan Zonasi">
    <style>
        .school-grid { display: grid; grid-template-columns: minmax(300px, .75fr) minmax(440px, 1.25fr); gap: 1rem; align-items: start; }
        .school-card { border-color: #cfe4dc; border-radius: .9rem; box-shadow: 0 12px 30px rgba(6,63,53,.07); }
        .school-item { border: 1px solid #d8e8e2; border-radius: .8rem; background: #fff; padding: 1rem; }
        .school-item + .school-item { margin-top: .8rem; }
        .select2-container--bootstrap-5 .select2-selection--multiple { min-height: 42px; }
        @media (max-width: 991.98px) { .school-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data Sekolah dan Zonasi</h3>
            <div class="text-muted">Kelola SMP tujuan dan cakupan kelurahan untuk Jalur Domisili.</div>
        </div>
    </div>

    @include('partials.flash')

    <div class="school-grid">
        <div class="d-grid gap-3">
            <section class="card school-card">
                <div class="card-header"><h5 class="fw-bold mb-0">Tambah Sekolah</h5></div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.sekolah.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-5"><label class="form-label">NPSN</label><input name="npsn" class="form-control"></div>
                        <div class="col-md-7"><label class="form-label">Nama Sekolah</label><input name="nama" class="form-control" required></div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required><option value="negeri">Negeri</option><option value="swasta">Swasta</option></select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Distrik</label>
                            <select name="kecamatan_id" class="form-select" data-school-district required>
                                <option value="">Pilih distrik</option>
                                @foreach($kecamatans as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Kelurahan/Kampung Sekolah</label>
                            <select name="kelurahan_id" class="form-select" data-school-village required><option value="">Pilih kampung</option></select>
                        </div>
                        <div class="col-12"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" rows="2"></textarea></div>
                        <div class="col-md-6"><label class="form-label">Telepon</label><input name="telepon" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-12"><hr class="my-1"><div class="fw-bold">Akun Login Sekolah</div><div class="small text-muted">Akun ini otomatis diberi role Admin Sekolah dan dihubungkan ke sekolah baru.</div></div>
                        <div class="col-md-6"><label class="form-label">Username</label><input name="username" value="{{ old('username') }}" class="form-control" autocomplete="username" required></div>
                        <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" autocomplete="new-password" minlength="12" required><div class="form-text">Minimal 12 karakter.</div></div>
                        <div class="col-12 d-grid"><button class="btn btn-primary">Simpan Sekolah</button></div>
                    </form>
                </div>
            </section>

            <section class="card school-card">
                <div class="card-header"><h5 class="fw-bold mb-0">Import CSV</h5></div>
                <div class="card-body">
                    <p class="small text-muted">Kolom: <code>npsn,nama_sekolah,status,kecamatan,kelurahan_sekolah,alamat,telepon,email,zonasi_kelurahan</code>. Pisahkan beberapa kampung zonasi dengan tanda titik koma.</p>
                    <a href="{{ asset('templates/import-sekolah-zonasi.csv') }}" class="btn btn-sm btn-outline-secondary mb-3" download>Unduh Template CSV</a>
                    <form method="post" action="{{ route('admin.sekolah-zonasi.import') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file_import" class="form-control mb-3" accept=".csv,.txt" required>
                        <button class="btn btn-outline-primary w-100">Import Sekolah dan Zonasi</button>
                    </form>
                </div>
            </section>
        </div>

        <section class="card school-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="fw-bold mb-0">Daftar Sekolah</h5><div class="small text-muted">{{ $sekolahs->count() }} sekolah tersimpan</div></div>
            </div>
            <div class="card-body">
                @forelse($sekolahs as $sekolah)
                    <article class="school-item">
                        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                            <div>
                                <div class="fw-bold">{{ $sekolah->nama }}</div>
                                <div class="small text-muted">NPSN {{ $sekolah->npsn ?: '-' }} · {{ ucfirst($sekolah->status) }} · {{ $sekolah->admin_count }} admin sekolah</div>
                            </div>
                            <span class="badge {{ $sekolah->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $sekolah->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <form method="post" action="{{ route('admin.sekolah.zonasi', $sekolah) }}">
                            @csrf
                            <label class="form-label fw-bold">Kelurahan/Kampung dalam Zonasi</label>
                            <select name="kelurahan_ids[]" class="form-select zone-select mb-3" multiple data-placeholder="Cari nama kampung atau distrik">
                                @foreach($kelurahans as $kelurahan)
                                    <option value="{{ $kelurahan->id }}" @selected(in_array($kelurahan->id, $zonasiBySchool[$sekolah->id] ?? []))>
                                        {{ $kelurahan->nama }} - {{ $kelurahan->nama_distrik }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary btn-sm">Simpan Zonasi</button>
                                <button type="submit"
                                        form="hapus-sekolah-{{ $sekolah->id }}"
                                        class="btn btn-outline-danger btn-sm"
                                        data-confirm="Hapus sekolah {{ $sekolah->nama }}? Relasi zonasi dan akun sekolah yang tidak lagi digunakan akan ikut dibersihkan.">
                                    Hapus Sekolah
                                </button>
                            </div>
                        </form>
                        <form id="hapus-sekolah-{{ $sekolah->id }}" method="post" action="{{ route('admin.sekolah.destroy', $sekolah) }}" class="d-none">
                            @csrf
                            @method('delete')
                        </form>
                    </article>
                @empty
                    <div class="text-center text-muted p-5">Belum ada data sekolah.</div>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const villages = @json($kelurahans);
            const district = document.querySelector('[data-school-district]');
            const village = document.querySelector('[data-school-village]');
            function fillVillages() {
                village.innerHTML = '<option value="">Pilih kampung</option>';
                villages.filter(item => String(item.kecamatan_id) === district.value).forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.nama;
                    village.appendChild(option);
                });
            }
            district?.addEventListener('change', fillVillages);

            $('.zone-select').each(function () {
                $(this).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $(this).data('placeholder'),
                    closeOnSelect: false
                });
            });
        });
    </script>
</x-layouts.app>
