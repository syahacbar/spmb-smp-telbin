<?php

namespace Tests\Feature;

use App\Http\Controllers\FormulirBerkasController;
use App\Http\Controllers\FormulirController;
use App\Models\Formulir;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormulirBerkasTest extends TestCase
{
    public function test_upload_dokumen_disimpan_pada_disk_lokal_private(): void
    {
        Storage::fake('local');

        $request = Request::create('/', 'POST', [], [], [
            'kartu_keluarga' => UploadedFile::fake()->create('kk.pdf', 100, 'application/pdf'),
        ]);

        $method = new \ReflectionMethod(FormulirController::class, 'storeUploads');
        $paths = $method->invoke(app(FormulirController::class), $request);

        $this->assertArrayHasKey('kartu_keluarga', $paths);
        $this->assertStringStartsWith('dokumen/', $paths['kartu_keluarga']);
        Storage::disk('local')->assertExists($paths['kartu_keluarga']);
    }

    public function test_pemilik_dapat_membuka_dokumen_private(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('dokumen/kk.pdf', 'isi dokumen');

        $formulir = new Formulir([
            'nisn' => '1234567890',
            'kartu_keluarga' => 'dokumen/kk.pdf',
        ]);
        $request = Request::create('/');
        $request->attributes->set('pengguna', new Pengguna([
            'id_pengguna' => '1234567890',
            'level' => 'User',
        ]));

        $response = app(FormulirBerkasController::class)
            ->show($request, $formulir, 'kartu_keluarga');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('inline', (string) $response->headers->get('Content-Disposition'));
    }
}
