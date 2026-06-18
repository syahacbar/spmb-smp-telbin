<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('spmb-login', function (Request $request): array {
            $nisn = trim((string) $request->input('nisn'));
            $key = ($nisn !== '' ? $nisn : 'kosong').'|'.$request->ip();

            return [
                Limit::perMinute(5)
                    ->by($key)
                    ->response(fn (Request $request, array $headers) => $this->tooManyAttemptsResponse(
                        $request,
                        $headers,
                        'Terlalu banyak percobaan login. Silakan tunggu sebentar sebelum mencoba kembali.',
                    )),
                Limit::perMinute(20)
                    ->by($request->ip())
                    ->response(fn (Request $request, array $headers) => $this->tooManyAttemptsResponse(
                        $request,
                        $headers,
                        'Terlalu banyak percobaan login dari jaringan ini. Silakan tunggu sebentar.',
                    )),
            ];
        });

        RateLimiter::for('spmb-register-check', fn (Request $request) => Limit::perMinute(10)
            ->by($request->ip())
            ->response(fn (Request $request, array $headers) => $this->tooManyAttemptsResponse(
                $request,
                $headers,
                'Terlalu banyak pengecekan NISN. Silakan tunggu sebentar sebelum mencoba kembali.',
            )));

        RateLimiter::for('spmb-register', fn (Request $request) => Limit::perMinutes(10, 3)
            ->by($request->ip())
            ->response(fn (Request $request, array $headers) => $this->tooManyAttemptsResponse(
                $request,
                $headers,
                'Terlalu banyak percobaan pendaftaran. Silakan tunggu beberapa menit sebelum mencoba kembali.',
            )));

        RateLimiter::for('spmb-status', fn (Request $request) => Limit::perMinute(10)
            ->by($request->ip())
            ->response(fn (Request $request, array $headers) => $this->tooManyAttemptsResponse(
                $request,
                $headers,
                'Terlalu banyak pengecekan status. Silakan tunggu sebentar sebelum mencoba kembali.',
            )));
    }

    private function tooManyAttemptsResponse(Request $request, array $headers, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'ok' => false,
                'type' => 'warning',
                'message' => $message,
            ], 429, $headers);
        }

        return back()
            ->withErrors(['rate_limit' => $message])
            ->withInput($request->except(['password', 'password_confirmation', 'captcha_answer']))
            ->withHeaders($headers);
    }
}
