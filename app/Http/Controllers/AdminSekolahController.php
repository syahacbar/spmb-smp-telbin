<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\JalurPendaftaran;
use App\Models\Sekolah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSekolahController extends Controller
{
    /**
     * Get the single school managed by the currently-authenticated admin sekolah.
     * Aborts with 403 if the user isn't an admin_sekolah or has no school.
     */
    private function getSekolah(Request $request): Sekolah
    {
        $pengguna = $request->attributes->get('pengguna');

        abort_unless($pengguna?->isAdminSekolah(), 403);

        $sekolah = $pengguna->sekolah()->first();

        abort_unless($sekolah, 403, 'Akun ini belum ditetapkan ke sekolah manapun.');

        return $sekolah;
    }

    // ─────────────────────────────────────────────
    // PROFIL SEKOLAH
    // ─────────────────────────────────────────────

    public function profil(Request $request): View
    {
        $sekolah = $this->getSekolah($request);

        return view('admin.sekolah.profil', [
            'pengguna' => $request->attributes->get('pengguna'),
            'sekolah'  => $sekolah,
            'kecamatan' => DB::table('ref_kecamatan')->where('id', $sekolah->kecamatan_id)->value('nama'),
            'kelurahan' => DB::table('ref_kelurahan')->where('id', $sekolah->kelurahan_id)->value('nama'),
        ]);
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $sekolah = $this->getSekolah($request);

        $data = $request->validate([
            'kepala_sekolah' => ['nullable', 'string', 'max:150'],
            'telepon'        => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:100'],
            'alamat'         => ['nullable', 'string', 'max:1000'],
            'deskripsi'      => ['nullable', 'string', 'max:2000'],
            'foto'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'foto.image'  => 'Foto sekolah harus berupa gambar.',
            'foto.max'    => 'Ukuran foto sekolah maksimal 2 MB.',
        ]);

        unset($data['foto']);

        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($sekolah->foto && str_starts_with($sekolah->foto, 'sekolah/foto/')) {
                Storage::disk('public')->delete($sekolah->foto);
            }

            $file = $request->file('foto');
            $name = Str::uuid().'.'.$file->extension();
            $path = $file->storeAs('sekolah/foto', $name, 'public');

            if (! $path) {
                return back()->with('warning', 'Foto sekolah gagal disimpan. Silakan coba kembali.');
            }

            $data['foto'] = $path;
        }

        $sekolah->update($data);

        return back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }

    public function destroyFoto(Request $request): RedirectResponse
    {
        $sekolah = $this->getSekolah($request);

        if ($sekolah->foto && str_starts_with($sekolah->foto, 'sekolah/foto/')) {
            Storage::disk('public')->delete($sekolah->foto);
        }

        $sekolah->update(['foto' => null]);

        return back()->with('success', 'Foto sekolah berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // KUOTA PER JALUR
    // ─────────────────────────────────────────────

    public function kuota(Request $request): View
    {
        $sekolah  = $this->getSekolah($request);
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');

        $jalurs = JalurPendaftaran::where('is_active', true)->orderBy('id')->get();

        // Current kuotas keyed by jalur_id
        $kuotas = DB::table('tb_kuota_sekolah_jalur')
            ->where('periode_id', $periodeId)
            ->where('sekolah_id', $sekolah->id)
            ->pluck('kuota', 'jalur_id');

        // Pendaftar count per jalur
        $pendaftarPerJalur = Formulir::where('sekolah_id', $sekolah->id)
            ->where('status', 'submitted')
            ->select('jalur_id')
            ->selectRaw('count(*) as total')
            ->groupBy('jalur_id')
            ->pluck('total', 'jalur_id');

        return view('admin.sekolah.kuota', [
            'pengguna'         => $request->attributes->get('pengguna'),
            'sekolah'          => $sekolah,
            'jalurs'           => $jalurs,
            'kuotas'           => $kuotas,
            'pendaftarPerJalur' => $pendaftarPerJalur,
            'periodeId'        => $periodeId,
        ]);
    }

    public function updateKuota(Request $request): RedirectResponse
    {
        $sekolah  = $this->getSekolah($request);
        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');

        abort_unless($periodeId, 422, 'Periode SPMB aktif belum tersedia.');

        $data = $request->validate([
            'kuota'          => ['required', 'array'],
            'kuota.*'        => ['required', 'integer', 'min:0', 'max:9999'],
        ], [
            'kuota.*.min'  => 'Kuota tidak boleh negatif.',
            'kuota.*.max'  => 'Kuota maksimal 9999.',
        ]);

        DB::transaction(function () use ($sekolah, $periodeId, $data): void {
            foreach ($data['kuota'] as $jalurId => $kuota) {
                DB::table('tb_kuota_sekolah_jalur')->updateOrInsert(
                    [
                        'periode_id' => $periodeId,
                        'sekolah_id' => $sekolah->id,
                        'jalur_id'   => (int) $jalurId,
                    ],
                    [
                        'kuota'      => (int) $kuota,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            }
        });

        return back()->with('success', 'Kuota penerimaan per jalur berhasil disimpan.');
    }

    // ─────────────────────────────────────────────
    // DATA PENDAFTAR
    // ─────────────────────────────────────────────

    public function pendaftar(Request $request): View
    {
        $sekolah = $this->getSekolah($request);
        $jalurFilter = (string) $request->query('jalur', '');

        $query = Formulir::with(['jalur', 'pengguna.calonSiswa'])
            ->where('sekolah_id', $sekolah->id)
            ->where('status', 'submitted');

        if ($jalurFilter) {
            $query->whereHas('jalur', fn ($q) => $q->where('kode', $jalurFilter));
        }

        $formulirs = $query->latest('submitted_at')->get();

        $jalurs = JalurPendaftaran::where('is_active', true)->orderBy('id')->get();

        // Per-jalur counts for the summary bar
        $countPerJalur = Formulir::where('sekolah_id', $sekolah->id)
            ->where('status', 'submitted')
            ->select('jalur_id')
            ->selectRaw('count(*) as total')
            ->groupBy('jalur_id')
            ->pluck('total', 'jalur_id');

        // Rank for prestasi (across all jalur prestasi applicants to this school)
        $prestasiRanks = $formulirs
            ->filter(fn ($item) => $item->jalur?->kode === 'prestasi')
            ->sortByDesc(function ($item) {
                $siswa = $item->pengguna?->calonSiswa;
                return $siswa && $siswa->nilai_tka_matematika !== null && $siswa->nilai_tka_bahasa_indonesia !== null
                    ? ((float) $siswa->nilai_tka_matematika + (float) $siswa->nilai_tka_bahasa_indonesia) / 2
                    : -1;
            })
            ->values()
            ->mapWithKeys(fn ($item, $index) => [$item->id => $index + 1]);

        return view('admin.sekolah.pendaftar', [
            'pengguna'      => $request->attributes->get('pengguna'),
            'sekolah'       => $sekolah,
            'formulirs'     => $formulirs,
            'jalurs'        => $jalurs,
            'jalurFilter'   => $jalurFilter,
            'countPerJalur' => $countPerJalur,
            'prestasiRanks' => $prestasiRanks,
        ]);
    }
}
