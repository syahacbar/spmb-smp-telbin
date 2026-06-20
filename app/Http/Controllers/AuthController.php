<?php

namespace App\Http\Controllers;

use App\Models\CalonSiswa;
use App\Models\KontakPanitia;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function login(Request $request): View
    {
        $this->generateLoginCaptcha($request);

        return view('auth.login', [
            'panitiaWhatsapp' => $this->panitiaWhatsapp(),
            'panitiaWhatsappUrl' => $this->panitiaWhatsappUrl(),
        ]);
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

        $pengguna = Pengguna::query()
            ->whereKey($credentials['nisn'])
            ->orWhere('username', $credentials['nisn'])
            ->first();

        if (! $pengguna || ! $this->passwordMatches($credentials['password'], $pengguna->password)) {
            $this->generateLoginCaptcha($request);

            return back()->withErrors(['nisn' => 'NISN atau password salah.'])->onlyInput('nisn');
        }

        if ($pengguna->isCalonMurid() && $pengguna->is_active === false) {
            $this->generateLoginCaptcha($request);

            return back()
                ->withErrors(['nisn' => 'Akun anda sedang nonaktif. Silakan menghubungi panitia SPMB.'])
                ->onlyInput('nisn');
        }

        if ($pengguna->isCalonMurid() && ! $pengguna->is_verified) {
            $request->session()->regenerate();
            $request->session()->put('pengguna_id', $pengguna->id_pengguna);
            $request->session()->forget(['login_captcha_question', 'login_captcha_answer']);

            return redirect()->route('akun.status');
        }

        if ($pengguna->isCalonMurid() && ! $pengguna->verification_notice_seen_at) {
            $request->session()->regenerate();
            $request->session()->put('pengguna_id', $pengguna->id_pengguna);
            $request->session()->forget(['login_captcha_question', 'login_captcha_answer']);

            return redirect()->route('akun.status');
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

        try {
            $kecamatanOptions = DB::table('ref_kecamatan')->orderBy('urutan')->orderBy('nama')->get(['id', 'nama']);
            $kelurahanOptions = DB::table('ref_kelurahan')->orderBy('urutan')->orderBy('nama')->get(['id', 'kecamatan_id', 'nama']);
        } catch (\Throwable) {
            $kecamatanOptions = collect();
            $kelurahanOptions = collect();
        }

        return view('auth.register', [
            'panitiaWhatsapp' => $this->panitiaWhatsapp(),
            'panitiaWhatsappUrl' => $this->panitiaWhatsappUrl(),
            'kecamatanOptions' => $kecamatanOptions,
            'kelurahanOptions' => $kelurahanOptions,
            'calonSiswa' => old('nisn')
                ? CalonSiswa::active()->find(old('nisn'))
                : null,
        ]);
    }

    public function checkRegisterNisn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'digits:10'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'type' => 'error',
                'message' => $validator->errors()->first('nisn'),
            ], 422);
        }

        $nisn = $validator->validated()['nisn'];

        if (Pengguna::whereKey($nisn)->exists()) {
            return response()->json([
                'ok' => false,
                'type' => 'warning',
                'message' => "NISN {$nisn} sudah terdaftar pada sistem SPMB. Silakan login atau hubungi panitia SPMB.",
            ], 409);
        }

        $calonSiswa = CalonSiswa::active()->find($nisn);

        if (! $calonSiswa) {
            return response()->json([
                'ok' => false,
                'type' => 'warning',
                'message' => 'Tidak ditemukan pada whitelist calon peserta didik aktif. Silakan menghubungi panitia SPMB melalui WhatsApp.',
            ], 404);
        }

        return response()->json([
            'ok' => true,
            'type' => 'success',
            'message' => "NISN {$nisn} tersedia di database calon peserta didik. Silakan lanjut isi data akun.",
            'student' => [
                'nisn' => $calonSiswa->nisn,
                'nama' => $calonSiswa->nama,
                'tempat_lahir' => $calonSiswa->tempat_lahir,
                'tanggal_lahir' => $calonSiswa->tanggal_lahir?->translatedFormat('d F Y'),
                'asal_sekolah' => $calonSiswa->asal_sekolah,
            ],
        ]);
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'nisn' => ['required', 'digits:10'],
            'kecamatan_id' => ['required', 'integer', 'exists:ref_kecamatan,id'],
            'kelurahan_id' => ['required', 'integer', 'exists:ref_kelurahan,id'],
            'detail_alamat' => ['required', 'string', 'max:1000'],
            'kartu_keluarga' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:2048'],
            'no_wa' => ['required', 'regex:/^8[0-9]{8,11}$/'],
            'password' => ['required', 'confirmed', 'min:8'],
            'captcha_answer' => ['required', 'integer'],
        ], [
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.digits' => 'NISN harus berisi 10 digit angka.',
            'kecamatan_id.required' => 'Distrik/kecamatan domisili wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan/kampung domisili wajib dipilih.',
            'detail_alamat.required' => 'Detail alamat domisili wajib diisi.',
            'kartu_keluarga.required' => 'Kartu Keluarga wajib diunggah untuk verifikasi domisili.',
            'kartu_keluarga.mimes' => 'Kartu Keluarga harus berupa PDF atau gambar.',
            'kartu_keluarga.max' => 'Ukuran Kartu Keluarga maksimal 2 MB.',
            'no_wa.required' => 'Nomor WhatsApp aktif wajib diisi.',
            'no_wa.regex' => 'Nomor WhatsApp harus diawali angka 8 dan berisi 9 sampai 12 digit setelah kode +62.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum sama dengan kata sandi.',
            'password.min' => 'Kata sandi minimal berisi 8 karakter.',
            'captcha_answer.required' => 'Captcha wajib diisi.',
            'captcha_answer.integer' => 'Captcha harus berupa angka.',
        ]);

        $validator->after(function ($validator) use ($request): void {
            if (! $validator->errors()->has('nisn')) {
                $nisn = trim((string) $request->input('nisn'));

                if (Pengguna::whereKey($nisn)->exists()) {
                    $validator->errors()->add('nisn', "NISN {$nisn} telah terdaftar pada sistem SPMB. Untuk informasi dan verifikasi kepemilikan akun, silakan menghubungi panitia SPMB.");
                } elseif (! CalonSiswa::active()->whereKey($nisn)->exists()) {
                    $validator->errors()->add('nisn', 'Tidak ditemukan pada whitelist calon peserta didik aktif. Silakan menghubungi panitia SPMB melalui WhatsApp.');
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
                ->withInput($request->except(['password', 'password_confirmation', 'kartu_keluarga']));
        }

        $data = $validator->validated();
        $calonSiswa = CalonSiswa::active()->findOrFail($data['nisn']);

        $kelurahanValid = DB::table('ref_kelurahan')
            ->where('id', $data['kelurahan_id'])
            ->where('kecamatan_id', $data['kecamatan_id'])
            ->exists();

        if (! $kelurahanValid) {
            return back()
                ->withErrors(['kelurahan_id' => 'Kelurahan/kampung tidak sesuai dengan distrik/kecamatan yang dipilih.'])
                ->withInput($request->except(['password', 'password_confirmation', 'kartu_keluarga']));
        }

        $file = $request->file('kartu_keluarga');
        $kkPath = $file->storeAs(
            'registrasi/kk',
            Str::uuid().'.'.$file->extension(),
            'local',
        );

        if (! $kkPath) {
            return back()
                ->withErrors(['kartu_keluarga' => 'Kartu Keluarga gagal disimpan. Silakan unggah kembali.'])
                ->withInput($request->except(['password', 'password_confirmation', 'kartu_keluarga']));
        }

        try {
            DB::transaction(function () use ($data, $calonSiswa, $kkPath): void {
                $pengguna = Pengguna::create([
                    'id_pengguna' => $data['nisn'],
                    'nama_pengguna' => $calonSiswa->nama,
                    'alamat' => $data['detail_alamat'],
                    'email' => null,
                    'telpon' => '62'.$data['no_wa'],
                    'username' => $data['nisn'],
                    'password' => Hash::make($data['password']),
                    'level' => 'User',
                    'is_verified' => false,
                    'verified_at' => null,
                ]);

                $roleId = DB::table('roles')->where('kode', 'calon_murid')->value('id');
                $pengguna->roles()->attach($roleId);

                $periodeId = DB::table('tb_periode_spmb')->where('is_active', true)->value('id');
                $registrasi = $pengguna->registrasiAkun()->create([
                    'periode_id' => $periodeId,
                    'kabupaten' => 'Teluk Bintuni',
                    'kecamatan_id' => $data['kecamatan_id'],
                    'kelurahan_id' => $data['kelurahan_id'],
                    'detail_alamat' => $data['detail_alamat'],
                    'kartu_keluarga_path' => $kkPath,
                    'status' => 'menunggu_verifikasi',
                    'submitted_at' => now(),
                ]);

                DB::table('tb_riwayat_verifikasi_akun')->insert([
                    'registrasi_akun_id' => $registrasi->id,
                    'status_baru' => 'menunggu_verifikasi',
                    'catatan' => 'Registrasi akun diajukan oleh calon murid.',
                    'created_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($kkPath);
            throw $exception;
        }

        $request->session()->forget(['register_captcha_question', 'register_captcha_answer']);

        $request->session()->put('pengguna_id', $data['nisn']);

        return redirect()->route('akun.status')->with('success', 'Pendaftaran akun berhasil. Data anda menunggu verifikasi Dinas Pendidikan.');
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

    private function panitiaWhatsapp(): string
    {
        return KontakPanitia::primary()?->nomor_whatsapp
            ?: (string) config('services.spmb.panitia_whatsapp');
    }

    private function panitiaWhatsappUrl(): string
    {
        $phone = preg_replace('/\D+/', '', $this->panitiaWhatsapp());

        return 'https://wa.me/'.$phone.'?text='.rawurlencode('Halo Admin SPMB SMP Kabupaten Teluk Bintuni, saya ingin konfirmasi NISN yang belum tersedia di sistem.');
    }
}
