<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSpmbAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $penggunaId = $request->session()->get('pengguna_id');
        $pengguna = $penggunaId ? $this->findPengguna($penggunaId) : null;

        if (! $pengguna) {
            return redirect()->route('login');
        }

        if ($pengguna->level !== 'Administrator' && $pengguna->is_active === false) {
            return $this->logoutWithError(
                $request,
                'Akun anda sedang nonaktif. Silakan menghubungi panitia SPMB.',
            );
        }

        if ($pengguna->level !== 'Administrator' && ! $pengguna->is_verified) {
            return $this->logoutWithError(
                $request,
                'Akun anda belum diverifikasi oleh admin sekolah. Silakan menunggu proses verifikasi panitia SPMB.',
            );
        }

        $request->attributes->set('pengguna', $pengguna);

        return $next($request);
    }

    protected function findPengguna(string $penggunaId): ?Pengguna
    {
        return Pengguna::with('formulirTerbaru')->find($penggunaId);
    }

    private function logoutWithError(Request $request, string $message): Response
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['nisn' => $message]);
    }
}
