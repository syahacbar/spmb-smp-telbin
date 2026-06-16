<x-layouts.app :pengguna="$pengguna" title="Data User">
    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data User</h3>
            <div class="text-muted">Kelola akun siswa dan verifikasi akses login.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Asal Sekolah</th>
                        <th>Email</th>
                        <th>No Telpon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->id_pengguna }}</td>
                            <td>{{ $user->calonSiswa->nama ?? ($user->nama_pengguna ?: '-') }}</td>
                            <td>{{ $user->calonSiswa->asal_sekolah ?? '-' }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->telpon }}</td>
                            <td>
                                @if($user->is_verified)
                                    <span class="badge text-bg-success">Terverifikasi</span>
                                    @if($user->verified_at)
                                        <div class="small text-muted">{{ $user->verified_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                @else
                                    <span class="badge text-bg-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if(! $user->is_verified)
                                    <form method="post" action="{{ route('admin.pengguna.verifikasi', $user) }}" class="mb-0">
                                        @csrf
                                        <button class="btn btn-sm btn-primary" data-confirm="Verifikasi akun ini?">Verifikasi</button>
                                    </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada user.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
