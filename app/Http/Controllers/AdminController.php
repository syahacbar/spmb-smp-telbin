<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class AdminController extends Controller
{
    public function pendaftar(Request $request): View
    {
        return view('admin.pendaftar', [
            'pengguna' => $request->attributes->get('pengguna'),
            'formulirs' => Formulir::where('status', 'submitted')->latest('id')->get(),
        ]);
    }

    public function pengguna(Request $request): View
    {
        return view('admin.pengguna', [
            'pengguna' => $request->attributes->get('pengguna'),
            'users' => Pengguna::with('calonSiswa')->where('level', 'User')->orderBy('id_pengguna')->get(),
        ]);
    }

    public function pengaturan(Request $request): View
    {
        return view('admin.pengaturan', [
            'pengguna' => $request->attributes->get('pengguna'),
        ]);
    }

    public function storePengguna(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'digits:10', 'unique:tb_pengguna,id_pengguna'],
            'no_wa' => ['required', 'regex:/^8[0-9]{8,11}$/'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
            'nisn.unique' => 'NISN tersebut sudah memiliki akun.',
            'no_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali angka 8 dan berisi 9 sampai 12 digit setelah kode +62.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $pengguna = Pengguna::create([
            'id_pengguna' => $data['nisn'],
            'nama_pengguna' => '',
            'email' => null,
            'telpon' => '62'.$data['no_wa'],
            'username' => $data['nisn'],
            'password' => Hash::make('siswa123'),
            'level' => 'User',
            'is_verified' => true,
            'is_active' => true,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.pengguna.formulir.create', $pengguna)
            ->with('success', 'Akun siswa berhasil dibuat dengan password default siswa123. Silakan lanjut isi biodata pendaftaran.');
    }

    public function verifikasiPengguna(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }

        $pengguna->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        if (! $pengguna->email) {
            return back()->with('success', 'Akun berhasil diverifikasi. Email notifikasi belum dikirim karena siswa belum mengisi email.');
        }

        try {
            Mail::raw(
                "Akun SPMB anda dengan NISN {$pengguna->id_pengguna} sudah diverifikasi. Silakan login untuk melanjutkan pengisian formulir pendaftaran.",
                function ($message) use ($pengguna): void {
                    $message->to($pengguna->email)
                        ->subject('Akun SPMB Sudah Diverifikasi');
                },
            );
        } catch (Throwable) {
            return back()->with('warning', 'Akun berhasil diverifikasi, tetapi email notifikasi belum terkirim. Periksa konfigurasi email aplikasi.');
        }

        return back()->with('success', 'Akun berhasil diverifikasi dan email notifikasi telah dikirim.');
    }

    public function togglePenggunaAktif(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->update(['is_active' => ! $pengguna->is_active]);

        return back()->with('success', $pengguna->is_active ? 'User berhasil diaktifkan.' : 'User berhasil dinonaktifkan.');
    }

    public function resetPasswordPengguna(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->update(['password' => Hash::make('siswa123')]);

        return back()->with('success', 'Password user berhasil direset ke default siswa123.');
    }

    public function destroyPengguna(Pengguna $pengguna): RedirectResponse
    {
        $this->guardUserAccount($pengguna);

        $pengguna->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    private function guardUserAccount(Pengguna $pengguna): void
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }
    }
}
