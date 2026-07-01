<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use App\Models\RegistrasiAkun;
use App\Models\JalurPendaftaran;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');
        $statusCounts = RegistrasiAkun::query()
            ->select('status')
            ->selectRaw('count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $schoolIds = $pengguna->isAdminSekolah()
            ? $pengguna->sekolah()->pluck('tb_sekolah.id')
            : collect();

        // Initialize variables for view
        $jalurStats = collect();
        $asalSekolahStats = collect();
        $sekolahStats = collect();
        $jalurs = collect();
        $totalPerJalur = [];
        $grandTotal = 0;

        if ($pengguna->isAdminSekolah() && $schoolIds->isNotEmpty()) {
            $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
            $activeJalurs = JalurPendaftaran::where('is_active', true)->orderBy('id')->get();

            $pendaftarJalur = Formulir::whereIn('sekolah_id', $schoolIds)
                ->whereIn('status', ['submitted', 'diterima', 'ditolak'])
                ->select('jalur_id')
                ->selectRaw('count(*) as total')
                ->groupBy('jalur_id')
                ->pluck('total', 'jalur_id');

            $kuotas = DB::table('tb_kuota_sekolah_jalur')
                ->where('periode_id', $periodeId)
                ->whereIn('sekolah_id', $schoolIds)
                ->select('jalur_id')
                ->selectRaw('sum(kuota) as total_kuota')
                ->groupBy('jalur_id')
                ->pluck('total_kuota', 'jalur_id');

            $jalurStats = $activeJalurs->map(function ($jalur) use ($pendaftarJalur, $kuotas) {
                $pendaftar = (int) ($pendaftarJalur[$jalur->id] ?? 0);
                $kuota = (int) ($kuotas[$jalur->id] ?? 0);
                return [
                    'id' => $jalur->id,
                    'nama' => $jalur->nama,
                    'kode' => $jalur->kode,
                    'pendaftar' => $pendaftar,
                    'kuota' => $kuota,
                    'keterisian' => $kuota > 0 ? round(($pendaftar / $kuota) * 100, 1) : 0,
                ];
            });

            $asalSekolahStats = Formulir::whereIn('sekolah_id', $schoolIds)
                ->select('asal_sekolah')
                ->selectRaw("count(case when status in ('submitted', 'diterima', 'ditolak') then 1 end) as total_final")
                ->selectRaw("count(case when status = 'draft' then 1 end) as total_draft")
                ->selectRaw("count(*) as total")
                ->groupBy('asal_sekolah')
                ->orderByDesc('total_final')
                ->orderByDesc('total')
                ->get();
        }

        if ($pengguna->isAdminDinas()) {
            $jalurs = JalurPendaftaran::where('is_active', true)->orderBy('id')->get();
            $sekolahs = Sekolah::where('is_active', true)->orderBy('nama')->get();
            $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');

            $pendaftarSekolahJalur = Formulir::whereIn('status', ['submitted', 'diterima', 'ditolak'])
                ->select('sekolah_id', 'jalur_id')
                ->selectRaw('count(*) as total')
                ->groupBy('sekolah_id', 'jalur_id')
                ->get()
                ->groupBy('sekolah_id')
                ->map(fn($items) => $items->pluck('total', 'jalur_id'));

            $kuotaSekolahJalur = DB::table('tb_kuota_sekolah_jalur')
                ->where('periode_id', $periodeId)
                ->get()
                ->groupBy('sekolah_id')
                ->map(fn($items) => $items->pluck('kuota', 'jalur_id'));

            $sekolahStats = $sekolahs->map(function ($sekolah) use ($jalurs, $pendaftarSekolahJalur, $kuotaSekolahJalur, &$totalPerJalur, &$grandTotal) {
                $counts = $pendaftarSekolahJalur[$sekolah->id] ?? collect();
                $kuotas = $kuotaSekolahJalur[$sekolah->id] ?? collect();
                $totalPendaftar = 0;
                $pendaftarPerJalur = [];
                $kuotaPerJalur = [];

                foreach ($jalurs as $jalur) {
                    $count = (int) ($counts[$jalur->id] ?? 0);
                    $kuota = (int) ($kuotas[$jalur->id] ?? 0);
                    $pendaftarPerJalur[$jalur->id] = $count;
                    $kuotaPerJalur[$jalur->id] = $kuota;
                    $totalPendaftar += $count;

                    $totalPerJalur[$jalur->id] = ($totalPerJalur[$jalur->id] ?? 0) + $count;
                }

                $grandTotal += $totalPendaftar;

                return [
                    'npsn' => $sekolah->npsn,
                    'nama' => $sekolah->nama,
                    'pendaftar_per_jalur' => $pendaftarPerJalur,
                    'kuota_per_jalur' => $kuotaPerJalur,
                    'total_pendaftar' => $totalPendaftar,
                ];
            });
        }

        return view('dashboard', [
            'pengguna' => $pengguna,
            'totalPengguna' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->count(),
            'totalMenungguVerifikasi' => (int) ($statusCounts['menunggu_verifikasi'] ?? 0),
            'totalTerverifikasi' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->where('is_verified', true)->count(),
            'totalFormulir' => Formulir::whereIn('status', ['submitted', 'diterima', 'ditolak'])->count(),
            'totalDraft' => Formulir::where('status', 'draft')->count(),
            'statusCounts' => $statusCounts,
            'antreanVerifikasi' => $pengguna->isAdminDinas()
                ? RegistrasiAkun::with(['pengguna.calonSiswa'])
                    ->where('status', 'menunggu_verifikasi')
                    ->oldest('submitted_at')
                    ->limit(8)
                    ->get()
                : collect(),
            'sekolahAdmin' => $pengguna->isAdminSekolah()
                ? $pengguna->sekolah()->orderBy('nama')->get()
                : collect(),
            'pendaftarSekolah' => $pengguna->isAdminSekolah()
                ? Formulir::with(['jalur', 'pengguna.calonSiswa'])
                    ->whereIn('sekolah_id', $schoolIds)
                    ->latest('submitted_at')
                    ->get()
                : collect(),
            'formulirSaya' => $pengguna->isCalonMurid()
                ? Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->first()
                : null,
            'jalurStats' => $jalurStats,
            'asalSekolahStats' => $asalSekolahStats,
            'sekolahStats' => $sekolahStats,
            'jalurs' => $jalurs,
            'totalPerJalur' => $totalPerJalur,
            'grandTotal' => $grandTotal,
        ]);
    }
}
