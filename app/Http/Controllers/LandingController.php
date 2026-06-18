<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\KontakPanitia;
use App\Models\PengaturanSpmb;
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
            'nisn' => ['required', 'digits:10'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
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
        $result = Pengguna::whereKey($nisn)->exists()
            ? 'registered'
            : $this->calonSiswaStatus($nisn);

        $this->generateStatusCaptcha($request);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'type' => $this->statusAlertType($result),
                'title' => $this->statusTitle($result, $nisn),
                'message' => $this->statusMessage($result),
                'captcha_question' => $request->session()->get('status_captcha_question'),
            ]);
        }

        return redirect('/#cek-status')
            ->with('status_result', $result)
            ->with('status_nisn', $nisn)
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
            'registered' => 'success',
            'not_registered' => 'info',
            default => 'warning',
        };
    }

    private function statusTitle(string $result, string $nisn): string
    {
        return match ($result) {
            'registered' => "NISN {$nisn} sudah memiliki akun SPMB.",
            'not_registered' => "NISN {$nisn} tersedia di database calon siswa.",
            'inactive' => "NISN {$nisn} tidak tersedia pada whitelist aktif tahun ini.",
            default => "NISN {$nisn} belum ditemukan.",
        };
    }

    private function statusMessage(string $result): string
    {
        return match ($result) {
            'registered' => 'Silakan login untuk melanjutkan proses pendaftaran.',
            'not_registered' => 'Silakan daftar akun SPMB melalui tombol daftar pada halaman ini.',
            'inactive' => 'Tidak ditemukan pada whitelist calon peserta didik aktif tahun ini. Silakan menghubungi panitia SPMB melalui WhatsApp.',
            default => 'Silakan hubungi panitia SPMB untuk pengecekan data calon peserta didik.',
        };
    }

    private function calonSiswaStatus(string $nisn): string
    {
        $tahun = (string) PengaturanSpmb::getValue('tahun_pendaftaran', date('Y'));

        if (CalonSiswa::activeForYear($tahun)->whereKey($nisn)->exists()) {
            return 'not_registered';
        }

        return CalonSiswa::whereKey($nisn)->exists() ? 'inactive' : 'not_found';
    }
}
