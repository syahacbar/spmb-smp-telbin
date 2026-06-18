<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $pengguna = $request->attributes->get('pengguna');
        return view('dashboard', [
            'pengguna' => $pengguna,
            'totalPengguna' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->count(),
            'totalMenungguVerifikasi' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->where('is_verified', false)->count(),
            'totalTerverifikasi' => Pengguna::whereHas('roles', fn ($query) => $query->where('kode', 'calon_murid'))->where('is_verified', true)->count(),
            'totalFormulir' => Formulir::where('status', 'submitted')->count(),
            'totalDraft' => Formulir::where('status', 'draft')->count(),
            'formulirSaya' => $pengguna->isCalonMurid()
                ? Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->first()
                : null,
        ]);
    }
}
