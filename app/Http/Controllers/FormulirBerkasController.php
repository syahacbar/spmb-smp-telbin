<?php

namespace App\Http\Controllers;

use App\Models\Formulir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormulirBerkasController extends Controller
{
    public function show(Request $request, Formulir $formulir, string $field): BinaryFileResponse|StreamedResponse
    {
        abort_unless(in_array($field, Formulir::DOCUMENT_FIELDS, true), 404);

        $pengguna = $request->attributes->get('pengguna');

        abort_unless(
            $pengguna->level === 'Administrator' || $formulir->nisn === $pengguna->id_pengguna,
            403,
        );

        $path = $formulir->{$field};

        abort_unless($path, 404);

        if (str_starts_with($path, 'dokumen/') && Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path, basename($path), [
                'Content-Disposition' => 'inline; filename="'.basename($path).'"',
            ]);
        }

        $legacyPath = $this->legacyPublicPath($path);

        abort_unless($legacyPath, 404);

        return response()->file($legacyPath, [
            'Content-Disposition' => 'inline; filename="'.basename($legacyPath).'"',
        ]);
    }

    private function legacyPublicPath(string $path): ?string
    {
        if (! str_starts_with($path, 'uploads/dokumen/')) {
            return null;
        }

        $basePath = realpath(public_path('uploads/dokumen'));
        $filePath = realpath(public_path($path));

        if (! $basePath || ! $filePath || ! str_starts_with($filePath, $basePath.DIRECTORY_SEPARATOR)) {
            return null;
        }

        return is_file($filePath) ? $filePath : null;
    }
}
