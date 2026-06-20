<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\EstimatedLocationGeocoder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomisiliSchoolController extends Controller
{
    public function __invoke(
        Request $request,
        Pengguna $pengguna,
        EstimatedLocationGeocoder $geocoder,
    ): JsonResponse {
        $actor = $request->attributes->get('pengguna');
        abort_unless(
            $actor?->isAdminDinas() || $actor?->id_pengguna === $pengguna->id_pengguna,
            403,
        );

        $pengguna->load('registrasiAkun');
        $registrasi = $pengguna->registrasiAkun;
        abort_unless($registrasi?->kelurahan_id, 422, 'Domisili terverifikasi belum tersedia.');

        $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
        $jalurId = DB::table('tb_jalur_pendaftaran')->where('kode', 'domisili')->value('id');
        abort_unless($periodeId && $jalurId, 422, 'Periode atau Jalur Domisili belum tersedia.');

        $domisili = DB::table('ref_kelurahan')
            ->join('ref_kecamatan', 'ref_kecamatan.id', '=', 'ref_kelurahan.kecamatan_id')
            ->where('ref_kelurahan.id', $registrasi->kelurahan_id)
            ->first([
                'ref_kelurahan.nama as kelurahan',
                'ref_kecamatan.nama as kecamatan',
            ]);
        abort_unless($domisili, 422, 'Referensi wilayah domisili tidak ditemukan.');

        $studentLocation = $geocoder->geocode(
            "{$domisili->kelurahan}, {$domisili->kecamatan}, Kabupaten Teluk Bintuni, Papua Barat, Indonesia",
        );

        $schools = DB::table('tb_sekolah')
            ->join('tb_zonasi_sekolah', function ($join) use ($periodeId, $registrasi): void {
                $join->on('tb_zonasi_sekolah.sekolah_id', '=', 'tb_sekolah.id')
                    ->where('tb_zonasi_sekolah.periode_id', $periodeId)
                    ->where('tb_zonasi_sekolah.kelurahan_id', $registrasi->kelurahan_id);
            })
            ->leftJoin('ref_kelurahan as sekolah_kelurahan', 'sekolah_kelurahan.id', '=', 'tb_sekolah.kelurahan_id')
            ->leftJoin('ref_kecamatan as sekolah_kecamatan', 'sekolah_kecamatan.id', '=', 'tb_sekolah.kecamatan_id')
            ->leftJoin('tb_kuota_sekolah_jalur as kuota', function ($join) use ($periodeId, $jalurId): void {
                $join->on('kuota.sekolah_id', '=', 'tb_sekolah.id')
                    ->where('kuota.periode_id', $periodeId)
                    ->where('kuota.jalur_id', $jalurId);
            })
            ->where('tb_sekolah.is_active', true)
            ->orderBy('tb_zonasi_sekolah.prioritas')
            ->orderBy('tb_sekolah.nama')
            ->get([
                'tb_sekolah.id',
                'tb_sekolah.nama',
                'tb_sekolah.alamat',
                'sekolah_kelurahan.nama as kelurahan',
                'sekolah_kecamatan.nama as kecamatan',
                'tb_sekolah.latitude',
                'tb_sekolah.longitude',
                DB::raw('coalesce(kuota.kuota, 0) as kuota'),
            ]);

        $applicantCounts = DB::table('tb_formulir')
            ->where('jalur_id', $jalurId)
            ->whereIn('sekolah_id', $schools->pluck('id'))
            ->where('status', 'submitted')
            ->groupBy('sekolah_id')
            ->selectRaw('sekolah_id, count(*) as total')
            ->pluck('total', 'sekolah_id');

        return response()->json([
            'domisili' => [
                'label' => "{$domisili->kelurahan} - {$domisili->kecamatan}",
                'latitude' => $studentLocation['latitude'] ?? null,
                'longitude' => $studentLocation['longitude'] ?? null,
                'radius_meters' => 1800,
            ],
            'schools' => $schools->map(function ($school) use ($geocoder, $applicantCounts): array {
                $address = collect([
                    $school->alamat,
                    $school->kelurahan,
                    $school->kecamatan,
                    'Kabupaten Teluk Bintuni',
                    'Papua Barat',
                    'Indonesia',
                ])->filter()->join(', ');
                $location = $school->latitude && $school->longitude
                    ? [
                        'latitude' => (float) $school->latitude,
                        'longitude' => (float) $school->longitude,
                    ]
                    : $geocoder->geocode($address);

                if ($location && (! $school->latitude || ! $school->longitude)) {
                    DB::table('tb_sekolah')->where('id', $school->id)->update([
                        'latitude' => $location['latitude'],
                        'longitude' => $location['longitude'],
                        'updated_at' => now(),
                    ]);
                }
                $applicants = (int) ($applicantCounts[$school->id] ?? 0);
                $quota = (int) $school->kuota;

                return [
                    'id' => (int) $school->id,
                    'nama' => $school->nama,
                    'alamat' => $school->alamat ?: collect([$school->kelurahan, $school->kecamatan])->filter()->join(', '),
                    'kuota' => $quota,
                    'pendaftar' => $applicants,
                    'sisa_kuota' => max(0, $quota - $applicants),
                    'latitude' => $location['latitude'] ?? null,
                    'longitude' => $location['longitude'] ?? null,
                ];
            })->values(),
        ]);
    }
}
