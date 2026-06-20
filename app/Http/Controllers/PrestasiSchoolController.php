<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\PrestasiOpportunityCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrestasiSchoolController extends Controller
{
    public function __invoke(
        Request $request,
        Pengguna $pengguna,
        PrestasiOpportunityCalculator $calculator,
    ): JsonResponse {
        $actor = $request->attributes->get('pengguna');
        abort_unless(
            $actor?->isAdminDinas() || $actor?->id_pengguna === $pengguna->id_pengguna,
            403,
        );

        $pengguna->load('calonSiswa');
        $student = $pengguna->calonSiswa;
        $matematika = $student?->nilai_tka_matematika !== null
            ? (float) $student->nilai_tka_matematika
            : null;
        $bahasaIndonesia = $student?->nilai_tka_bahasa_indonesia !== null
            ? (float) $student->nilai_tka_bahasa_indonesia
            : null;
        $studentScore = $calculator->score($matematika, $bahasaIndonesia);
        abort_unless($studentScore !== null, 422, 'Nilai TKA siswa belum lengkap pada database Dinas.');

        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        $jalurId = DB::table('tb_jalur_pendaftaran')->where('kode', 'prestasi')->value('id');
        abort_unless($periodeId && $jalurId, 422, 'Periode atau Jalur Prestasi belum tersedia.');

        $submittedApplicants = DB::table('tb_formulir')
            ->join('tb_calon_siswa', 'tb_calon_siswa.nisn', '=', 'tb_formulir.nisn')
            ->where('tb_formulir.jalur_id', $jalurId)
            ->where('tb_formulir.status', 'submitted')
            ->where('tb_formulir.nisn', '!=', $pengguna->id_pengguna)
            ->whereNotNull('tb_calon_siswa.nilai_tka_matematika')
            ->whereNotNull('tb_calon_siswa.nilai_tka_bahasa_indonesia')
            ->get([
                'tb_formulir.sekolah_id',
                'tb_calon_siswa.nilai_tka_matematika',
                'tb_calon_siswa.nilai_tka_bahasa_indonesia',
            ])
            ->map(function ($applicant) use ($calculator): object {
                $applicant->score = $calculator->score(
                    (float) $applicant->nilai_tka_matematika,
                    (float) $applicant->nilai_tka_bahasa_indonesia,
                );

                return $applicant;
            });

        $ranking = $calculator->rank($studentScore, $submittedApplicants->pluck('score'));

        $schools = DB::table('tb_sekolah')
            ->leftJoin('tb_kuota_sekolah_jalur as kuota_prestasi', function ($join) use ($periodeId, $jalurId): void {
                $join->on('kuota_prestasi.sekolah_id', '=', 'tb_sekolah.id')
                    ->where('kuota_prestasi.periode_id', $periodeId)
                    ->where('kuota_prestasi.jalur_id', $jalurId);
            })
            ->where('tb_sekolah.is_active', true)
            ->orderBy('tb_sekolah.nama')
            ->get([
                'tb_sekolah.id',
                'tb_sekolah.nama',
                'tb_sekolah.alamat',
                DB::raw('coalesce(kuota_prestasi.kuota, 0) as kuota'),
            ]);

        $totalQuotas = DB::table('tb_kuota_sekolah_jalur')
            ->where('periode_id', $periodeId)
            ->whereIn('sekolah_id', $schools->pluck('id'))
            ->groupBy('sekolah_id')
            ->selectRaw('sekolah_id, sum(kuota) as total')
            ->pluck('total', 'sekolah_id');

        return response()->json([
            'student' => [
                'matematika' => $matematika,
                'bahasa_indonesia' => $bahasaIndonesia,
                'rata_rata' => $studentScore,
                'peringkat' => $ranking['rank'],
                'total_peserta' => $ranking['total'],
            ],
            'schools' => $schools->map(function ($school) use (
                $submittedApplicants,
                $totalQuotas,
                $calculator,
                $studentScore,
            ): array {
                $scores = $submittedApplicants
                    ->where('sekolah_id', $school->id)
                    ->pluck('score');
                $quota = (int) $school->kuota;
                $applicants = $scores->count();
                $cutoff = $calculator->cutoff($scores, $quota);
                $opportunity = $calculator->estimate($studentScore, $cutoff, $quota, $applicants);
                $totalQuota = (int) ($totalQuotas[$school->id] ?? 0);

                return [
                    'id' => (int) $school->id,
                    'nama' => $school->nama,
                    'alamat' => $school->alamat ?: 'Alamat sekolah belum tersedia',
                    'kuota' => $quota,
                    'kuota_persen' => $totalQuota > 0 ? round(($quota / $totalQuota) * 100, 1) : 0,
                    'pendaftar' => $applicants,
                    'cutoff' => $cutoff,
                    'peluang' => $opportunity,
                ];
            })->values(),
            'disclaimer' => 'Peringkat, cut-off, dan peluang bersifat sementara serta dapat berubah mengikuti pendaftar yang masuk.',
        ]);
    }
}
