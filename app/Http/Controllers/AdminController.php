<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\KontakPanitia;
use App\Models\PengaturanSpmb;
use App\Models\Pengguna;
use App\Models\RegistrasiAkun;
use App\Models\Sekolah;
use App\Services\CalonSiswaImportReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AdminController extends Controller
{
    public function pendaftar(Request $request): View
    {
        return view('admin.pendaftar', [
            'pengguna' => $request->attributes->get('pengguna'),
            'formulirs' => Formulir::with(['jalur', 'sekolah'])->whereIn('status', ['submitted', 'diterima', 'ditolak'])->latest('id')->get(),
        ]);
    }

    public function pengguna(Request $request): View
    {
        $status = (string) $request->query('status', '');
        $allowedStatuses = ['menunggu_verifikasi', 'terverifikasi', 'perlu_perbaikan', 'ditolak'];

        $users = Pengguna::with(['calonSiswa', 'registrasiAkun'])
            ->whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))
            ->when(
                in_array($status, $allowedStatuses, true),
                fn ($query) => $query->whereHas('registrasiAkun', fn ($registrasi) => $registrasi->where('status', $status)),
            )
            ->orderByRaw("case when exists (
                select 1 from tb_registrasi_akun
                where tb_registrasi_akun.nisn = tb_pengguna.id_pengguna
                and tb_registrasi_akun.status = 'menunggu_verifikasi'
            ) then 0 else 1 end")
            ->orderBy('id_pengguna')
            ->get();

        return view('admin.pengguna', [
            'pengguna' => $request->attributes->get('pengguna'),
            'users' => $users,
            'activeStatus' => $status,
            'statusCounts' => RegistrasiAkun::query()
                ->select('status')
                ->selectRaw('count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
            'kecamatanNames' => DB::table('ref_kecamatan')->pluck('nama', 'id'),
            'kelurahanNames' => DB::table('ref_kelurahan')->pluck('nama', 'id'),
        ]);
    }

    public function verifikasiAkun(Request $request, RegistrasiAkun $registrasi): View
    {
        $registrasi->load(['pengguna.calonSiswa', 'periode']);

        abort_unless($registrasi->pengguna?->isCalonMurid(), 404);

        return view('admin.verifikasi-akun', [
            'pengguna' => $request->attributes->get('pengguna'),
            'registrasi' => $registrasi,
            'calonSiswa' => $registrasi->pengguna->calonSiswa,
            'kecamatan' => DB::table('ref_kecamatan')->where('id', $registrasi->kecamatan_id)->value('nama'),
            'kelurahan' => DB::table('ref_kelurahan')->where('id', $registrasi->kelurahan_id)->value('nama'),
            'riwayat' => DB::table('tb_riwayat_verifikasi_akun')
                ->leftJoin('tb_pengguna as petugas', 'petugas.id_pengguna', '=', 'tb_riwayat_verifikasi_akun.diproses_oleh')
                ->where('registrasi_akun_id', $registrasi->id)
                ->orderByDesc('tb_riwayat_verifikasi_akun.id')
                ->get([
                    'tb_riwayat_verifikasi_akun.*',
                    'petugas.nama_pengguna as nama_petugas',
                ]),
        ]);
    }

    public function pengaturan(Request $request): View
    {
        return view('admin.pengaturan', [
            'pengguna' => $request->attributes->get('pengguna'),
            'settings' => PengaturanSpmb::allSettings(),
            'contacts' => KontakPanitia::query()->orderByDesc('is_primary')->orderBy('id')->get(),
            'whitelistStats' => CalonSiswa::query()
                ->select('tahun_lulus')
                ->selectRaw('count(*) as total')
                ->selectRaw('sum(case when is_active = 1 then 1 else 0 end) as active_total')
                ->groupBy('tahun_lulus')
                ->orderByDesc('tahun_lulus')
                ->get(),
            'whitelistYears' => CalonSiswa::query()
                ->select('tahun_lulus')
                ->distinct()
                ->orderByDesc('tahun_lulus')
                ->pluck('tahun_lulus'),
            'whitelist' => CalonSiswa::query()
                ->orderByDesc('tahun_lulus')
                ->orderByDesc('is_active')
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function sekolahZonasi(Request $request): View
    {
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');

        return view('admin.sekolah-zonasi', [
            'pengguna' => $request->attributes->get('pengguna'),
            'sekolahs' => Sekolah::query()
                ->withCount(['pengguna as admin_count'])
                ->orderBy('nama')
                ->get(),
            'kecamatans' => DB::table('ref_kecamatan')->orderBy('urutan')->orderBy('nama')->get(['id', 'nama']),
            'kelurahans' => DB::table('ref_kelurahan')
                ->join('ref_kecamatan', 'ref_kecamatan.id', '=', 'ref_kelurahan.kecamatan_id')
                ->orderBy('ref_kecamatan.urutan')
                ->orderBy('ref_kecamatan.nama')
                ->orderBy('ref_kelurahan.urutan')
                ->orderBy('ref_kelurahan.nama')
                ->get([
                    'ref_kelurahan.id',
                    'ref_kelurahan.kecamatan_id',
                    'ref_kelurahan.nama',
                    'ref_kecamatan.nama as nama_distrik',
                ]),
            'zonasiBySchool' => DB::table('tb_zonasi_sekolah')
                ->where('periode_id', $periodeId)
                ->get()
                ->groupBy('sekolah_id')
                ->map(fn ($items) => $items->pluck('kelurahan_id')->map(fn ($id) => (int) $id)->all()),
        ]);
    }

    public function storeSekolah(Request $request): RedirectResponse
    {
        $request->merge(['username' => trim((string) $request->input('username'))]);
        $data = $this->validatedSchool($request);
        $account = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('tb_pengguna', 'username')],
            'password' => ['required', 'string', 'min:12', 'max:255'],
        ]);

        DB::transaction(function () use ($data, $account): void {
            $data['is_active'] = true;
            $sekolah = Sekolah::create($data);
            $roleId = DB::table('roles')->where('kode', 'admin_sekolah')->value('id');
            abort_unless($roleId, 422, 'Role Admin Sekolah belum tersedia.');

            do {
                $penggunaId = substr('SCH'.strtoupper(Str::random(8)), 0, 11);
            } while (Pengguna::whereKey($penggunaId)->exists());

            $pengguna = Pengguna::create([
                'id_pengguna' => $penggunaId,
                'nama_pengguna' => 'Admin '.$sekolah->nama,
                'alamat' => $sekolah->alamat,
                'telpon' => $sekolah->telepon ?: '',
                'email' => null,
                'username' => $account['username'],
                'password' => Hash::make($account['password']),
                'level' => 'Administrator',
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]);

            $pengguna->roles()->attach($roleId);
            $pengguna->sekolah()->attach($sekolah->id);
        });

        return back()->with('success', 'Data sekolah dan akun login sekolah berhasil ditambahkan.');
    }

    public function updateSekolah(Request $request, Sekolah $sekolah): RedirectResponse
    {
        $data = $this->validatedSchool($request, $sekolah);
        $data['is_active'] = $request->boolean('is_active');
        $sekolah->update($data);

        return back()->with('success', 'Data sekolah berhasil diperbarui.');
    }

    public function destroySekolah(Sekolah $sekolah): RedirectResponse
    {
        DB::transaction(function () use ($sekolah): void {
            $adminSekolahRoleId = DB::table('roles')->where('kode', 'admin_sekolah')->value('id');
            abort_unless($adminSekolahRoleId, 422, 'Role Admin Sekolah belum tersedia.');
            $adminSekolah = $sekolah->pengguna()
                ->whereHas('roles', fn ($query) => $query->where('kode', 'admin_sekolah'))
                ->get();

            Formulir::where('sekolah_id', $sekolah->id)->update(['sekolah_id' => null]);

            foreach ($adminSekolah as $admin) {
                $jumlahSekolahLain = $admin->sekolah()
                    ->where('tb_sekolah.id', '!=', $sekolah->id)
                    ->count();

                if ($jumlahSekolahLain === 0) {
                    $admin->roles()->detach($adminSekolahRoleId);

                    if (! $admin->roles()->exists()) {
                        $admin->delete();
                    }
                }
            }

            $sekolah->delete();
        });

        return back()->with('success', "Sekolah {$sekolah->nama} berhasil dihapus.");
    }

    public function syncZonasiSekolah(Request $request, Sekolah $sekolah): RedirectResponse
    {
        $data = $request->validate([
            'kelurahan_ids' => ['nullable', 'array'],
            'kelurahan_ids.*' => ['integer', 'exists:ref_kelurahan,id'],
        ]);
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        abort_unless($periodeId, 422, 'Periode SPMB aktif belum tersedia.');

        DB::transaction(function () use ($periodeId, $sekolah, $data): void {
            DB::table('tb_zonasi_sekolah')
                ->where('periode_id', $periodeId)
                ->where('sekolah_id', $sekolah->id)
                ->delete();

            foreach (array_values(array_unique($data['kelurahan_ids'] ?? [])) as $priority => $kelurahanId) {
                DB::table('tb_zonasi_sekolah')->insert([
                    'periode_id' => $periodeId,
                    'sekolah_id' => $sekolah->id,
                    'kelurahan_id' => $kelurahanId,
                    'prioritas' => $priority + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return back()->with('success', "Zonasi {$sekolah->nama} berhasil diperbarui.");
    }

    public function importSekolahZonasi(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file_import' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'file_import.mimes' => 'Import sekolah dan zonasi sementara menggunakan format CSV.',
        ]);

        $handle = fopen($data['file_import']->getRealPath(), 'r');
        abort_unless($handle, 422, 'File import tidak dapat dibuka.');

        $header = array_map(
            fn ($value) => strtolower(trim(str_replace(' ', '_', (string) $value))),
            fgetcsv($handle) ?: [],
        );
        $required = ['npsn', 'nama_sekolah', 'status', 'kecamatan', 'kelurahan_sekolah', 'zonasi_kelurahan'];

        if (array_diff($required, $header)) {
            fclose($handle);

            return back()->with('warning', 'Kolom CSV wajib: '.implode(', ', $required).'.');
        }

        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($handle, $header, $periodeId, &$imported, &$skipped): void {
            while (($row = fgetcsv($handle)) !== false) {
                $row = array_slice(array_pad($row, count($header), ''), 0, count($header));
                $item = array_combine($header, $row);

                if (! $item || trim((string) ($item['nama_sekolah'] ?? '')) === '') {
                    $skipped++;

                    continue;
                }

                $kecamatan = DB::table('ref_kecamatan')
                    ->whereRaw('lower(nama) = ?', [strtolower(trim($item['kecamatan']))])
                    ->first();
                $kelurahan = $kecamatan
                    ? DB::table('ref_kelurahan')
                        ->where('kecamatan_id', $kecamatan->id)
                        ->whereRaw('lower(nama) = ?', [strtolower(trim($item['kelurahan_sekolah']))])
                        ->first()
                    : null;

                if (! $kecamatan || ! $kelurahan) {
                    $skipped++;

                    continue;
                }

                $npsn = trim($item['npsn']);
                $sekolah = Sekolah::updateOrCreate(
                    $npsn !== '' ? ['npsn' => $npsn] : ['nama' => trim($item['nama_sekolah'])],
                    [
                        'npsn' => $npsn ?: null,
                        'nama' => trim($item['nama_sekolah']),
                        'status' => strtolower(trim($item['status'])) === 'swasta' ? 'swasta' : 'negeri',
                        'kecamatan_id' => $kecamatan->id,
                        'kelurahan_id' => $kelurahan->id,
                        'alamat' => trim((string) ($item['alamat'] ?? '')),
                        'telepon' => trim((string) ($item['telepon'] ?? '')),
                        'email' => trim((string) ($item['email'] ?? '')) ?: null,
                        'is_active' => true,
                    ],
                );

                DB::table('tb_zonasi_sekolah')
                    ->where('periode_id', $periodeId)
                    ->where('sekolah_id', $sekolah->id)
                    ->delete();

                $zoneNames = array_filter(array_map('trim', explode(';', (string) $item['zonasi_kelurahan'])));
                foreach ($zoneNames as $priority => $zoneName) {
                    $zoneId = DB::table('ref_kelurahan')
                        ->whereRaw('lower(nama) = ?', [strtolower($zoneName)])
                        ->value('id');

                    if ($zoneId) {
                        DB::table('tb_zonasi_sekolah')->insert([
                            'periode_id' => $periodeId,
                            'sekolah_id' => $sekolah->id,
                            'kelurahan_id' => $zoneId,
                            'prioritas' => $priority + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $imported++;
            }
        });

        fclose($handle);

        return back()->with('success', "Import selesai: {$imported} sekolah diproses, {$skipped} baris dilewati.");
    }

    public function updateIdentitas(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_pendaftaran' => ['required', 'digits:4'],
            'tahun_pelajaran' => ['required', 'string', 'max:20'],
            'kepala_nama' => ['required', 'string', 'max:100'],
            'kepala_nip' => ['nullable', 'string', 'max:50'],
            'kepala_jabatan' => ['required', 'string', 'max:100'],
            'tanggal_tes' => ['required', 'string', 'max:100'],
            'waktu_tes' => ['required', 'string', 'max:100'],
            'tempat_tes' => ['required', 'string', 'max:150'],
            'catatan_kartu' => ['required', 'string', 'max:1000'],
            'kepala_ttd' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [
            'kepala_ttd.image' => 'Tanda tangan digital harus berupa gambar.',
            'kepala_ttd.max' => 'Ukuran tanda tangan digital maksimal 1 MB.',
        ]);

        unset($data['kepala_ttd']);

        if ($request->hasFile('kepala_ttd')) {
            $oldPath = (string) PengaturanSpmb::getValue('kepala_ttd_path', '');
            $file = $request->file('kepala_ttd');
            $name = Str::uuid().'.'.$file->extension();
            $newPath = $file->storeAs('pengaturan/tanda-tangan', $name, 'local');

            if (! $newPath) {
                return back()->with('warning', 'Tanda tangan digital gagal disimpan. Silakan coba kembali.');
            }

            $data['kepala_ttd_path'] = $newPath;

            try {
                PengaturanSpmb::setMany($data);
            } catch (Throwable $exception) {
                Storage::disk('local')->delete($newPath);

                throw $exception;
            }

            $this->deleteOldSignature($oldPath);

            return back()->with('success', 'Identitas dan pengaturan kartu pendaftaran berhasil diperbarui.');
        }

        PengaturanSpmb::setMany($data);

        return back()->with('success', 'Identitas dan pengaturan kartu pendaftaran berhasil diperbarui.');
    }

    public function showSignature(): BinaryFileResponse|StreamedResponse
    {
        return $this->signatureResponse();
    }

    public function importCalonSiswa(Request $request, CalonSiswaImportReader $reader): RedirectResponse
    {
        $data = $request->validate([
            'tahun_lulus' => ['required', 'digits:4'],
            'calon_siswa_file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:5120'],
            'deactivate_missing_in_year' => ['nullable', 'boolean'],
        ], [
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.digits' => 'Tahun lulus harus berisi 4 digit.',
            'calon_siswa_file.required' => 'File whitelist calon siswa wajib dipilih.',
            'calon_siswa_file.mimes' => 'File whitelist harus berformat XLSX, CSV, atau TXT.',
            'calon_siswa_file.max' => 'Ukuran file whitelist maksimal 5 MB.',
        ]);

        $uploadedFile = $request->file('calon_siswa_file');
        $temporaryPath = $uploadedFile->getRealPath().'.'.$uploadedFile->getClientOriginalExtension();
        copy($uploadedFile->getRealPath(), $temporaryPath);

        try {
            $result = $reader->read($temporaryPath);
        } finally {
            @unlink($temporaryPath);
        }

        if ($result['valid']->isEmpty()) {
            $detail = $result['errors'][0] ?? 'Pastikan file memiliki kolom NISN, Nama Siswa, Tempat Lahir, Tanggal Lahir, dan Asal Sekolah.';

            return back()->with('warning', 'Tidak ada data valid yang dapat diimpor. '.$detail);
        }

        $tahun = $data['tahun_lulus'];
        $validRows = $result['valid'];
        $importedNisn = $validRows->pluck('nisn')->all();

        DB::transaction(function () use ($tahun, $validRows, $importedNisn, $request): void {
            CalonSiswa::query()
                ->where('tahun_lulus', '!=', $tahun)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            if ($request->boolean('deactivate_missing_in_year')) {
                CalonSiswa::query()
                    ->where('tahun_lulus', $tahun)
                    ->whereNotIn('nisn', $importedNisn)
                    ->update(['is_active' => false]);
            }

            foreach ($validRows as $row) {
                CalonSiswa::updateOrCreate(
                    ['nisn' => $row['nisn']],
                    [
                        'nama' => $row['nama'],
                        'tempat_lahir' => $row['tempat_lahir'],
                        'tanggal_lahir' => $row['tanggal_lahir'],
                        'asal_sekolah' => $row['asal_sekolah'],
                        'nilai_tka_matematika' => $row['nilai_tka_matematika'],
                        'nilai_tka_bahasa_indonesia' => $row['nilai_tka_bahasa_indonesia'],
                        'tahun_lulus' => $tahun,
                        'is_active' => true,
                    ],
                );
            }
        });

        $message = "Whitelist calon siswa berhasil diimpor: {$validRows->count()} data aktif untuk tahun lulus {$tahun}. Data tahun lulus lain tetap tersimpan dalam status nonaktif.";

        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} baris dilewati karena tidak valid.";
        }

        if ($result['missing_score_count'] > 0) {
            $message .= " {$result['missing_score_count']} siswa memiliki nilai TKA yang belum lengkap dan tetap disimpan.";
        }

        return back()->with('success', $message);
    }

    public function deactivateCalonSiswaWhitelist(Request $request): RedirectResponse
    {
        $tahun = $this->validatedWhitelistYear($request);

        $updated = CalonSiswa::query()
            ->where('tahun_lulus', $tahun)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', "Whitelist tahun {$tahun} berhasil dinonaktifkan ({$updated} data).");
    }

    private function validatedWhitelistYear(Request $request): string
    {
        $data = $request->validate([
            'tahun_lulus' => ['required', 'digits:4', 'exists:tb_calon_siswa,tahun_lulus'],
        ], [
            'tahun_lulus.required' => 'Pilih tahun lulus terlebih dahulu.',
            'tahun_lulus.exists' => 'Tahun lulus yang dipilih tidak ditemukan.',
        ]);

        return $data['tahun_lulus'];
    }

    public function toggleCalonSiswaWhitelist(CalonSiswa $calonSiswa): RedirectResponse
    {
        $calonSiswa->update(['is_active' => ! $calonSiswa->is_active]);

        $status = $calonSiswa->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "NISN {$calonSiswa->nisn} berhasil {$status}.");
    }

    public function storeKontakPanitia(Request $request): RedirectResponse
    {
        $data = $this->validatedContact($request);
        $data['nomor_whatsapp'] = $this->normalizeWhatsapp($data['nomor_whatsapp']);
        $data['is_active'] = true;
        $data['is_primary'] = $request->boolean('is_primary');

        DB::transaction(function () use ($data): void {
            if ($data['is_primary']) {
                KontakPanitia::query()->update(['is_primary' => false]);
            }

            KontakPanitia::create($data);
        });

        return back()->with('success', 'Kontak panitia berhasil ditambahkan.');
    }

    public function updateKontakPanitia(Request $request, KontakPanitia $kontak): RedirectResponse
    {
        $data = $this->validatedContact($request);
        $data['nomor_whatsapp'] = $this->normalizeWhatsapp($data['nomor_whatsapp']);
        $data['is_active'] = $request->boolean('is_active');

        $kontak->update($data);

        return back()->with('success', 'Kontak panitia berhasil diperbarui.');
    }

    public function setKontakPanitiaUtama(KontakPanitia $kontak): RedirectResponse
    {
        DB::transaction(function () use ($kontak): void {
            KontakPanitia::query()->update(['is_primary' => false]);
            $kontak->update([
                'is_primary' => true,
                'is_active' => true,
            ]);
        });

        return back()->with('success', 'Kontak utama WhatsApp berhasil dipilih.');
    }

    public function destroyKontakPanitia(KontakPanitia $kontak): RedirectResponse
    {
        $wasPrimary = $kontak->is_primary;
        $kontak->delete();

        if ($wasPrimary) {
            KontakPanitia::query()->where('is_active', true)->orderBy('id')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Kontak panitia berhasil dihapus.');
    }

    public function verifikasiPengguna(Request $request, Pengguna $pengguna): RedirectResponse
    {
        if (! $pengguna->isCalonMurid()) {
            abort(403);
        }

        $admin = $request->attributes->get('pengguna');
        $registrasiAkun = $pengguna->registrasiAkun;

        if (! $registrasiAkun || ! $registrasiAkun->kartuKeluargaTersedia()) {
            return back()->with('warning', 'Akun belum dapat diverifikasi karena Kartu Keluarga tidak tersedia.');
        }

        DB::transaction(function () use ($pengguna, $admin): void {
            $pengguna->update([
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
                'verification_notice_seen_at' => null,
            ]);

            if ($registrasi = $pengguna->registrasiAkun) {
                $statusSebelumnya = $registrasi->status;
                $registrasi->update([
                    'status' => 'terverifikasi',
                    'catatan_verifikasi' => null,
                    'verified_at' => now(),
                    'rejected_at' => null,
                    'verified_by' => $admin?->id_pengguna,
                ]);

                DB::table('tb_riwayat_verifikasi_akun')->insert([
                    'registrasi_akun_id' => $registrasi->id,
                    'status_sebelumnya' => $statusSebelumnya,
                    'status_baru' => 'terverifikasi',
                    'catatan' => 'Alamat domisili dan Kartu Keluarga dinyatakan sesuai.',
                    'diproses_oleh' => $admin?->id_pengguna,
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('admin.verifikasi-akun.show', $pengguna->registrasiAkun)
            ->with('success', 'Akun berhasil disetujui dan calon murid sudah dapat masuk ke dashboard.');
    }

    public function togglePenggunaAktif(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->update(['is_active' => ! $pengguna->is_active]);

        return back()->with('success', $pengguna->is_active ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.');
    }

    public function updateStatusVerifikasiPengguna(Request $request, Pengguna $pengguna): RedirectResponse
    {
        if (! $pengguna->isCalonMurid()) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:perlu_perbaikan,ditolak'],
            'catatan' => ['required', 'string', 'max:1000'],
        ], [
            'catatan.required' => 'Catatan verifikasi wajib diisi agar calon murid mengetahui bagian yang harus diperbaiki.',
        ]);

        $admin = $request->attributes->get('pengguna');

        DB::transaction(function () use ($pengguna, $admin, $data): void {
            $pengguna->update([
                'is_verified' => false,
                'verified_at' => null,
            ]);

            $registrasi = $pengguna->registrasiAkun;
            abort_unless($registrasi, 422, 'Data registrasi akun belum tersedia.');

            $statusSebelumnya = $registrasi->status;
            $registrasi->update([
                'status' => $data['status'],
                'catatan_verifikasi' => $data['catatan'],
                'verified_at' => null,
                'rejected_at' => $data['status'] === 'ditolak' ? now() : null,
                'verified_by' => $admin?->id_pengguna,
            ]);

            DB::table('tb_riwayat_verifikasi_akun')->insert([
                'registrasi_akun_id' => $registrasi->id,
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru' => $data['status'],
                'catatan' => $data['catatan'],
                'diproses_oleh' => $admin?->id_pengguna,
                'created_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.verifikasi-akun.show', $pengguna->registrasiAkun)
            ->with('success', 'Status dan catatan verifikasi berhasil disimpan pada panel status calon murid.');
    }

    public function destroyPengguna(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        if ($pengguna->formulir()->exists()) {
            return back()->with(
                'warning',
                'Akun tidak dapat dihapus karena sudah memiliki formulir pendaftaran. Nonaktifkan akun jika akses siswa perlu dihentikan.',
            );
        }

        $pengguna->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function guardUserAccount(Pengguna $pengguna): void
    {
        if (! $pengguna->isCalonMurid()) {
            abort(403);
        }
    }

    private function signatureResponse(): BinaryFileResponse|StreamedResponse
    {
        $path = $this->currentSignaturePath();

        abort_unless(str_starts_with($path, 'pengaturan/tanda-tangan/'), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, basename($path), [
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }

    protected function currentSignaturePath(): string
    {
        return (string) PengaturanSpmb::getValue('kepala_ttd_path', '');
    }

    private function deleteOldSignature(string $path): void
    {
        if (str_starts_with($path, 'pengaturan/tanda-tangan/')) {
            Storage::disk('local')->delete($path);

            return;
        }

        if (! str_starts_with($path, 'uploads/pengaturan/')) {
            return;
        }

        $basePath = realpath(public_path('uploads/pengaturan'));
        $filePath = realpath(public_path($path));

        if ($basePath && $filePath && str_starts_with($filePath, $basePath.DIRECTORY_SEPARATOR) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    private function validatedContact(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'label' => ['nullable', 'string', 'max:100'],
            'nomor_whatsapp' => ['required', 'regex:/^(\+?62|0|8)[0-9]{8,13}$/'],
        ], [
            'nomor_whatsapp.regex' => 'Nomor WhatsApp harus diawali 62, +62, 0, atau 8 dan berisi angka yang valid.',
        ]);
    }

    private function normalizeWhatsapp(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    private function validatedSchool(Request $request, ?Sekolah $sekolah = null): array
    {
        return $request->validate([
            'npsn' => ['nullable', 'string', 'max:20', Rule::unique('tb_sekolah', 'npsn')->ignore($sekolah?->id)],
            'nama' => ['required', 'string', 'max:150'],
            'status' => ['required', 'in:negeri,swasta'],
            'kecamatan_id' => ['required', 'integer', 'exists:ref_kecamatan,id'],
            'kelurahan_id' => ['required', 'integer', 'exists:ref_kelurahan,id'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);
    }
}
