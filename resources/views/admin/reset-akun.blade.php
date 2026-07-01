<x-layouts.app :pengguna="$pengguna" title="Reset Akun">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Reset Akun Calon Murid</h3>
            <div class="text-muted">Cari data calon murid berdasarkan NISN dan reset password ke default.</div>
        </div>
    </div>

    @include('partials.flash')

    <div class="row">
        <div class="col-lg-6 col-md-8">
            <!-- Search card -->
            <div class="card shadow-sm border border-light-subtle mb-4" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('admin.reset-akun') }}">
                        <div class="mb-3">
                            <label for="nisn" class="form-label fw-bold">Masukkan NISN Calon Murid</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror" 
                                       id="nisn" name="nisn" value="{{ $nisn }}" 
                                       placeholder="Masukkan NISN" required>
                                <button class="btn btn-primary px-4" type="submit">Cek Data</button>
                            </div>
                            <div class="form-text">Masukkan nomor NISN yang terdaftar pada sistem.</div>
                            @error('nisn')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>

            @if($searched)
                @if($student)
                    <!-- Student details & Reset Button -->
                    <div class="card shadow-sm border border-light-subtle" style="border-radius: 0.75rem;">
                        <div class="card-header bg-white border-bottom pt-4 px-4 pb-2">
                            <h5 class="fw-bold mb-0">Konfirmasi Data Calon Murid</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <th class="bg-light text-muted w-35" scope="row">NISN</th>
                                            <td><strong>{{ $student->id_pengguna }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted" scope="row">Nama Lengkap</th>
                                            <td>{{ $student->calonSiswa?->nama ?? $student->nama_pengguna }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted" scope="row">Asal Sekolah</th>
                                            <td>{{ $student->calonSiswa?->asal_sekolah ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted" scope="row">No. WhatsApp</th>
                                            <td>+{{ $student->telpon }}</td>
                                        </tr>
                                        <tr>
                                            <th class="bg-light text-muted" scope="row">Status Akun</th>
                                            <td>
                                                @if($student->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                                
                                                @if($student->is_verified)
                                                    <span class="badge bg-info">Terverifikasi</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Belum Terverifikasi</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-warning border-0 bg-warning-subtle text-warning-emphasis mb-4">
                                <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Peringatan Reset Sandi</h6>
                                Password akun calon murid di atas akan direset menjadi password default: <strong class="fs-6 text-dark font-monospace bg-white px-2 py-0.5 rounded border">CalonMurid123</strong>. Pastikan Anda telah mengonfirmasi bahwa data siswa di atas sudah benar.
                            </div>

                            <form method="POST" action="{{ route('admin.reset-akun.proses') }}">
                                @csrf
                                <input type="hidden" name="id_pengguna" value="{{ $student->id_pengguna }}">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger py-2.5 fw-bold" 
                                            data-confirm="Apakah Anda yakin ingin mereset password akun {{ $student->nama_pengguna }} (NISN: {{ $student->id_pengguna }}) ke password default 'CalonMurid123'?">
                                        <i class="bi bi-shield-lock-fill"></i> Reset Password Sekarang
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Not found state -->
                    <div class="alert alert-danger border-0 bg-danger-subtle text-danger-emphasis p-4" role="alert" style="border-radius: 0.75rem;">
                        <h5 class="fw-bold mb-2"><i class="bi bi-x-circle-fill"></i> Akun Tidak Ditemukan</h5>
                        Akun calon murid dengan NISN <strong>{{ $nisn }}</strong> tidak terdaftar dalam sistem atau bukan merupakan akun Calon Murid. Silakan periksa kembali nomor NISN yang Anda masukkan.
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
