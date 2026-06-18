<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\KontakPanitia;
use App\Models\PengaturanSpmb;
use App\Models\Pengguna;
use App\Services\CalonSiswaImportReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'formulirs' => Formulir::where('status', 'submitted')->latest('id')->get(),
        ]);
    }

    public function pengguna(Request $request): View
    {
        $users = Pengguna::with(['calonSiswa', 'registrasiAkun'])
            ->whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))
            ->orderBy('id_pengguna')
            ->get();

        return view('admin.pengguna', [
            'pengguna' => $request->attributes->get('pengguna'),
            'users' => $users,
            'kecamatanNames' => DB::table('ref_kecamatan')->pluck('nama', 'id'),
            'kelurahanNames' => DB::table('ref_kelurahan')->pluck('nama', 'id'),
        ]);
    }

    public function pengaturan(Request $request): View
    {
        return view('admin.pengaturan', [
            'pengguna' => $request->attributes->get('pengguna'),
            'settings' => PengaturanSpmb::allSettings(),
            'contacts' => KontakPanitia::query()->orderByDesc('is_primary')->orderBy('id')->get(),
            'whitelistStats' => CalonSiswa::query()
                ->select('tahun_pendaftaran')
                ->selectRaw('count(*) as total')
                ->selectRaw('sum(case when is_active = 1 then 1 else 0 end) as active_total')
                ->groupBy('tahun_pendaftaran')
                ->orderByDesc('tahun_pendaftaran')
                ->get(),
            'whitelistYears' => CalonSiswa::query()
                ->select('tahun_pendaftaran')
                ->distinct()
                ->orderByDesc('tahun_pendaftaran')
                ->pluck('tahun_pendaftaran'),
            'whitelist' => CalonSiswa::query()
                ->orderByDesc('tahun_pendaftaran')
                ->orderByDesc('is_active')
                ->orderBy('nama')
                ->get(),
        ]);
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
            'tahun_pendaftaran' => ['required', 'digits:4'],
            'calon_siswa_file' => ['required', 'file', 'mimes:xlsx,csv,txt', 'max:5120'],
            'deactivate_missing_in_year' => ['nullable', 'boolean'],
        ], [
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

        $tahun = $data['tahun_pendaftaran'];
        $validRows = $result['valid'];
        $importedNisn = $validRows->pluck('nisn')->all();

        DB::transaction(function () use ($tahun, $validRows, $importedNisn, $request): void {
            if ($request->boolean('deactivate_missing_in_year')) {
                CalonSiswa::query()
                    ->where('tahun_pendaftaran', $tahun)
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
                        'tahun_pendaftaran' => $tahun,
                        'is_active' => true,
                    ],
                );
            }
        });

        $message = "Whitelist calon siswa berhasil diimpor: {$validRows->count()} data aktif untuk tahun {$tahun}.";

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
            ->where('tahun_pendaftaran', $tahun)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', "Whitelist tahun {$tahun} berhasil dinonaktifkan ({$updated} data).");
    }

    public function activateCalonSiswaWhitelist(Request $request): RedirectResponse
    {
        $tahun = $this->validatedWhitelistYear($request);

        $updated = CalonSiswa::query()
            ->where('tahun_pendaftaran', $tahun)
            ->where('is_active', false)
            ->update(['is_active' => true]);

        return back()->with('success', "Whitelist tahun {$tahun} berhasil diaktifkan ({$updated} data).");
    }

    private function validatedWhitelistYear(Request $request): string
    {
        $data = $request->validate([
            'tahun_pendaftaran' => ['required', 'digits:4', 'exists:tb_calon_siswa,tahun_pendaftaran'],
        ], [
            'tahun_pendaftaran.required' => 'Pilih tahun pendaftaran terlebih dahulu.',
            'tahun_pendaftaran.exists' => 'Tahun pendaftaran yang dipilih tidak ditemukan.',
        ]);

        return $data['tahun_pendaftaran'];
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

        return back()->with('success', 'Akun berhasil diverifikasi. Kirim pemberitahuan melalui tombol WhatsApp.');
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

        return back()->with('success', 'Status verifikasi akun berhasil diperbarui. Gunakan tombol WhatsApp untuk menyampaikan hasil kepada pendaftar.');
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
}
