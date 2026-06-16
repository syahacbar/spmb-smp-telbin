<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

    public function verifikasiPengguna(Pengguna $pengguna): RedirectResponse
    {
        if ($pengguna->level === 'Administrator') {
            abort(403);
        }

        $pengguna->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);

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
}
