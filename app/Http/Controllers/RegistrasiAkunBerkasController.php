<?php

namespace App\Http\Controllers;

use App\Models\RegistrasiAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrasiAkunBerkasController extends Controller
{
    public function __invoke(Request $request, RegistrasiAkun $registrasi): StreamedResponse
    {
        $pengguna = $request->attributes->get('pengguna');
        abort_unless($pengguna?->isAdminDinas(), 403);
        abort_unless($registrasi->kartuKeluargaTersedia(), 404);

        return Storage::disk('local')->response(
            $registrasi->kartu_keluarga_path,
            basename($registrasi->kartu_keluarga_path),
            ['Content-Disposition' => 'inline; filename="'.basename($registrasi->kartu_keluarga_path).'"'],
        );
    }
}
