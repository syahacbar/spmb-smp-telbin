<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\JalurPendaftaran;
use App\Models\PengaturanSpmb;
use App\Models\Pengguna;
use App\Models\Sekolah;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class FormulirController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');
        abort_unless($pengguna->isCalonMurid(), 403);
        $formulir = Formulir::where('nisn', $pengguna->id_pengguna)->first();

        if ($formulir) {
            if ($formulir->isSubmitted()) {
                return redirect()->route('formulir.riwayat');
            }

            return redirect()->route('formulir.edit', $formulir);
        }

        return view('formulir.form', [
            'pengguna' => $pengguna,
            'akunPendaftar' => $pengguna,
            'calonSiswa' => $pengguna->calonSiswa,
            'formulir' => new Formulir(array_merge(
                ['nisn' => $pengguna->id_pengguna],
                $this->calonSiswaFormulirData($pengguna->id_pengguna),
            )),
            ...$this->formReferences($pengguna->id_pengguna),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');
        abort_unless($pengguna->isCalonMurid(), 403);

        if ($formulir = Formulir::where('nisn', $pengguna->id_pengguna)->first()) {
            return redirect()->route(
                $formulir->isSubmitted() ? 'formulir.riwayat' : 'formulir.edit',
                $formulir->isSubmitted() ? [] : $formulir,
            )->with('warning', 'Formulir anda sudah tersedia.');
        }

        $data = $this->validatedData($request, true, $pengguna->id_pengguna);
        $this->syncAkunEmail($pengguna->id_pengguna, $data['email'] ?? null);
        unset($data['email']);

        $data['nisn'] = $pengguna->id_pengguna;
        $data['hp'] = $pengguna->telpon;
        $data['status'] = 'draft';
        $data['submitted_at'] = null;
        $data['kartu_keluarga'] = $this->registrationFamilyCardPath($pengguna->id_pengguna);
        $data = array_merge($data, $this->calonSiswaFormulirData($pengguna->id_pengguna));
        $uploadedPaths = $this->storeUploads($request);
        $data = array_merge($data, $uploadedPaths);

        try {
            $formulir = Formulir::create($data);
        } catch (QueryException $exception) {
            $this->deleteUploadedFiles($uploadedPaths);

            if ($formulir = Formulir::where('nisn', $pengguna->id_pengguna)->first()) {
                return redirect()->route(
                    $formulir->isSubmitted() ? 'formulir.riwayat' : 'formulir.edit',
                    $formulir->isSubmitted() ? [] : $formulir,
                )->with('warning', 'Formulir anda sudah tersimpan dari permintaan sebelumnya.');
            }

            throw $exception;
        }

        return redirect()->route('formulir.periksa', $formulir)->with('success', 'Data formulir berhasil disimpan. Periksa kembali sebelum mengirim final.');
    }

    public function riwayat(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');
        abort_unless($pengguna->isCalonMurid(), 403);

        return view('formulir.riwayat', [
            'pengguna' => $pengguna,
            'formulirs' => Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->get(),
        ]);
    }

    public function edit(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if (! $pengguna->isAdminDinas() && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        $akunPendaftar = Pengguna::find($formulir->nisn) ?: $pengguna;

        return view('formulir.form', [
            'pengguna' => $pengguna,
            'formulir' => $formulir,
            'akunPendaftar' => $akunPendaftar,
            'calonSiswa' => CalonSiswa::find($formulir->nisn),
            ...$this->formReferences($formulir->nisn),
        ]);
    }

    public function adminCreate(Request $request, Pengguna $pengguna): View|RedirectResponse
    {
        $admin = $request->attributes->get('pengguna');

        if ($pengguna->isAdminDinas()) {
            abort(403);
        }

        $formulir = Formulir::where('nisn', $pengguna->id_pengguna)->first();

        if ($formulir) {
            return redirect()->route('formulir.edit', $formulir);
        }

        return view('formulir.form', [
            'pengguna' => $admin,
            'akunPendaftar' => $pengguna,
            'calonSiswa' => $pengguna->calonSiswa,
            'formulir' => new Formulir(array_merge(
                ['nisn' => $pengguna->id_pengguna],
                $this->calonSiswaFormulirData($pengguna->id_pengguna),
            )),
            'formAction' => route('admin.pengguna.formulir.store', $pengguna),
            ...$this->formReferences($pengguna->id_pengguna),
        ]);
    }

    public function adminStore(Request $request, Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->isAdminDinas()) {
            abort(403);
        }

        if (Formulir::where('nisn', $pengguna->id_pengguna)->exists()) {
            return redirect()->route('admin.pengguna.formulir.create', $pengguna)
                ->with('warning', 'Formulir user tersebut sudah tersedia.');
        }

        $data = $this->validatedData($request, true, $pengguna->id_pengguna);
        $this->syncAkunEmail($pengguna->id_pengguna, $data['email'] ?? null);
        unset($data['email']);

        $data['nisn'] = $pengguna->id_pengguna;
        $data['hp'] = $pengguna->telpon;
        $data['status'] = 'draft';
        $data['submitted_at'] = null;
        $data['kartu_keluarga'] = $this->registrationFamilyCardPath($pengguna->id_pengguna);
        $data = array_merge($data, $this->calonSiswaFormulirData($pengguna->id_pengguna));
        $uploadedPaths = $this->storeUploads($request);
        $data = array_merge($data, $uploadedPaths);

        try {
            $formulir = Formulir::create($data);
        } catch (QueryException $exception) {
            $this->deleteUploadedFiles($uploadedPaths);

            if (Formulir::where('nisn', $pengguna->id_pengguna)->exists()) {
                return redirect()->route('admin.pengguna.formulir.create', $pengguna)
                    ->with('warning', 'Formulir user tersebut sudah tersimpan dari permintaan sebelumnya.');
            }

            throw $exception;
        }

        return redirect()->route('formulir.periksa', $formulir)->with('success', 'Data formulir user berhasil disimpan. Periksa kembali sebelum mengirim final.');
    }

    public function update(Request $request, Formulir $formulir): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if (! $pengguna->isAdminDinas() && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        $data = $this->validatedData($request, false, $formulir->nisn);
        $akunPendaftar = Pengguna::find($formulir->nisn);

        if ($akunPendaftar) {
            $this->syncAkunEmail($akunPendaftar->id_pengguna, $data['email'] ?? null);
            $data['hp'] = $akunPendaftar->telpon;
        }

        unset($data['email']);

        $data = array_merge($data, $this->calonSiswaFormulirData($formulir->nisn));
        $uploadedPaths = $this->storeUploads($request);
        $replacedPaths = collect(array_keys($uploadedPaths))
            ->mapWithKeys(fn (string $field): array => [$field => $formulir->{$field}])
            ->filter()
            ->all();

        try {
            $formulir->update(array_merge($data, $uploadedPaths));
        } catch (Throwable $exception) {
            $this->deleteDocumentFiles($uploadedPaths);

            throw $exception;
        }

        $this->deleteDocumentFiles($replacedPaths, true);

        if ($pengguna->isAdminDinas()) {
            return redirect()->route('admin.pendaftar')->with('success', 'Formulir berhasil diperbarui.');
        }

        return redirect()->route('formulir.periksa', $formulir)
            ->with('success', 'Formulir berhasil diperbarui. Periksa kembali sebelum mengirim final.');
    }

    public function periksa(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        return view('formulir.periksa', compact('pengguna', 'formulir'));
    }

    public function kirim(Request $request, Formulir $formulir): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('success', 'Formulir sudah dikirim final.');
        }

        $formulir->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('formulir.riwayat')->with('success', 'Formulir berhasil dikirim final. Kartu pendaftaran sudah dapat dicetak.');
    }

    public function cetak(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if (! $formulir->isSubmitted()) {
            return redirect()->route('formulir.periksa', $formulir)->with('warning', 'Kartu pendaftaran dapat dicetak setelah formulir dikirim final.');
        }

        return view('formulir.cetak', [
            'pengguna' => $pengguna,
            'formulir' => $formulir,
            'settings' => PengaturanSpmb::allSettings(),
        ]);
    }

    private function validatedData(Request $request, bool $requireFiles = true, ?string $nisn = null): array
    {
        $requiredFileRule = $requireFiles ? 'required' : 'nullable';
        $hasMasterIdentity = $nisn && CalonSiswa::whereKey($nisn)->exists();
        $parentAddressSame = $request->boolean('alamat_ortu_sama_dengan_siswa');
        $identityRules = $hasMasterIdentity
            ? [
                'nama' => ['nullable', 'string', 'max:100'],
                'tempat_lahir' => ['nullable', 'string', 'max:100'],
                'tanggal_lahir' => ['nullable', 'date'],
                'asal_sekolah' => ['nullable', 'string', 'max:100'],
            ]
            : [
                'nama' => ['required', 'string', 'max:100'],
                'tempat_lahir' => ['required', 'string', 'max:100'],
                'tanggal_lahir' => ['required', 'date', 'before_or_equal:-13 years', 'after_or_equal:-21 years'],
                'asal_sekolah' => ['required', 'string', 'max:100'],
            ];

        $data = $request->validate(array_merge($identityRules, [
            'email' => ['nullable', 'email', 'max:100', Rule::unique('tb_pengguna', 'email')->ignore($nisn, 'id_pengguna')],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('tb_formulir', 'nik')->ignore($nisn, 'nisn'),
            ],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['required', 'string', 'max:50'],
            'nama_ayah' => ['required', 'string', 'max:100'],
            'pekerjaan_ayah' => ['required', 'string', 'max:100'],
            'nama_ibu' => ['required', 'string', 'max:100'],
            'pekerjaan_ibu' => ['required', 'string', 'max:100'],
            'hp_ortu' => ['required', 'string', 'max:20'],
            'alamat_ortu_sama_dengan_siswa' => ['nullable', 'boolean'],
            'alamat_ortu' => [$parentAddressSame ? 'nullable' : 'required', 'string'],
            'alamat_ortu_provinsi' => [$parentAddressSame ? 'nullable' : 'required', 'string', 'max:100'],
            'alamat_ortu_kabupaten' => [$parentAddressSame ? 'nullable' : 'required', 'string', 'max:100'],
            'alamat_ortu_kecamatan' => [$parentAddressSame ? 'nullable' : 'required', 'string', 'max:100'],
            'alamat_ortu_kelurahan' => [$parentAddressSame ? 'nullable' : 'required', 'string', 'max:100'],
            'jalur_id' => [
                'nullable',
                Rule::exists('tb_jalur_pendaftaran', 'id')->where('is_active', true),
            ],
            'sekolah_id' => [
                'required',
                Rule::exists('tb_sekolah', 'id')->where('is_active', true),
            ],
            'surat_keterangan_lulus' => ['prohibited'],
            'kartu_keluarga' => ['prohibited'],
            'foto_selfie' => [$requiredFileRule, 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
            'dokumen_pendukung' => [
                Rule::requiredIf(function () use ($request, $requireFiles): bool {
                    if (! $requireFiles) {
                        return false;
                    }

                    $kode = DB::table('tb_jalur_pendaftaran')->where('id', $request->input('jalur_id'))->value('kode');

                    return in_array($kode, ['afirmasi', 'mutasi'], true);
                }),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]), [
            'nik.digits' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK tersebut sudah digunakan oleh pendaftar lain.',
            'foto_selfie.required' => 'Pas foto wajib diunggah.',
            'foto_selfie.image' => 'Pas foto harus berupa gambar.',
            'foto_selfie.mimes' => 'Pas foto harus berupa file JPG, JPEG, atau PNG.',
            'foto_selfie.max' => 'Ukuran pas foto maksimal 4 MB.',
        ]);

        unset($data['surat_keterangan_lulus'], $data['kartu_keluarga'], $data['foto_selfie'], $data['dokumen_pendukung']);
        $data['alamat_ortu_sama_dengan_siswa'] = $parentAddressSame;
        $data = array_merge($data, $this->domisiliFormulirData($nisn));

        $kelurahanId = Pengguna::find($nisn)?->registrasiAkun?->kelurahan_id;
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        $isZonedSchool = DB::table('tb_zonasi_sekolah')
            ->where('periode_id', $periodeId)
            ->where('kelurahan_id', $kelurahanId)
            ->where('sekolah_id', $data['sekolah_id'])
            ->exists();

        $jalurKode = DB::table('tb_jalur_pendaftaran')->where('id', $data['jalur_id'] ?? null)->value('kode');

        if ($isZonedSchool) {
            if (! in_array($jalurKode, ['domisili', 'prestasi', 'afirmasi', 'mutasi'], true)) {
                throw ValidationException::withMessages([
                    'jalur_id' => 'Pilih salah satu jalur pendaftaran yang valid (Domisili, Prestasi, Afirmasi, atau Mutasi).',
                ]);
            }
        } else {
            if (! in_array($jalurKode, ['prestasi', 'afirmasi', 'mutasi'], true)) {
                throw ValidationException::withMessages([
                    'jalur_id' => 'Sekolah berada di luar zonasi. Pilih Jalur Prestasi, Afirmasi, atau Mutasi.',
                ]);
            }
        }

        if (
            $jalurKode === 'prestasi'
            && ! CalonSiswa::query()
                ->whereKey($nisn)
                ->whereNotNull('nilai_tka_matematika')
                ->whereNotNull('nilai_tka_bahasa_indonesia')
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'jalur_id' => 'Jalur Prestasi tidak dapat dipilih karena nilai TKA belum lengkap.',
            ]);
        }

        if (
            in_array($jalurKode, ['afirmasi', 'mutasi'], true)
            && ! $request->hasFile('dokumen_pendukung')
            && ! ($nisn ? Formulir::where('nisn', $nisn)->value('dokumen_pendukung') : null)
        ) {
            throw ValidationException::withMessages([
                'dokumen_pendukung' => 'Dokumen pendukung wajib diunggah untuk jalur afirmasi atau mutasi orang tua/wali.',
            ]);
        }

        if ($parentAddressSame) {
            $data['alamat_ortu_provinsi'] = 'Papua Barat';
            $data['alamat_ortu_kabupaten'] = $data['alamat_kabupaten'];
            $data['alamat_ortu_kecamatan'] = $data['alamat_kecamatan'];
            $data['alamat_ortu_kelurahan'] = $data['alamat_kelurahan'];
            $data['alamat_ortu'] = $data['alamat'];
        }

        return $data;
    }

    private function syncAkunEmail(string $nisn, ?string $email): void
    {
        Pengguna::whereKey($nisn)->update(['email' => $email ?: null]);
    }

    private function storeUploads(Request $request): array
    {
        $paths = [];

        try {
            foreach (array_intersect(Formulir::DOCUMENT_FIELDS, ['foto_selfie', 'dokumen_pendukung']) as $field) {
                if (! $request->hasFile($field)) {
                    continue;
                }

                $file = $request->file($field);
                $name = Str::uuid().'_'.$field.'.'.$file->extension();
                $path = $file->storeAs('dokumen', $name, 'local');

                if (! $path) {
                    throw new RuntimeException("Berkas {$field} gagal disimpan.");
                }

                $paths[$field] = $path;
            }
        } catch (Throwable $exception) {
            $this->deleteDocumentFiles($paths);

            throw $exception;
        }

        return $paths;
    }

    private function deleteUploadedFiles(array $paths): void
    {
        $this->deleteDocumentFiles($paths);
    }

    private function deleteDocumentFiles(array $paths, bool $skipReferencedFiles = false): void
    {
        foreach ($paths as $path) {
            if (! is_string($path) || $path === '') {
                continue;
            }

            if ($skipReferencedFiles && $this->documentPathIsReferenced($path)) {
                continue;
            }

            if (str_starts_with($path, 'dokumen/')) {
                Storage::disk('local')->delete($path);

                continue;
            }

            if (str_starts_with($path, 'uploads/dokumen/')) {
                $this->deleteLegacyPublicDocument($path);
            }
        }
    }

    private function documentPathIsReferenced(string $path): bool
    {
        return Formulir::query()
            ->where(function ($query) use ($path): void {
                foreach (Formulir::DOCUMENT_FIELDS as $field) {
                    $query->orWhere($field, $path);
                }
            })
            ->exists();
    }

    private function deleteLegacyPublicDocument(string $path): void
    {
        $basePath = realpath(public_path('uploads/dokumen'));
        $filePath = realpath(public_path($path));

        if (! $basePath || ! $filePath || ! str_starts_with($filePath, $basePath.DIRECTORY_SEPARATOR)) {
            return;
        }

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    private function formReferences(string $nisn): array
    {
        $wilayah = DB::table('ref_wilayah_kelurahan')
            ->join('ref_wilayah_kecamatan', 'ref_wilayah_kecamatan.id', '=', 'ref_wilayah_kelurahan.kecamatan_id')
            ->join('ref_wilayah_kabupaten', 'ref_wilayah_kabupaten.id', '=', 'ref_wilayah_kecamatan.kabupaten_id')
            ->join('ref_wilayah_provinsi', 'ref_wilayah_provinsi.id', '=', 'ref_wilayah_kabupaten.provinsi_id')
            ->orderBy('ref_wilayah_provinsi.urutan')
            ->orderBy('ref_wilayah_kabupaten.urutan')
            ->orderBy('ref_wilayah_kecamatan.urutan')
            ->orderBy('ref_wilayah_kelurahan.urutan')
            ->get([
                'ref_wilayah_provinsi.nama as provinsi',
                'ref_wilayah_kabupaten.nama as kabupaten',
                'ref_wilayah_kecamatan.nama as kecamatan',
                'ref_wilayah_kelurahan.nama as kelurahan',
            ]);

        $pengguna = Pengguna::with('registrasiAkun')->find($nisn);
        $domisili = $this->domisiliFormulirData($nisn);
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        $eligibleDomisiliIds = DB::table('tb_zonasi_sekolah')
            ->where('periode_id', $periodeId)
            ->where('kelurahan_id', $pengguna?->registrasiAkun?->kelurahan_id)
            ->pluck('sekolah_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        return [
            'domisili' => $domisili,
            'sekolahAsalOptions' => CalonSiswa::query()
                ->where('is_active', true)
                ->whereNotNull('asal_sekolah')
                ->distinct()
                ->orderBy('asal_sekolah')
                ->pluck('asal_sekolah')
                ->all(),
            'wilayahProvinsiOptions' => DB::table('ref_wilayah_provinsi')
                ->orderBy('urutan')
                ->orderBy('nama')
                ->pluck('nama')
                ->all(),
            'wilayahOptions' => $wilayah
                ->groupBy('provinsi')
                ->map(fn ($provinsiItems) => $provinsiItems
                    ->groupBy('kabupaten')
                    ->map(fn ($kabupatenItems) => $kabupatenItems
                        ->groupBy('kecamatan')
                        ->map(fn ($kecamatanItems) => $kecamatanItems->pluck('kelurahan')->values()->all())
                        ->all())
                    ->all())
                ->all(),
            'jalurOptions' => JalurPendaftaran::query()->where('is_active', true)->orderBy('id')->get(),
            'schoolOptions' => Sekolah::query()
                ->where('is_active', true)
                ->orderBy('nama')
                ->get()
                ->map(fn (Sekolah $sekolah) => [
                    'id' => $sekolah->id,
                    'nama' => $sekolah->nama,
                    'status' => $sekolah->status,
                    'eligible_domisili' => in_array($sekolah->id, $eligibleDomisiliIds, true),
                ])
                ->values(),
            'nilaiTka' => $pengguna?->calonSiswa
                ? [
                    'matematika' => $pengguna->calonSiswa->nilai_tka_matematika,
                    'bahasa_indonesia' => $pengguna->calonSiswa->nilai_tka_bahasa_indonesia,
                ]
                : null,
            'registrasiAkun' => $pengguna?->registrasiAkun,
        ];
    }

    private function registrationFamilyCardPath(string $nisn): string
    {
        $path = Pengguna::with('registrasiAkun')->find($nisn)?->registrasiAkun?->kartu_keluarga_path;

        if (! $path || ! Storage::disk('local')->exists($path)) {
            throw ValidationException::withMessages([
                'kartu_keluarga' => 'Kartu Keluarga pada registrasi akun tidak tersedia. Silakan hubungi panitia.',
            ]);
        }

        return $path;
    }

    private function domisiliFormulirData(?string $nisn): array
    {
        $registrasi = $nisn
            ? Pengguna::with('registrasiAkun')->find($nisn)?->registrasiAkun
            : null;

        $kecamatan = $registrasi?->kecamatan_id
            ? DB::table('ref_kecamatan')->where('id', $registrasi->kecamatan_id)->value('nama')
            : null;
        $kelurahan = $registrasi?->kelurahan_id
            ? DB::table('ref_kelurahan')->where('id', $registrasi->kelurahan_id)->value('nama')
            : null;

        if (! $registrasi || ! $kecamatan || ! $kelurahan || ! $registrasi->detail_alamat) {
            throw ValidationException::withMessages([
                'domisili' => 'Data domisili akun belum lengkap. Silakan hubungi panitia untuk memperbarui data registrasi akun.',
            ]);
        }

        return [
            'alamat' => $registrasi->detail_alamat,
            'alamat_kabupaten' => $registrasi->kabupaten ?: 'Teluk Bintuni',
            'alamat_kecamatan' => $kecamatan,
            'alamat_kelurahan' => $kelurahan,
        ];
    }

    private function calonSiswaFormulirData(string $nisn): array
    {
        $calonSiswa = CalonSiswa::find($nisn);

        if (! $calonSiswa) {
            return [];
        }

        return [
            'nama' => $calonSiswa->nama,
            'tempat_lahir' => $calonSiswa->tempat_lahir,
            'tanggal_lahir' => $calonSiswa->tanggal_lahir,
            'asal_sekolah' => $calonSiswa->asal_sekolah,
        ];
    }

    private function authorizeFormulir($pengguna, Formulir $formulir): void
    {
        if (! $pengguna->isAdminDinas() && $formulir->nisn !== $pengguna->id_pengguna) {
            abort(403);
        }
    }
}
