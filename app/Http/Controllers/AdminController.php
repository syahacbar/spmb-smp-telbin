<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\KontakPanitia;
use App\Models\PengaturanSpmb;
use App\Models\Pengguna;
use App\Models\ProgramKeahlian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
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
        return view('admin.pengguna', [
            'pengguna' => $request->attributes->get('pengguna'),
            'users' => Pengguna::with('calonSiswa')->where('level', 'User')->orderBy('id_pengguna')->get(),
        ]);
    }

    public function pengaturan(Request $request): View
    {
        return view('admin.pengaturan', [
            'pengguna' => $request->attributes->get('pengguna'),
            'settings' => PengaturanSpmb::allSettings(),
            'programs' => ProgramKeahlian::query()->ordered()->get(),
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
            $dir = public_path('uploads/pengaturan');

            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $file = $request->file('kepala_ttd');
            $name = 'ttd_kepala_'.time().'.'.$file->extension();
            $file->move($dir, $name);
            $data['kepala_ttd_path'] = 'uploads/pengaturan/'.$name;
        }

        PengaturanSpmb::setMany($data);

        return back()->with('success', 'Identitas dan pengaturan kartu pendaftaran berhasil diperbarui.');
    }

    public function updateProgramKeahlian(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'programs' => ['required', 'array'],
            'programs.*.nama' => ['required', 'string', 'max:100'],
            'programs.*.singkatan' => ['nullable', 'string', 'max:20'],
            'programs.*.kuota' => ['required', 'integer', 'min:0', 'max:10000'],
            'programs.*.aliases' => ['nullable', 'string', 'max:500'],
            'programs.*.urutan' => ['required', 'integer', 'min:0', 'max:10000'],
            'programs.*.is_active' => ['nullable', 'boolean'],
        ]);

        foreach ($data['programs'] as $id => $programData) {
            $program = ProgramKeahlian::findOrFail($id);

            $program->update([
                'nama' => $programData['nama'],
                'singkatan' => $programData['singkatan'] ?? null,
                'kuota' => $programData['kuota'],
                'aliases' => $this->aliasesFromInput($programData['aliases'] ?? ''),
                'urutan' => $programData['urutan'],
                'is_active' => (bool) ($programData['is_active'] ?? false),
            ]);
        }

        return back()->with('success', 'Kuota program keahlian berhasil diperbarui.');
    }

    public function storeProgramKeahlian(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100', 'unique:tb_program_keahlian,nama'],
            'singkatan' => ['nullable', 'string', 'max:20'],
            'kuota' => ['required', 'integer', 'min:0', 'max:10000'],
            'aliases' => ['nullable', 'string', 'max:500'],
            'urutan' => ['required', 'integer', 'min:0', 'max:10000'],
        ]);

        ProgramKeahlian::create([
            'nama' => $data['nama'],
            'singkatan' => $data['singkatan'] ?? null,
            'kuota' => $data['kuota'],
            'aliases' => $this->aliasesFromInput($data['aliases'] ?? ''),
            'urutan' => $data['urutan'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Program keahlian berhasil ditambahkan.');
    }

    public function destroyProgramKeahlian(ProgramKeahlian $program): RedirectResponse
    {
        $program->delete();

        return back()->with('success', 'Program keahlian berhasil dihapus.');
    }

    public function importCalonSiswa(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tahun_pendaftaran' => ['required', 'digits:4'],
            'calon_siswa_csv' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
            'deactivate_missing_in_year' => ['nullable', 'boolean'],
        ], [
            'calon_siswa_csv.required' => 'File CSV whitelist calon siswa wajib dipilih.',
            'calon_siswa_csv.mimes' => 'File whitelist harus berformat CSV atau TXT.',
            'calon_siswa_csv.max' => 'Ukuran file whitelist maksimal 4 MB.',
        ]);

        $result = $this->readCalonSiswaCsv($request->file('calon_siswa_csv')->getRealPath());

        if ($result['valid']->isEmpty()) {
            return back()->with('warning', 'Tidak ada data valid yang dapat diimport. Pastikan header CSV berisi nisn,nama,tempat_lahir,tanggal_lahir,asal_sekolah.');
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
                        'tahun_pendaftaran' => $tahun,
                        'is_active' => true,
                    ],
                );
            }
        });

        $message = "Whitelist calon siswa berhasil diimport: {$validRows->count()} data aktif untuk tahun {$tahun}.";

        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} baris dilewati karena tidak valid.";
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

    public function storePengguna(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'digits:10', 'unique:tb_pengguna,id_pengguna'],
            'no_wa' => ['required', 'regex:/^8[0-9]{8,11}$/'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
            'nisn.unique' => 'NISN tersebut sudah memiliki akun.',
            'no_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali angka 8 dan berisi 9 sampai 12 digit setelah kode +62.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $pengguna = Pengguna::create([
            'id_pengguna' => $data['nisn'],
            'nama_pengguna' => '',
            'email' => null,
            'telpon' => '62'.$data['no_wa'],
            'username' => $data['nisn'],
            'password' => Hash::make('siswa123'),
            'level' => 'User',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.pengguna.formulir.create', $pengguna)
            ->with('success', 'Akun siswa berhasil dibuat dengan password default siswa123. Silakan lanjut isi biodata pendaftaran.');
    }

    public function verifikasiPengguna(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }

        $pengguna->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        if (! $pengguna->email) {
            return back()->with('success', 'Akun berhasil diverifikasi. Email notifikasi belum dikirim karena siswa belum mengisi email.');
        }

        try {
            Mail::raw(
                "Akun SPMB anda dengan NISN {$pengguna->id_pengguna} sudah diverifikasi. Silakan login untuk melanjutkan pengisian formulir pendaftaran.",
                function ($message) use ($pengguna): void {
                    $message->to($pengguna->email)
                        ->subject('Akun SPMB Sudah Diverifikasi');
                },
            );
        } catch (Throwable) {
            return back()->with('warning', 'Akun berhasil diverifikasi, tetapi email notifikasi belum terkirim. Periksa konfigurasi email aplikasi.');
        }

        return back()->with('success', 'Akun berhasil diverifikasi dan email notifikasi telah dikirim.');
    }

    public function togglePenggunaAktif(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->update(['is_active' => ! $pengguna->is_active]);

        return back()->with('success', $pengguna->is_active ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.');
    }

    public function resetPasswordPengguna(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->update(['password' => Hash::make('siswa123')]);

        return back()->with('success', 'Password user berhasil direset ke default siswa123.');
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
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }
    }

    private function aliasesFromInput(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value))
            ->map(fn (string $alias) => trim($alias))
            ->filter()
            ->values()
            ->all();
    }

    private function readCalonSiswaCsv(string $path): array
    {
        $file = fopen($path, 'r');
        $header = fgetcsv($file);
        $valid = collect();
        $skipped = 0;

        if (! $header) {
            fclose($file);

            return ['valid' => $valid, 'skipped' => 0];
        }

        $header = array_map(fn (string $column): string => strtolower(trim($column)), $header);
        $requiredColumns = ['nisn', 'nama', 'tempat_lahir', 'tanggal_lahir', 'asal_sekolah'];

        if (array_diff($requiredColumns, $header)) {
            fclose($file);

            return ['valid' => $valid, 'skipped' => 0];
        }

        while (($row = fgetcsv($file)) !== false) {
            $row = array_slice(array_pad($row, count($header), ''), 0, count($header));
            $data = array_combine($header, $row);

            if (! $data) {
                $skipped++;
                continue;
            }

            $nisn = preg_replace('/\D+/', '', (string) ($data['nisn'] ?? ''));
            $tanggalLahir = $this->normalizeDate((string) ($data['tanggal_lahir'] ?? ''));

            if (
                strlen($nisn) !== 10
                || trim((string) ($data['nama'] ?? '')) === ''
                || trim((string) ($data['tempat_lahir'] ?? '')) === ''
                || ! $tanggalLahir
                || trim((string) ($data['asal_sekolah'] ?? '')) === ''
            ) {
                $skipped++;
                continue;
            }

            $valid->push([
                'nisn' => $nisn,
                'nama' => trim((string) $data['nama']),
                'tempat_lahir' => trim((string) $data['tempat_lahir']),
                'tanggal_lahir' => $tanggalLahir,
                'asal_sekolah' => trim((string) $data['asal_sekolah']),
            ]);
        }

        fclose($file);

        return [
            'valid' => $valid->unique('nisn')->values(),
            'skipped' => $skipped,
        ];
    }

    private function normalizeDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        $timestamp = strtotime(str_replace('/', '-', $value));

        return $timestamp ? date('Y-m-d', $timestamp) : null;
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
