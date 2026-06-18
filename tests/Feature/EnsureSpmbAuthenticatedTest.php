<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureSpmbAuthenticated;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EnsureSpmbAuthenticatedTest extends TestCase
{
    public function test_sesi_siswa_ditolak_setelah_akun_dinonaktifkan(): void
    {
        $pengguna = $this->buatPengguna([
            'is_active' => false,
            'is_verified' => true,
        ]);

        [$response, $session] = $this->jalankanMiddleware($pengguna);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'nisn' => 'Akun anda sedang nonaktif. Silakan menghubungi panitia SPMB.',
            ]);
        $this->assertFalse($session->has('pengguna_id'));
    }

    public function test_sesi_siswa_ditolak_jika_akun_tidak_lagi_terverifikasi(): void
    {
        $pengguna = $this->buatPengguna([
            'is_active' => true,
            'is_verified' => false,
        ]);

        [$response, $session] = $this->jalankanMiddleware($pengguna);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'nisn' => 'Akun anda belum diverifikasi oleh admin sekolah. Silakan menunggu proses verifikasi panitia SPMB.',
            ]);
        $this->assertFalse($session->has('pengguna_id'));
    }

    public function test_status_aktif_dan_verifikasi_tidak_memblokir_administrator(): void
    {
        $pengguna = $this->buatPengguna([
            'level' => 'Administrator',
            'is_active' => false,
            'is_verified' => false,
        ]);

        [$response, $session] = $this->jalankanMiddleware($pengguna);

        $response
            ->assertOk()
            ->assertSee('diizinkan');
        $this->assertSame($pengguna->id_pengguna, $session->get('pengguna_id'));
    }

    private function buatPengguna(array $attributes = []): Pengguna
    {
        return new Pengguna(array_merge([
            'id_pengguna' => '1234567890',
            'nama_pengguna' => 'Pengguna Uji',
            'telpon' => '628123456789',
            'username' => '1234567890',
            'password' => 'password',
            'level' => 'User',
            'is_active' => true,
            'is_verified' => true,
        ], $attributes));
    }

    private function jalankanMiddleware(Pengguna $pengguna): array
    {
        $session = app('session')->driver();
        $session->flush();
        $session->start();
        $session->put('pengguna_id', $pengguna->id_pengguna);

        $request = Request::create('/test/spmb-auth');
        $request->setLaravelSession($session);

        $middleware = new class($pengguna) extends EnsureSpmbAuthenticated
        {
            public function __construct(private readonly Pengguna $pengguna) {}

            protected function findPengguna(string $penggunaId): ?Pengguna
            {
                return $this->pengguna;
            }
        };

        $response = $middleware->handle(
            $request,
            fn () => response('diizinkan'),
        );

        return [TestResponse::fromBaseResponse($response), $session];
    }
}
