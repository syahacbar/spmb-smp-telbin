<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_home_page_displays_the_spmb_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('SPMB SMP KABUPATEN TELUK BINTUNI');
        $response->assertSee(route('login'), false);
        $response->assertSee('href="#cek-status"', false);
        $response->assertSee('Cek Status Sekarang');
        $response->assertSee(route('status.check'), false);
        $response->assertSee('Kartu Keluarga');
        $response->assertSee('data-open-image', false);
        $response->assertSee('data-status-form', false);
    }

    public function test_old_status_page_redirects_to_landing_status_section(): void
    {
        $response = $this->get('/cek-status');

        $response->assertRedirect('/#cek-status');
    }

    public function test_auth_pages_do_not_show_status_links(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('Cek Status SPMB')
            ->assertDontSee('Cek status');

        $this->get('/daftar')
            ->assertOk()
            ->assertSee('Masukkan NISN')
            ->assertSee('Selanjutnya')
            ->assertSee('Konfirmasi Data Calon Siswa')
            ->assertSee('Data Benar, Lanjutkan')
            ->assertSee('Data Salah, Hubungi Panitia')
            ->assertDontSee('Cek Status SPMB')
            ->assertDontSee('Email Aktif');
    }

    public function test_login_menampilkan_tombol_daftar_dominan_dan_link_beranda(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSee('Apakah anda belum punya akun SPMB? Klik daftar.')
            ->assertSee('Daftar Akun SPMB')
            ->assertSee('Jika anda sudah punya akun, masukkan NISN dan kata sandi.')
            ->assertSee('Kembali ke halaman beranda')
            ->assertSee('WhatsApp')
            ->assertSee('https://wa.me/', false)
            ->assertSee('btn btn-primary btn-lg w-100 fw-bold shadow-sm', false)
            ->assertSee('small text-muted text-decoration-none', false);
    }

    public function test_register_nisn_check_validates_nisn_before_lookup(): void
    {
        $response = $this->postJson('/daftar/cek-nisn', [
            'nisn' => '123',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonPath('message', 'NISN harus berisi 10 digit angka.');
    }

    public function test_status_check_supports_ajax_validation_response(): void
    {
        $response = $this
            ->withSession(['status_captcha_answer' => '10'])
            ->postJson('/cek-status', [
                'nisn' => '123',
                'captcha_answer' => '1',
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('ok', false)
            ->assertJsonStructure(['type', 'title', 'messages', 'captcha_question']);
    }
}
