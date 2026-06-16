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
        $pengguna = $penggunaId ? Pengguna::with('formulirTerbaru')->find($penggunaId) : null;

        if (! $pengguna) {
            return redirect()->route('login');
        }

        $request->attributes->set('pengguna', $pengguna);

        return $next($request);
    }
}
