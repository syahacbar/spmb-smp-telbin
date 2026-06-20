<?php

namespace App\Console\Commands;

use App\Models\Pengguna;
use App\Models\Sekolah;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminSekolah extends Command
{
    protected $signature = 'spmb:create-admin-sekolah
        {school : ID sekolah}
        {--name= : Nama admin sekolah}
        {--username= : Username login}
        {--phone= : Nomor telepon}
        {--email= : Email}
        {--password= : Password minimal 12 karakter}';

    protected $description = 'Membuat akun Admin Sekolah dan menghubungkannya ke sekolah';

    public function handle(): int
    {
        $sekolah = Sekolah::find($this->argument('school'));

        if (! $sekolah) {
            $this->error('Sekolah tidak ditemukan.');

            return self::FAILURE;
        }

        $name = trim((string) ($this->option('name') ?: $this->ask('Nama admin sekolah')));
        $username = trim((string) ($this->option('username') ?: $this->ask('Username')));
        $phone = preg_replace('/\D+/', '', (string) ($this->option('phone') ?: $this->ask('Nomor telepon')));
        $email = trim((string) ($this->option('email') ?: $this->ask('Email')));
        $password = (string) ($this->option('password') ?: $this->secret('Password minimal 12 karakter'));

        if ($name === '' || $username === '' || strlen($password) < 12) {
            $this->error('Nama, username, dan password minimal 12 karakter wajib diisi.');

            return self::FAILURE;
        }

        if (Pengguna::where('username', $username)->exists()) {
            $this->error('Username sudah digunakan.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($sekolah, $name, $username, $phone, $email, $password): void {
            $pengguna = Pengguna::create([
                'id_pengguna' => substr('SCH'.strtoupper(Str::random(8)), 0, 11),
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

            $roleId = DB::table('roles')->where('kode', 'admin_sekolah')->value('id');
            $pengguna->roles()->attach($roleId);
            $pengguna->sekolah()->attach($sekolah->id);
        });

        $this->info("Akun Admin Sekolah untuk {$sekolah->nama} berhasil dibuat.");

        return self::SUCCESS;
    }
}
