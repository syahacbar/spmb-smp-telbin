<?php

namespace Tests\Feature;

use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    public function test_login_dibatasi_setelah_lima_percobaan_untuk_nisn_dan_ip_yang_sama(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.11'])
                ->post('/login', ['nisn' => '1234567890'])
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.11'])
            ->post('/login', ['nisn' => '1234567890'])
            ->assertRedirect()
            ->assertSessionHasErrors([
                'rate_limit' => 'Terlalu banyak percobaan login. Silakan tunggu sebentar sebelum mencoba kembali.',
            ])
            ->assertHeader('Retry-After');
    }

    public function test_pengecekan_nisn_dibatasi_setelah_sepuluh_permintaan(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.12'])
                ->postJson('/daftar/cek-nisn', ['nisn' => '123'])
                ->assertStatus(422);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.12'])
            ->postJson('/daftar/cek-nisn', ['nisn' => '123'])
            ->assertStatus(429)
            ->assertJsonPath('type', 'warning')
            ->assertHeader('Retry-After');
    }

    public function test_registrasi_dibatasi_setelah_tiga_percobaan_dalam_sepuluh_menit(): void
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.13'])
                ->post('/daftar')
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.13'])
            ->post('/daftar')
            ->assertRedirect()
            ->assertSessionHasErrors([
                'rate_limit' => 'Terlalu banyak percobaan pendaftaran. Silakan tunggu beberapa menit sebelum mencoba kembali.',
            ])
            ->assertHeader('Retry-After');
    }

    public function test_cek_status_dibatasi_setelah_sepuluh_permintaan(): void
    {
        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.14'])
                ->postJson('/cek-status')
                ->assertStatus(422);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.14'])
            ->postJson('/cek-status')
            ->assertStatus(429)
            ->assertJsonPath('type', 'warning')
            ->assertHeader('Retry-After');
    }
}
