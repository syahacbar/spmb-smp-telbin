<x-layouts.app :pengguna="$pengguna" title="Data User">
    <style>
        .data-user-table thead .filter-row th {
            background: #f8fafc;
            padding-top: .55rem;
            padding-bottom: .55rem;
        }
        .data-user-table thead .filter-row th::after,
        .data-user-table thead .filter-row th::before {
            display: none !important;
        }
        .data-user-table .dt-column-title {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }
        .dt-search label,
        .dt-length label {
            color: #667085;
            font-weight: 700;
        }
        .dt-search input,
        .dt-length select,
        .column-filter {
            border-color: #d0d5dd;
            border-radius: .45rem;
        }
    </style>

    <div class="page-title">
        <div>
            <h3 class="fw-bold">Data User</h3>
            <div class="text-muted">Kelola akun siswa dan verifikasi akses login.</div>
        </div>
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#formTambahUser" aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-controls="formTambahUser">
            <span aria-hidden="true">+</span>
            <span>Tambah User</span>
        </button>
    </div>

    <div class="collapse {{ $errors->any() ? 'show' : '' }}" id="formTambahUser">
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <div class="fw-bold">Tambah User Siswa</div>
                <div class="small text-muted">Password awal otomatis diset ke <strong>siswa123</strong>, lalu admin diarahkan ke form biodata pendaftaran.</div>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.pengguna.store') }}" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label">NISN</label>
                        <input type="text" name="nisn" value="{{ old('nisn') }}" class="form-control form-control-lg" inputmode="numeric" maxlength="10" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Nomor WhatsApp Aktif</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">+62</span>
                            <input type="text" name="no_wa" value="{{ old('no_wa') }}" class="form-control" inputmode="numeric" placeholder="81234567890" required>
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button class="btn btn-primary btn-lg">Buat Akun</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="dataUserTable" class="table table-hover mb-0 data-user-table w-100">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Asal Sekolah</th>
                        <th>No Telpon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    <tr class="filter-row">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="3" aria-label="Filter asal sekolah">
                                <option value="">Semua sekolah</option>
                            </select>
                        </th>
                        <th></th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="5" aria-label="Filter status">
                                <option value="">Semua status</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Menunggu">Menunggu</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        @php
                            if ($user->is_active === false) {
                                $statusLabel = 'Nonaktif';
                                $statusOrder = 3;
                            } elseif ($user->is_verified) {
                                $statusLabel = 'Aktif';
                                $statusOrder = 1;
                            } else {
                                $statusLabel = 'Menunggu';
                                $statusOrder = 2;
                            }
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->id_pengguna }}</td>
                            <td>{{ $user->calonSiswa->nama ?? ($user->nama_pengguna ?: '-') }}</td>
                            <td>{{ $user->calonSiswa->asal_sekolah ?? '-' }}</td>
                            <td>
                                @php($phone = preg_replace('/\D+/', '', $user->telpon ?? ''))
                                @if($phone)
                                    <a href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener" class="fw-semibold text-decoration-none" aria-label="Hubungi WhatsApp {{ $user->id_pengguna }}" title="Hubungi via WhatsApp">
                                        {{ $user->telpon }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td data-order="{{ $statusOrder }}" data-search="{{ $statusLabel }}">
                                @if($user->is_active === false)
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                @elseif($user->is_verified)
                                    <span class="badge text-bg-success">Aktif</span>
                                    @if($user->verified_at)
                                        <div class="small text-muted">{{ $user->verified_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                @else
                                    <span class="badge text-bg-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <form method="post" action="{{ route('admin.pengguna.verifikasi', $user) }}" class="mb-0">
                                        @csrf
                                        <button class="btn btn-sm {{ $user->is_verified ? 'btn-outline-success' : 'btn-success' }}" data-confirm="Verifikasi akun ini?" aria-label="Verifikasi user {{ $user->id_pengguna }}" title="{{ $user->is_verified ? 'Sudah terverifikasi' : 'Verifikasi akun' }}" @disabled($user->is_verified)>
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="m5 12 4 4L19 6" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                            <span class="visually-hidden">Verifikasi</span>
                                        </button>
                                    </form>

                                    @if($user->is_verified && $user->is_active && $phone)
                                        <a href="{{ route('admin.pengguna.notifikasi-whatsapp', $user) }}" target="_blank" rel="noopener" class="btn btn-sm btn-success" aria-label="Kirim pemberitahuan WhatsApp kepada user {{ $user->id_pengguna }}" title="Kirim pemberitahuan akun aktif via WhatsApp">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M20.5 11.6a8.5 8.5 0 0 1-12.6 7.5L3 20.5l1.4-4.7A8.5 8.5 0 1 1 20.5 11.6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                                                <path d="M8.2 7.7c.2-.4.4-.4.7-.4h.5c.2 0 .4.1.5.4l.8 1.9c.1.2.1.4 0 .6l-.7 1c-.2.2-.1.4 0 .6.8 1.4 1.9 2.4 3.3 3.1.2.1.4.1.6-.1l.9-1.1c.2-.2.4-.3.7-.2l1.9.9c.3.1.4.3.4.5 0 .5-.2 1.5-.8 2-.6.5-1.4.8-2.4.6-1.3-.2-3-.9-4.8-2.5-1.5-1.3-2.5-2.9-2.9-4.2-.4-1.1-.1-2.3.4-2.8l.9-.3Z" fill="currentColor"></path>
                                            </svg>
                                            <span class="visually-hidden">Kirim WhatsApp</span>
                                        </a>
                                    @endif

                                    <form method="post" action="{{ route('admin.pengguna.toggle-active', $user) }}" class="mb-0">
                                        @csrf
                                        <button class="btn btn-sm {{ $user->is_active === false ? 'btn-outline-success' : 'btn-outline-warning' }}" data-confirm="{{ $user->is_active === false ? 'Aktifkan user ini?' : 'Nonaktifkan user ini?' }}" aria-label="{{ $user->is_active === false ? 'Aktifkan' : 'Nonaktifkan' }} user {{ $user->id_pengguna }}" title="{{ $user->is_active === false ? 'Aktifkan user' : 'Nonaktifkan user' }}">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M12 2v10" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"></path>
                                                <path d="M18.4 6.7a8 8 0 1 1-12.8 0" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"></path>
                                            </svg>
                                            <span class="visually-hidden">{{ $user->is_active === false ? 'Aktifkan' : 'Nonaktifkan' }}</span>
                                        </button>
                                    </form>

                                    <form method="post" action="{{ route('admin.pengguna.reset-password', $user) }}" class="mb-0">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-info" data-confirm="Reset password user ini ke siswa123?" aria-label="Reset password user {{ $user->id_pengguna }}" title="Reset password">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <circle cx="7.5" cy="14.5" r="3.5" stroke="currentColor" stroke-width="2"></circle>
                                                <path d="M10.2 12 21 1.2M15 6.2l2.8 2.8M18.2 3l2.8 2.8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                            <span class="visually-hidden">Reset password</span>
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.pengguna.formulir.create', $user) }}" class="btn btn-sm btn-outline-primary" aria-label="Isi biodata pendaftaran user {{ $user->id_pengguna }}" title="Isi biodata">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                                            <path d="M14 3v6h6" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                                            <path d="M8 14h8M8 18h5" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                        </svg>
                                        <span class="visually-hidden">Isi biodata</span>
                                    </a>

                                    <form method="post" action="{{ route('admin.pengguna.destroy', $user) }}" class="mb-0">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-outline-danger" data-confirm="Hapus user ini? Data formulir yang sudah ada tidak ikut dihapus." aria-label="Hapus user {{ $user->id_pengguna }}" title="Hapus user">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 7h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                                <path d="M6 7l1 14h10l1-14M9 7V4h6v3" stroke="currentColor" stroke-width="2" stroke-linejoin="round"></path>
                                            </svg>
                                            <span class="visually-hidden">Hapus user</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableElement = document.getElementById('dataUserTable');

            if (! tableElement || ! window.DataTable) {
                return;
            }

            const table = new DataTable(tableElement, {
                orderCellsTop: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[1, 'asc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [0, 6] },
                    { type: 'num', targets: 5 },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ user',
                    infoEmpty: 'Tidak ada user yang ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ total user)',
                    zeroRecords: 'Data user tidak ditemukan',
                    emptyTable: 'Belum ada user.',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });

            const schoolFilter = tableElement.querySelector('.column-filter[data-column="3"]');

            if (schoolFilter) {
                table
                    .column(3)
                    .data()
                    .unique()
                    .sort()
                    .each(function (value) {
                        const school = String(value).trim();

                        if (! school || school === '-') {
                            return;
                        }

                        const option = document.createElement('option');
                        option.value = school;
                        option.textContent = school;
                        schoolFilter.appendChild(option);
                    });
            }

            tableElement.querySelectorAll('.column-filter').forEach(function (select) {
                select.addEventListener('change', function () {
                    const columnIndex = Number(select.dataset.column);
                    const value = select.value;
                    const escapedValue = value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

                    table.column(columnIndex).search(value ? '^' + escapedValue + '$' : '', true, false).draw();
                });

                select.addEventListener('click', function (event) {
                    event.stopPropagation();
                });
            });

            table.on('order.dt search.dt draw.dt', function () {
                let index = 1;

                table
                    .cells(null, 0, { search: 'applied', order: 'applied' })
                    .every(function () {
                        this.data(index++);
                    });
            }).draw();
        });
    </script>
</x-layouts.app>
