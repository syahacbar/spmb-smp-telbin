<x-layouts.app :pengguna="$pengguna" title="Data Registrasi">
    <style>
        .data-registrasi-table thead .filter-row th {
            background: #f8fafc;
            padding-top: .55rem;
            padding-bottom: .55rem;
        }
        .data-registrasi-table thead .filter-row th::after,
        .data-registrasi-table thead .filter-row th::before {
            display: none !important;
        }
        .data-registrasi-table .dt-column-title {
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
            <h3 class="fw-bold">Data Registrasi</h3>
            <div class="text-muted">Formulir yang sudah dikirim final oleh siswa.</div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table id="dataRegistrasiTable" class="table table-hover align-middle mb-0 data-registrasi-table w-100">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama</th>
                        <th>Asal Sekolah</th>
                        <th>Minat A</th>
                        <th>Minat B</th>
                        <th>Foto</th>
                        <th>Tanggal Kirim</th>
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
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="4" aria-label="Filter minat A">
                                <option value="">Semua minat A</option>
                            </select>
                        </th>
                        <th>
                            <select class="form-select form-select-sm column-filter" data-column="5" aria-label="Filter minat B">
                                <option value="">Semua minat B</option>
                            </select>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($formulirs as $formulir)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $formulir->nisn }}</td>
                            <td>{{ $formulir->nama }}</td>
                            <td>{{ $formulir->asal_sekolah }}</td>
                            <td>{{ $formulir->program_keahlian_1 }}</td>
                            <td>{{ $formulir->program_keahlian_2 }}</td>
                            <td>
                                <a href="{{ $formulir->berkasUrl('foto_selfie') }}" target="_blank">
                                    <img src="{{ $formulir->berkasUrl('foto_selfie') }}" class="doc-thumb" alt="Foto {{ $formulir->nama }}">
                                </a>
                            </td>
                            @php($tanggalKirim = $formulir->submitted_at ?: $formulir->created_at)
                            <td data-order="{{ $tanggalKirim?->timestamp ?? 0 }}">{{ $tanggalKirim?->format('d/m/Y H:i') }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('formulir.edit', $formulir) }}" class="btn btn-success btn-sm">Edit</a>
                                <a href="{{ route('formulir.cetak', $formulir) }}" class="btn btn-outline-success btn-sm" target="_blank">Cetak</a>
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
            const tableElement = document.getElementById('dataRegistrasiTable');

            if (! tableElement || ! window.DataTable) {
                return;
            }

            const table = new DataTable(tableElement, {
                orderCellsTop: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [[7, 'desc']],
                columnDefs: [
                    { orderable: false, searchable: false, targets: [0, 6, 8] },
                ],
                language: {
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ registrasi',
                    infoEmpty: 'Tidak ada registrasi yang ditampilkan',
                    infoFiltered: '(difilter dari _MAX_ total registrasi)',
                    zeroRecords: 'Data registrasi tidak ditemukan',
                    emptyTable: 'Belum ada data registrasi.',
                    paginate: {
                        first: 'Awal',
                        last: 'Akhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya',
                    },
                },
            });

            [3, 4, 5].forEach(function (columnIndex) {
                const filter = tableElement.querySelector('.column-filter[data-column="' + columnIndex + '"]');

                if (! filter) {
                    return;
                }

                table
                    .column(columnIndex)
                    .data()
                    .unique()
                    .sort()
                    .each(function (value) {
                        const label = String(value).trim();

                        if (! label || label === '-') {
                            return;
                        }

                        const option = document.createElement('option');
                        option.value = label;
                        option.textContent = label;
                        filter.appendChild(option);
                    });
            });

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
