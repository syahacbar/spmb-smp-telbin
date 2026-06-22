<x-layouts.app :pengguna="$pengguna" title="Profil Sekolah – {{ $sekolah->nama }}">
    <style>
        .profil-hero {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(135deg, #063f35, #0b5d4b 55%, #0788a8);
            color: #fff;
            padding: 1.75rem;
            box-shadow: 0 18px 42px rgba(6, 63, 53, .22);
        }
        .profil-hero::after {
            content: "";
            position: absolute;
            right: -5rem;
            bottom: -7rem;
            width: 18rem;
            height: 18rem;
            border: 2.5rem solid rgba(242, 184, 75, .12);
            border-radius: 50%;
        }
        .profil-hero > * { position: relative; z-index: 1; }
        .profil-kicker {
            color: #f2b84b;
            font-size: .76rem;
            font-weight: 900;
            letter-spacing: .09em;
            text-transform: uppercase;
        }
        .profil-hero p { color: rgba(255,255,255,.78); }
        .school-foto-wrap {
            width: 130px;
            height: 130px;
            flex: 0 0 130px;
            border-radius: 1rem;
            overflow: hidden;
            border: 3px solid rgba(255,255,255,.3);
            background: rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .school-foto-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .school-foto-placeholder {
            font-size: 3rem;
            color: rgba(255,255,255,.5);
        }
        .profil-form-card {
            border: 1px solid #d8e8e2;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 12px 30px rgba(16, 55, 47, .06);
        }
        .profil-form-card .card-header {
            border-radius: 1rem 1rem 0 0;
            background: linear-gradient(90deg, #eef7f3, #fff);
            border-bottom: 1px solid #d8e8e2;
            padding: 1.25rem 1.5rem;
        }
        .profil-form-card .card-body {
            padding: 2rem 1.75rem;
        }
        @media (max-width: 575.98px) {
            .profil-form-card .card-header {
                padding: 1rem;
            }
            .profil-form-card .card-body {
                padding: 1.25rem 1rem;
            }
        }
        .section-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: .5rem;
            background: #0b5d4b;
            color: #fff;
            font-size: .8rem;
            font-weight: 900;
        }
        .foto-upload-zone {
            border: 2px dashed #b0d4c8;
            border-radius: .75rem;
            background: #f2faf7;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .foto-upload-zone:hover {
            border-color: #0b5d4b;
            background: #e4f3ed;
        }
        .foto-preview-thumb {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: .65rem;
            border: 2px solid #b0d4c8;
            box-shadow: 0 4px 12px rgba(11, 93, 75, .1);
        }
        .info-item {
            display: flex;
            gap: .6rem;
            padding: .6rem 0;
            border-bottom: 1px solid #edf4f1;
        }
        .info-item:last-child { border-bottom: 0; }
        .info-label {
            min-width: 130px;
            color: #667085;
            font-size: .84rem;
            font-weight: 700;
        }
        .info-value { color: #12372f; font-weight: 600; }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Profil Sekolah</h3>
            <div class="text-muted">Identitas dan informasi umum sekolah</div>
        </div>
    </div>

    {{-- Hero --}}
    <div class="profil-hero mb-4">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <div class="school-foto-wrap">
                @if($sekolah->foto)
                    <img src="{{ asset('storage/'.$sekolah->foto) }}" alt="Foto {{ $sekolah->nama }}" id="heroFotoImg">
                @else
                    <div class="school-foto-placeholder" id="heroFotoPlaceholder">🏫</div>
                @endif
            </div>
            <div>
                <div class="profil-kicker">{{ $sekolah->status === 'negeri' ? 'Sekolah Negeri' : 'Sekolah Swasta' }}</div>
                <h2 class="fw-bold mt-1 mb-1">{{ $sekolah->nama }}</h2>
                <p class="mb-0">
                    @if($sekolah->kepala_sekolah) Kepala Sekolah: <strong>{{ $sekolah->kepala_sekolah }}</strong> &nbsp;·&nbsp; @endif
                    {{ $kecamatan }} {{ $kelurahan ? ', '.$kelurahan : '' }}
                    @if($sekolah->npsn) &nbsp;·&nbsp; NPSN <strong>{{ $sekolah->npsn }}</strong> @endif
                </p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Info Tetap --}}
        <div class="col-lg-4">
            <div class="profil-form-card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-badge">i</span>
                    <div>
                        <div class="fw-bold">Identitas Sekolah</div>
                        <div class="small text-muted">Data dikelola oleh Admin Dinas</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <span class="info-label">NPSN</span>
                        <span class="info-value">{{ $sekolah->npsn ?: '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nama Sekolah</span>
                        <span class="info-value">{{ $sekolah->nama }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            <span class="badge {{ $sekolah->status === 'negeri' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                {{ ucfirst($sekolah->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kecamatan</span>
                        <span class="info-value">{{ $kecamatan ?: '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Kelurahan</span>
                        <span class="info-value">{{ $kelurahan ?: '-' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status Aktif</span>
                        <span class="info-value">
                            <span class="badge {{ $sekolah->is_active ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ $sekolah->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Edit Form --}}
        <div class="col-lg-8">
            <div class="profil-form-card">
                <div class="card-header d-flex align-items-center gap-2">
                    <span class="section-badge">✏</span>
                    <div>
                        <div class="fw-bold">Edit Profil Sekolah</div>
                        <div class="small text-muted">Foto, kontak, dan deskripsi sekolah</div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('sekolah.admin.profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Foto Upload --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Foto Sekolah</label>
                            <div class="d-flex align-items-start gap-3 flex-wrap">
                                @if($sekolah->foto)
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <img src="{{ asset('storage/'.$sekolah->foto) }}" class="foto-preview-thumb" id="fotoPreviewThumb" alt="Foto Sekolah">
                                        <form action="{{ route('sekolah.admin.profil.foto.destroy') }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" data-confirm="Hapus foto sekolah?">Hapus Foto</button>
                                        </form>
                                    </div>
                                @else
                                    <div class="d-flex flex-column align-items-center gap-2">
                                        <div class="foto-preview-thumb d-flex align-items-center justify-content-center bg-light text-muted" id="fotoPreviewThumb" style="font-size:2rem;">🏫</div>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="foto-upload-zone" id="fotoUploadZone" onclick="document.getElementById('fotoInput').click()">
                                        <div style="font-size:1.5rem;">📷</div>
                                        <div class="fw-bold mt-1 text-success">Pilih atau ganti foto sekolah</div>
                                        <div class="small text-muted mt-1">JPG, PNG, WebP · Maks. 2 MB</div>
                                    </div>
                                    <input type="file" id="fotoInput" name="foto" accept="image/*" class="d-none">
                                    @error('foto')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="kepala_sekolah" class="form-label fw-bold">Kepala Sekolah</label>
                                <input type="text" id="kepala_sekolah" name="kepala_sekolah" class="form-control @error('kepala_sekolah') is-invalid @enderror"
                                    value="{{ old('kepala_sekolah', $sekolah->kepala_sekolah) }}" placeholder="Nama kepala sekolah" maxlength="150">
                                @error('kepala_sekolah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="telepon" class="form-label fw-bold">Nomor Telepon</label>
                                <input type="text" id="telepon" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
                                    value="{{ old('telepon', $sekolah->telepon) }}" placeholder="Nomor telepon sekolah" maxlength="20">
                                @error('telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label for="email" class="form-label fw-bold">Email Sekolah</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $sekolah->email) }}" placeholder="email@sekolah.sch.id" maxlength="100">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label for="alamat" class="form-label fw-bold">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror"
                                    placeholder="Alamat lengkap sekolah" maxlength="1000">{{ old('alamat', $sekolah->alamat) }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-12">
                                <label for="deskripsi" class="form-label fw-bold">Deskripsi / Visi Sekolah</label>
                                <textarea id="deskripsi" name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror"
                                    placeholder="Tuliskan visi, misi, atau deskripsi singkat tentang sekolah ini..." maxlength="2000">{{ old('deskripsi', $sekolah->deskripsi) }}</textarea>
                                @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('fotoInput')?.addEventListener('change', function () {
            const file = this.files[0];
            if (! file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const thumb = document.getElementById('fotoPreviewThumb');
                if (thumb.tagName === 'IMG') {
                    thumb.src = e.target.result;
                } else {
                    // replace placeholder div with img
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = thumb.className;
                    img.id = thumb.id;
                    img.alt = 'Preview Foto';
                    thumb.replaceWith(img);
                }
                const heroImg = document.getElementById('heroFotoImg');
                const heroPlaceholder = document.getElementById('heroFotoPlaceholder');
                if (heroImg) {
                    heroImg.src = e.target.result;
                } else if (heroPlaceholder) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.id = 'heroFotoImg';
                    img.alt = 'Foto Sekolah';
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
                    heroPlaceholder.replaceWith(img);
                }
            };
            reader.readAsDataURL(file);
        });
    </script>
</x-layouts.app>
