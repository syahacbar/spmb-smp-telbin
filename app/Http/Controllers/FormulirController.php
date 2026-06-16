<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FormulirController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');
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
            ...$this->formReferences(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $data = $this->validatedData($request, true, $pengguna->id_pengguna);
        $data['nisn'] = $pengguna->id_pengguna;
        $data['hp'] = $pengguna->telpon;
        $data['status'] = 'draft';
        $data['submitted_at'] = null;
        $data = array_merge($data, $this->calonSiswaFormulirData($pengguna->id_pengguna));
        $data = array_merge($data, $this->storeUploads($request));

        $formulir = Formulir::create($data);

        return redirect()->route('formulir.periksa', $formulir)->with('success', 'Data formulir berhasil disimpan. Periksa kembali sebelum mengirim final.');
    }

    public function riwayat(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');

        return view('formulir.riwayat', [
            'pengguna' => $pengguna,
            'formulirs' => Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->get(),
        ]);
    }

    public function edit(Request $request, Formulir $formulir): View|RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($pengguna->level !== 'Administrator' && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        $akunPendaftar = Pengguna::find($formulir->nisn) ?: $pengguna;

        return view('formulir.form', [
            'pengguna' => $pengguna,
            'formulir' => $formulir,
            'akunPendaftar' => $akunPendaftar,
            'calonSiswa' => CalonSiswa::find($formulir->nisn),
            ...$this->formReferences(),
        ]);
    }

    public function update(Request $request, Formulir $formulir): RedirectResponse
    {
        $pengguna = $request->attributes->get('pengguna');

        $this->authorizeFormulir($pengguna, $formulir);

        if ($pengguna->level !== 'Administrator' && $formulir->isSubmitted()) {
            return redirect()->route('formulir.riwayat')->with('warning', 'Formulir yang sudah dikirim final tidak dapat diedit.');
        }

        $data = $this->validatedData($request, false, $formulir->nisn);
        $akunPendaftar = Pengguna::find($formulir->nisn);

        if ($akunPendaftar) {
            $data['hp'] = $akunPendaftar->telpon;
        }

        $data = array_merge($data, $this->calonSiswaFormulirData($formulir->nisn));
        $formulir->update(array_merge($data, $this->storeUploads($request)));

        if ($pengguna->level === 'Administrator') {
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

        return view('formulir.cetak', compact('pengguna', 'formulir'));
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
            'nik' => ['required', 'string', 'max:30'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'agama' => ['required', 'string', 'max:50'],
            'alamat' => ['required', 'string'],
            'alamat_kabupaten' => ['required', 'string', 'max:100'],
            'alamat_kecamatan' => ['required', 'string', 'max:100'],
            'alamat_kelurahan' => ['required', 'string', 'max:100'],
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
            'program_keahlian_1' => ['required', 'string', 'max:100', 'different:program_keahlian_2'],
            'program_keahlian_2' => ['required', 'string', 'max:100', 'not_in:Teknik Komputer dan Jaringan (TKJ)'],
            'surat_keterangan_lulus' => [$requiredFileRule, 'file', 'mimes:pdf', 'max:1024'],
            'kartu_keluarga' => [$requiredFileRule, 'file', 'mimes:pdf', 'max:1024'],
            'foto_selfie' => [$requiredFileRule, 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]), [
            'program_keahlian_1.required' => 'Pilih program keahlian wajib diisi.',
            'program_keahlian_2.required' => 'Pilih program keahlian wajib diisi.',
            'program_keahlian_2.not_in' => 'Program keahlian B belum dapat memilih Teknik Komputer dan Jaringan (TKJ). Silakan pilih program keahlian lain.',
        ]);

        unset($data['surat_keterangan_lulus'], $data['kartu_keluarga'], $data['foto_selfie']);
        $data['alamat_ortu_sama_dengan_siswa'] = $parentAddressSame;

        if ($parentAddressSame) {
            $data['alamat_ortu_provinsi'] = 'Papua Barat';
            $data['alamat_ortu_kabupaten'] = $data['alamat_kabupaten'];
            $data['alamat_ortu_kecamatan'] = $data['alamat_kecamatan'];
            $data['alamat_ortu_kelurahan'] = $data['alamat_kelurahan'];
            $data['alamat_ortu'] = $data['alamat'];
        }

        return $data;
    }

    private function storeUploads(Request $request): array
    {
        $paths = [];
        $dir = public_path('uploads/dokumen');

        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        foreach (['surat_keterangan_lulus', 'kartu_keluarga', 'foto_selfie'] as $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $name = $request->session()->get('pengguna_id').'_'.time().'_'.$field.'.'.$file->extension();
            $file->move($dir, $name);
            $paths[$field] = 'uploads/dokumen/'.$name;
        }

        return $paths;
    }

    private function formReferences(): array
    {
        $kecamatan = DB::table('ref_kecamatan')
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get(['id', 'nama']);

        $kelurahan = DB::table('ref_kelurahan')
            ->join('ref_kecamatan', 'ref_kecamatan.id', '=', 'ref_kelurahan.kecamatan_id')
            ->orderBy('ref_kecamatan.urutan')
            ->orderBy('ref_kelurahan.urutan')
            ->orderBy('ref_kelurahan.nama')
            ->get([
                'ref_kelurahan.nama',
                'ref_kecamatan.nama as kecamatan',
            ]);

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

        return [
            'kecamatanOptions' => $kecamatan->pluck('nama')->all(),
            'kelurahanOptionsByKecamatan' => $kelurahan
                ->groupBy('kecamatan')
                ->map(fn ($items) => $items->pluck('nama')->values()->all())
                ->all(),
            'sekolahAsalOptions' => DB::table('ref_sekolah_asal')
                ->orderBy('urutan')
                ->orderBy('nama')
                ->pluck('nama')
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
        if ($pengguna->level !== 'Administrator' && $formulir->nisn !== $pengguna->id_pengguna) {
            abort(403);
        }
    }
}
