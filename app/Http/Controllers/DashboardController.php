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
        $programs = [
            'Akuntansi dan Keuangan Lembaga (AKL)' => ['kuota' => 72, 'aliases' => ['Akuntansi dan Keuangan Lembaga']],
            'Teknik Kendaraan Ringan (TKR)' => ['kuota' => 36, 'aliases' => ['Teknik Kendaraan Ringan']],
            'Teknik Komputer dan Jaringan (TKJ)' => ['kuota' => 36, 'aliases' => ['Teknik Komputer dan Jaringan', 'Teknik Jaringan dan Telekomunikasi']],
            'Desain Komunikasi Visual (DKV)' => ['kuota' => 36, 'aliases' => ['Desain Komunikasi Visual']],
            'Teknik Sepeda Motor (TSM)' => ['kuota' => 36, 'aliases' => ['Teknik Sepeda Motor']],
        ];
        $programCounts = $pengguna->level === 'Administrator'
            ? $this->programCounts($programs)
            : collect();

        return view('dashboard', [
            'pengguna' => $pengguna,
            'totalPengguna' => Pengguna::where('level', 'User')->count(),
            'totalMenungguVerifikasi' => Pengguna::where('level', 'User')->where('is_verified', false)->count(),
            'totalTerverifikasi' => Pengguna::where('level', 'User')->where('is_verified', true)->count(),
            'totalFormulir' => Formulir::where('status', 'submitted')->count(),
            'totalDraft' => Formulir::where('status', 'draft')->count(),
            'programCounts' => $programCounts,
            'formulirSaya' => $pengguna->level === 'User'
                ? Formulir::where('nisn', $pengguna->id_pengguna)->latest('id')->first()
                : null,
        ]);
    }

    private function programCounts(array $programs)
    {
        $submitted = Formulir::where('status', 'submitted')
            ->get(['program_keahlian_1', 'program_keahlian_2']);

        return collect($programs)->map(function (array $config, string $program) use ($submitted): array {
            $kuota = (int) $config['kuota'];
            $aliases = $config['aliases'];
            $acceptedNames = collect([$program, ...$aliases])->map(fn (string $name) => $this->normalizeProgramName($name))->all();
            $minatA = $submitted->filter(fn (Formulir $formulir) => in_array($this->normalizeProgramName($formulir->program_keahlian_1), $acceptedNames, true))->count();
            $minatB = $submitted->filter(fn (Formulir $formulir) => in_array($this->normalizeProgramName($formulir->program_keahlian_2), $acceptedNames, true))->count();
            $total = $minatA + $minatB;

            return [
                'nama' => $program,
                'minat_a' => $minatA,
                'minat_b' => $minatB,
                'total' => $total,
                'kuota' => $kuota,
                'persen' => $kuota > 0 ? min(100, round(($minatA / $kuota) * 100)) : 0,
            ];
        })->values();
    }

    private function normalizeProgramName(?string $program): string
    {
        return trim(preg_replace('/\s*\([A-Z]+\)\s*$/', '', (string) $program));
    }
}
