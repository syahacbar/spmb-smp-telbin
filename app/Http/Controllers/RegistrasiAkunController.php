<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class RegistrasiAkunController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $pengguna = $this->pengguna($request);

        if (! $pengguna) {
            return redirect()->route('login')->withErrors(['nisn' => 'Silakan login untuk melihat status akun.']);
        }

        return view('auth.status-akun', [
            'pengguna' => $pengguna,
            'registrasi' => $pengguna->registrasiAkun,
            'calonSiswa' => $pengguna->calonSiswa,
            'kecamatanOptions' => DB::table('ref_kecamatan')->orderBy('urutan')->orderBy('nama')->get(['id', 'nama']),
            'kelurahanOptions' => DB::table('ref_kelurahan')->orderBy('urutan')->orderBy('nama')->get(['id', 'kecamatan_id', 'nama']),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $pengguna = $this->pengguna($request);
        abort_unless($pengguna?->registrasiAkun?->status === 'perlu_perbaikan', 403);

        $data = $request->validate([
            'kecamatan_id' => ['required', 'integer', 'exists:ref_kecamatan,id'],
            'kelurahan_id' => ['required', 'integer', 'exists:ref_kelurahan,id'],
            'detail_alamat' => ['required', 'string', 'max:1000'],
            'no_wa' => ['required', 'regex:/^8[0-9]{8,11}$/'],
            'kartu_keluarga' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'],
        ]);

        abort_unless(
            DB::table('ref_kelurahan')
                ->where('id', $data['kelurahan_id'])
                ->where('kecamatan_id', $data['kecamatan_id'])
                ->exists(),
            422,
            'Kelurahan/kampung tidak sesuai dengan distrik.',
        );

        $registrasi = $pengguna->registrasiAkun;
        $oldPath = $registrasi->kartu_keluarga_path;
        $newPath = null;

        if ($request->hasFile('kartu_keluarga')) {
            $file = $request->file('kartu_keluarga');
            $newPath = $file->storeAs('registrasi/kk', Str::uuid().'.'.$file->extension(), 'local');

            if (! $newPath) {
                return back()->withErrors(['kartu_keluarga' => 'Kartu Keluarga gagal disimpan.']);
            }
        }

        if (! $newPath && ! $registrasi->kartuKeluargaTersedia()) {
            return back()->withErrors(['kartu_keluarga' => 'Kartu Keluarga wajib diunggah kembali.']);
        }

        try {
            DB::transaction(function () use ($pengguna, $registrasi, $data, $newPath): void {
                $pengguna->update([
                    'alamat' => $data['detail_alamat'],
                    'telpon' => '62'.$data['no_wa'],
                ]);

                $registrasi->update([
                    'kecamatan_id' => $data['kecamatan_id'],
                    'kelurahan_id' => $data['kelurahan_id'],
                    'detail_alamat' => $data['detail_alamat'],
                    'kartu_keluarga_path' => $newPath ?: $registrasi->kartu_keluarga_path,
                    'status' => 'menunggu_verifikasi',
                    'catatan_verifikasi' => null,
                    'submitted_at' => now(),
                    'verified_by' => null,
                ]);

                DB::table('tb_riwayat_verifikasi_akun')->insert([
                    'registrasi_akun_id' => $registrasi->id,
                    'status_sebelumnya' => 'perlu_perbaikan',
                    'status_baru' => 'menunggu_verifikasi',
                    'catatan' => 'Calon murid telah mengirim perbaikan data registrasi akun.',
                    'created_at' => now(),
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return back()->with('success', 'Perbaikan berhasil dikirim dan kembali menunggu verifikasi Dinas Pendidikan.');
    }

    private function pengguna(Request $request): ?Pengguna
    {
        $id = $request->session()->get('pengguna_id');

        return $id
            ? Pengguna::with(['calonSiswa', 'registrasiAkun'])->find($id)
            : null;
    }
}
