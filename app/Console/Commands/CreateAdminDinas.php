<?php

namespace App\Console\Commands;

use App\Models\Pengguna;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminDinas extends Command
{
    protected $signature = 'spmb:create-admin-dinas
        {--name= : Nama lengkap admin}
        {--username= : Username login}
        {--phone= : Nomor WhatsApp}
        {--email= : Alamat email}';

    protected $description = 'Membuat akun Admin Dinas tanpa password bawaan';

    public function handle(): int
    {
        $name = trim((string) ($this->option('name') ?: $this->ask('Nama lengkap')));
        $username = trim((string) ($this->option('username') ?: $this->ask('Username')));
        $phone = preg_replace('/\D+/', '', (string) ($this->option('phone') ?: $this->ask('Nomor WhatsApp')));
        $email = trim((string) ($this->option('email') ?: $this->ask('Email')));
        $password = (string) $this->secret('Password minimal 12 karakter');
        $confirmation = (string) $this->secret('Ulangi password');

        if ($name === '' || $username === '' || strlen($password) < 12) {
            $this->error('Nama, username, dan password minimal 12 karakter wajib diisi.');

            return self::FAILURE;
        }

        if (! hash_equals($password, $confirmation)) {
            $this->error('Konfirmasi password tidak sama.');

            return self::FAILURE;
        }

        if (Pengguna::query()->where('username', $username)->exists()) {
            $this->error('Username sudah digunakan.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($name, $username, $phone, $email, $password): void {
            $pengguna = Pengguna::create([
                'id_pengguna' => substr('ADM'.strtoupper(Str::random(8)), 0, 11),
                'nama_pengguna' => $name,
                'telpon' => $phone,
                'email' => $email ?: null,
                'username' => $username,
                'password' => Hash::make($password),
                'level' => 'Administrator',
                'is_verified' => true,
                'is_active' => true,
                'verified_at' => now(),
            ]);

            $roleId = DB::table('roles')->where('kode', 'admin_dinas')->value('id');
            $pengguna->roles()->attach($roleId);
        });

        $this->info('Akun Admin Dinas berhasil dibuat.');

        return self::SUCCESS;
    }
}
