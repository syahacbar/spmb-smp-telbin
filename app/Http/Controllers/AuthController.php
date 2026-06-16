<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(Request $request): View
    {
        $this->generateLoginCaptcha($request);

        return view('auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'string'],
            'password' => ['required', 'string'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'captcha_answer.required' => 'Captcha wajib diisi.',
            'captcha_answer.integer' => 'Captcha harus berupa angka.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            $expected = (string) $request->session()->get('login_captcha_answer');
            $answer = trim((string) $request->input('captcha_answer'));

            if ($expected === '' || ! hash_equals($expected, $answer)) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha tidak sesuai.');
            }
        });

        if ($validator->fails()) {
            $this->generateLoginCaptcha($request);

            return back()->withErrors($validator)->onlyInput('nisn');
        }

        $credentials = $validator->validated();

        $pengguna = Pengguna::find($credentials['nisn']);

        if (! $pengguna || ! $this->passwordMatches($credentials['password'], $pengguna->password)) {
            $this->generateLoginCaptcha($request);

            return back()->withErrors(['nisn' => 'NISN atau password salah.'])->onlyInput('nisn');
        }

        if ($pengguna->level !== 'Administrator' && ! $pengguna->is_verified) {
            $this->generateLoginCaptcha($request);

            return back()
                ->withErrors(['nisn' => 'Akun anda belum diverifikasi oleh admin sekolah. Silakan cek email setelah admin melakukan verifikasi.'])
                ->onlyInput('nisn');
        }

        if (! str_starts_with($pengguna->password, '$2y$') && ! str_starts_with($pengguna->password, '$argon')) {
            $pengguna->update(['password' => Hash::make($credentials['password'])]);
        }

        $request->session()->regenerate();
        $request->session()->put('pengguna_id', $pengguna->id_pengguna);
        $request->session()->forget(['login_captcha_question', 'login_captcha_answer']);

        return redirect()->route('dashboard')->with('success', 'Login berhasil.');
    }

    public function register(Request $request): View
    {
        $this->generateRegisterCaptcha($request);

        return view('auth.register', [
            'panitiaWhatsapp' => $this->panitiaWhatsapp(),
            'panitiaWhatsappUrl' => $this->panitiaWhatsappUrl(),
        ]);
    }

    public function status(Request $request): View
    {
        $this->generateStatusCaptcha($request);

        return view('auth.status');
    }

    public function checkStatus(Request $request): RedirectResponse
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

            return back()->withErrors($validator)->onlyInput('nisn');
        }

        $nisn = $validator->validated()['nisn'];
        $result = Pengguna::whereKey($nisn)->exists()
            ? 'registered'
            : (CalonSiswa::whereKey($nisn)->exists() ? 'not_registered' : 'not_found');

        $this->generateStatusCaptcha($request);

        return back()
            ->with('status_result', $result)
            ->with('status_nisn', $nisn)
            ->onlyInput('nisn');
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'digits:10'],
            'email' => ['required', 'email', 'max:100', 'unique:tb_pengguna,email'],
            'no_wa' => ['required', 'regex:/^8[0-9]{8,11}$/'],
            'password' => ['required', 'confirmed', 'min:3'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
            'email.required' => 'Email aktif wajib diisi.',
            'email.email' => 'Format email belum sesuai. Gunakan email aktif yang benar.',
            'email.max' => 'Email maksimal berisi 100 karakter.',
            'email.unique' => 'Email tersebut telah digunakan pada sistem SPMB. Silakan gunakan email lain atau hubungi panitia SPMB.',
            'no_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali angka 8 dan berisi 9 sampai 12 digit setelah kode +62.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum sama dengan kata sandi.',
            'password.min' => 'Kata sandi minimal berisi 3 karakter.',
            'captcha_answer.required' => 'Captcha wajib diisi.',
            'captcha_answer.integer' => 'Captcha harus berupa angka.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if (! $validator->errors()->has('nisn')) {
                $nisn = trim((string) $request->input('nisn'));

                if (Pengguna::whereKey($nisn)->exists()) {
                    $validator->errors()->add('nisn', "NISN {$nisn} telah terdaftar pada sistem SPMB. Untuk informasi dan verifikasi kepemilikan akun, silakan menghubungi panitia SPMB.");
                } elseif (! CalonSiswa::whereKey($nisn)->exists()) {
                    $validator->errors()->add('nisn', "NISN {$nisn} tidak ditemukan pada database calon peserta didik. Silakan menghubungi panitia SPMB melalui WhatsApp.");
                }
            }

            $expected = (string) $request->session()->get('register_captcha_answer');
            $answer = trim((string) $request->input('captcha_answer'));

            if ($expected === '' || ! hash_equals($expected, $answer)) {
                $validator->errors()->add('captcha_answer', 'Jawaban captcha tidak sesuai.');
            }
        });

        if ($validator->fails()) {
            $this->generateRegisterCaptcha($request);

            return back()
                ->withErrors($validator)
                ->onlyInput('nisn', 'email', 'no_wa');
        }

        $data = $validator->validated();
        $calonSiswa = CalonSiswa::findOrFail($data['nisn']);

        Pengguna::create([
            'id_pengguna' => $data['nisn'],
            'nama_pengguna' => $calonSiswa->nama,
            'email' => $data['email'],
            'telpon' => '62'.$data['no_wa'],
            'username' => $data['nisn'],
            'password' => Hash::make($data['password']),
            'level' => 'User',
            'is_verified' => false,
            'verified_at' => null,
        ]);

        $request->session()->forget(['register_captcha_question', 'register_captcha_answer']);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil. Akun anda menunggu verifikasi admin sekolah.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('pengguna_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function passwordMatches(string $plain, string $stored): bool
    {
        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon')) {
            return Hash::check($plain, $stored);
        }

        return hash_equals($stored, $plain);
    }

    private function generateLoginCaptcha(Request $request): void
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);

        $request->session()->put('login_captcha_question', "{$firstNumber} + {$secondNumber}");
        $request->session()->put('login_captcha_answer', (string) ($firstNumber + $secondNumber));
    }

    private function generateRegisterCaptcha(Request $request): void
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);

        $request->session()->put('register_captcha_question', "{$firstNumber} + {$secondNumber}");
        $request->session()->put('register_captcha_answer', (string) ($firstNumber + $secondNumber));
    }

    private function generateStatusCaptcha(Request $request): void
    {
        $firstNumber = random_int(2, 9);
        $secondNumber = random_int(1, 9);

        $request->session()->put('status_captcha_question', "{$firstNumber} + {$secondNumber}");
        $request->session()->put('status_captcha_answer', (string) ($firstNumber + $secondNumber));
    }

    private function panitiaWhatsapp(): string
    {
        return (string) config('services.spmb.panitia_whatsapp');
    }

    private function panitiaWhatsappUrl(): string
    {
        $phone = preg_replace('/\D+/', '', $this->panitiaWhatsapp());

        return 'https://wa.me/'.$phone.'?text='.rawurlencode('Halo Panitia SPMB SMK Negeri 1 Bintuni, saya ingin konfirmasi NISN yang belum tersedia di sistem.');
    }
}
