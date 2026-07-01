<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\KontakPanitia;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(Request $request): View
    {
        $this->generateStatusCaptcha($request);
        $kontakPanitia = KontakPanitia::primary();

        return view('landing', [
            'whatsapp' => $kontakPanitia?->nomor_whatsapp ?: (string) config('services.spmb.panitia_whatsapp', '6281111110002'),
            'whatsappLabel' => $kontakPanitia?->label ?: 'Panitia',
        ]);
    }

    public function checkStatus(Request $request): RedirectResponse|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'string', 'max:20'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.max' => 'NISN maksimal berisi 20 karakter.',
            'captcha_answer.required' => 'Captcha wajib diisi.',
            'captcha_answer.integer' => 'Captcha harus berupa angka.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            $expected = (string) $request->session()->get('status_captcha_answer');
            $answer = trim((string) $request->input('captcha_answer'));

            if ($expected === '' || ! hash_equals($expected, $answer)) {
                $validator->errors()->add('captcha_answer', 'Kode Captcha tidak sesuai. Silakan masukkan kembali kode keamanan yang benar.');
            }
        });

        if ($validator->fails()) {
            $this->generateStatusCaptcha($request);

            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'type' => 'error',
                    'title' => 'Pengecekan belum berhasil.',
                    'messages' => $validator->errors()->all(),
                    'captcha_question' => $request->session()->get('status_captcha_question'),
                ], 422);
            }

            return redirect('/#cek-status')
                ->withErrors($validator, 'status')
                ->withInput();
        }

        $nisn = $validator->validated()['nisn'];
        $pengguna = Pengguna::with('registrasiAkun')->find($nisn);
        $result = $pengguna
            ? ($pengguna->registrasiAkun?->status ?: 'registered')
            : $this->calonSiswaStatus($nisn);
        $note = $pengguna?->registrasiAkun?->catatan_verifikasi;

        $this->generateStatusCaptcha($request);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'type' => $this->statusAlertType($result),
                'title' => $this->statusTitle($result, $nisn),
                'message' => $this->statusMessage($result),
                'note' => $note,
                'action_url' => $result === 'terverifikasi' ? route('login') : null,
                'captcha_question' => $request->session()->get('status_captcha_question'),
            ]);
        }

        return redirect('/#cek-status')
            ->with('status_result', $result)
            ->with('status_nisn', $nisn)
            ->with('status_note', $note)
            ->withInput(['nisn' => $nisn]);
    }

    private function generateStatusCaptcha(Request $request): void
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);

        $request->session()->put('status_captcha_question', "{$firstNumber} + {$secondNumber}");
        $request->session()->put('status_captcha_answer', (string) ($firstNumber + $secondNumber));
    }

    private function statusAlertType(string $result): string
    {
        return match ($result) {
            'registered', 'terverifikasi' => 'success',
            'menunggu_verifikasi' => 'info',
            'perlu_perbaikan', 'ditolak' => 'warning',
            'not_registered' => 'info',
            default => 'warning',
        };
    }

    private function statusTitle(string $result, string $nisn): string
    {
        return match ($result) {
            'registered' => "NISN {$nisn} sudah memiliki akun SPMB.",
            'menunggu_verifikasi' => "Akun NISN {$nisn} sedang menunggu verifikasi.",
            'terverifikasi' => "Akun NISN {$nisn} sudah terverifikasi.",
            'perlu_perbaikan' => "Registrasi NISN {$nisn} perlu diperbaiki.",
            'ditolak' => "Registrasi NISN {$nisn} ditolak.",
            'not_registered' => "NISN {$nisn} tersedia di database calon siswa.",
            'inactive' => "NISN {$nisn} tidak tersedia pada whitelist aktif.",
            default => "NISN {$nisn} belum ditemukan.",
        };
    }

    private function statusMessage(string $result): string
    {
        return match ($result) {
            'registered' => 'Silakan login untuk melanjutkan proses pendaftaran.',
            'menunggu_verifikasi' => 'Data alamat dan Kartu Keluarga sedang diperiksa oleh Dinas Pendidikan.',
            'terverifikasi' => 'Akun sudah aktif. Silakan login untuk melanjutkan pendaftaran.',
            'perlu_perbaikan' => 'Silakan login menggunakan NISN dan kata sandi untuk membaca catatan serta memperbaiki data.',
            'ditolak' => 'Silakan perhatikan catatan Dinas Pendidikan atau hubungi admin untuk informasi lebih lanjut.',
            'not_registered' => 'Silakan daftar akun SPMB melalui tombol daftar pada halaman ini.',
            'inactive' => 'Tidak ditemukan pada whitelist calon peserta didik aktif. Silakan menghubungi panitia SPMB melalui WhatsApp.',
            default => 'Silakan hubungi panitia SPMB untuk pengecekan data calon peserta didik.',
        };
    }

    private function calonSiswaStatus(string $nisn): string
    {
        if (CalonSiswa::active()->whereKey($nisn)->exists()) {
            return 'not_registered';
        }

        return CalonSiswa::whereKey($nisn)->exists() ? 'inactive' : 'not_found';
    }
}
