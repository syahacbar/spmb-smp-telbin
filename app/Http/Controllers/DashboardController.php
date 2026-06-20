<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use App\Models\RegistrasiAkun;
use Illuminate\Http\Request;
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

        return view('dashboard', [
            'pengguna' => $pengguna,
            'totalPengguna' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->count(),
            'totalMenungguVerifikasi' => (int) ($statusCounts['menunggu_verifikasi'] ?? 0),
            'totalTerverifikasi' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->where('is_verified', true)->count(),
            'totalFormulir' => Formulir::where('status', 'submitted')->count(),
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
        ]);
    }
}
