<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_periode_spmb', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 100);
            $table->string('tahun_pendaftaran', 4)->unique();
            $table->string('tahun_pelajaran', 20);
            $table->date('mulai_registrasi')->nullable();
            $table->date('selesai_registrasi')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('tb_sekolah', function (Blueprint $table): void {
            $table->id();
            $table->string('npsn', 20)->nullable()->unique();
            $table->string('nama', 150);
            $table->enum('status', ['negeri', 'swasta'])->default('negeri');
            $table->foreignId('kecamatan_id')->nullable()->constrained('ref_kecamatan')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('ref_kelurahan')->nullOnDelete();
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 50)->unique();
            $table->string('nama', 100);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 100)->unique();
            $table->string('nama', 150);
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table): void {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::create('pengguna_role', function (Blueprint $table): void {
            $table->string('pengguna_id', 11);
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['pengguna_id', 'role_id']);
            $table->foreign('pengguna_id')->references('id_pengguna')->on('tb_pengguna')->cascadeOnDelete();
        });

        Schema::create('pengguna_sekolah', function (Blueprint $table): void {
            $table->string('pengguna_id', 11);
            $table->foreignId('sekolah_id')->constrained('tb_sekolah')->cascadeOnDelete();
            $table->primary(['pengguna_id', 'sekolah_id']);
            $table->foreign('pengguna_id')->references('id_pengguna')->on('tb_pengguna')->cascadeOnDelete();
        });

        Schema::create('tb_registrasi_akun', function (Blueprint $table): void {
            $table->id();
            $table->string('nisn', 11)->unique();
            $table->foreignId('periode_id')->constrained('tb_periode_spmb')->restrictOnDelete();
            $table->string('kabupaten', 100)->default('Teluk Bintuni');
            $table->foreignId('kecamatan_id')->nullable()->constrained('ref_kecamatan')->restrictOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('ref_kelurahan')->restrictOnDelete();
            $table->text('detail_alamat')->nullable();
            $table->string('kartu_keluarga_path', 255)->nullable();
            $table->string('status', 30)->default('menunggu_verifikasi')->index();
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('verified_by', 11)->nullable();
            $table->timestamps();

            $table->foreign('nisn')->references('id_pengguna')->on('tb_pengguna')->cascadeOnDelete();
            $table->foreign('verified_by')->references('id_pengguna')->on('tb_pengguna')->nullOnDelete();
        });

        Schema::create('tb_riwayat_verifikasi_akun', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registrasi_akun_id')->constrained('tb_registrasi_akun')->cascadeOnDelete();
            $table->string('status_sebelumnya', 30)->nullable();
            $table->string('status_baru', 30);
            $table->text('catatan')->nullable();
            $table->string('diproses_oleh', 11)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('diproses_oleh')->references('id_pengguna')->on('tb_pengguna')->nullOnDelete();
        });

        Schema::create('tb_jalur_pendaftaran', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tb_zonasi_sekolah', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('periode_id')->constrained('tb_periode_spmb')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('tb_sekolah')->cascadeOnDelete();
            $table->foreignId('kelurahan_id')->constrained('ref_kelurahan')->cascadeOnDelete();
            $table->unsignedInteger('prioritas')->default(1);
            $table->timestamps();
            $table->unique(['periode_id', 'sekolah_id', 'kelurahan_id'], 'zonasi_sekolah_wilayah_unique');
        });

        Schema::create('tb_kuota_sekolah_jalur', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('periode_id')->constrained('tb_periode_spmb')->cascadeOnDelete();
            $table->foreignId('sekolah_id')->constrained('tb_sekolah')->cascadeOnDelete();
            $table->foreignId('jalur_id')->constrained('tb_jalur_pendaftaran')->cascadeOnDelete();
            $table->unsignedInteger('kuota')->default(0);
            $table->timestamps();
            $table->unique(['periode_id', 'sekolah_id', 'jalur_id'], 'kuota_sekolah_jalur_unique');
        });

        $now = now();
        $tahun = (string) (DB::table('tb_pengaturan_spmb')->where('key', 'tahun_pendaftaran')->value('value') ?: date('Y'));
        $tahunPelajaran = (string) (DB::table('tb_pengaturan_spmb')->where('key', 'tahun_pelajaran')->value('value') ?: $tahun.'/'.((int) $tahun + 1));

        $periodeId = DB::table('tb_periode_spmb')->insertGetId([
            'nama' => "SPMB SMP Kabupaten Teluk Bintuni {$tahunPelajaran}",
            'tahun_pendaftaran' => $tahun,
            'tahun_pelajaran' => $tahunPelajaran,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $roles = [
            'calon_murid' => ['id' => 1, 'nama' => 'Calon Murid'],
            'admin_sekolah' => ['id' => 2, 'nama' => 'Admin Sekolah'],
            'admin_dinas' => ['id' => 3, 'nama' => 'Admin Dinas Kabupaten'],
        ];

        foreach ($roles as $kode => $role) {
            DB::table('roles')->insert(['id' => $role['id'], 'kode' => $kode, 'nama' => $role['nama'], 'created_at' => $now, 'updated_at' => $now]);
        }

        $permissions = [
            'pendaftaran.kelola_sendiri' => ['id' => 1, 'nama' => 'Mengelola pendaftaran sendiri'],
            'pendaftar.sekolah.lihat' => ['id' => 2, 'nama' => 'Melihat pendaftar sekolah'],
            'pendaftar.sekolah.verifikasi' => ['id' => 3, 'nama' => 'Memverifikasi pendaftar sekolah'],
            'master.kelola' => ['id' => 4, 'nama' => 'Mengelola seluruh master data'],
            'akun.verifikasi' => ['id' => 5, 'nama' => 'Memverifikasi registrasi akun'],
            'seleksi.kelola' => ['id' => 6, 'nama' => 'Mengelola proses seleksi'],
            'laporan.kabupaten' => ['id' => 7, 'nama' => 'Melihat laporan kabupaten'],
        ];

        foreach ($permissions as $kode => $permission) {
            DB::table('permissions')->insert(['id' => $permission['id'], 'kode' => $kode, 'nama' => $permission['nama'], 'created_at' => $now, 'updated_at' => $now]);
        }

        $roleIds = collect($roles)->map(fn (array $role) => $role['id']);
        $permissionIds = collect($permissions)->map(fn (array $permission) => $permission['id']);
        $grants = [
            'calon_murid' => ['pendaftaran.kelola_sendiri'],
            'admin_sekolah' => ['pendaftar.sekolah.lihat', 'pendaftar.sekolah.verifikasi'],
            'admin_dinas' => array_keys($permissions),
        ];

        foreach ($grants as $role => $permissionCodes) {
            foreach ($permissionCodes as $permissionCode) {
                DB::table('permission_role')->insert([
                    'role_id' => $roleIds[$role],
                    'permission_id' => $permissionIds[$permissionCode],
                ]);
            }
        }

        DB::table('tb_jalur_pendaftaran')->insert([
            ['kode' => 'domisili', 'nama' => 'Jalur Domisili', 'deskripsi' => 'Pilihan sekolah berdasarkan wilayah domisili terverifikasi.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'prestasi', 'nama' => 'Jalur Prestasi', 'deskripsi' => 'Seleksi lintas domisili berdasarkan nilai TKA.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'afirmasi', 'nama' => 'Jalur Afirmasi', 'deskripsi' => 'Untuk keluarga tidak mampu, disabilitas, atau kelompok khusus.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'mutasi', 'nama' => 'Jalur Mutasi', 'deskripsi' => 'Untuk perpindahan tugas atau pekerjaan orang tua/wali.', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $adminRoleId = $roleIds['admin_dinas'];
        $studentRoleId = $roleIds['calon_murid'];

        DB::table('tb_pengguna')->orderBy('id_pengguna')->each(function ($pengguna) use ($adminRoleId, $studentRoleId, $periodeId, $now): void {
            $isAdmin = $pengguna->level === 'Administrator';
            DB::table('pengguna_role')->insert([
                'pengguna_id' => $pengguna->id_pengguna,
                'role_id' => $isAdmin ? $adminRoleId : $studentRoleId,
            ]);

            if (! $isAdmin) {
                $status = $pengguna->is_verified ? 'terverifikasi' : 'menunggu_verifikasi';
                DB::table('tb_registrasi_akun')->insert([
                    'nisn' => $pengguna->id_pengguna,
                    'periode_id' => $periodeId,
                    'detail_alamat' => $pengguna->alamat,
                    'status' => $status,
                    'submitted_at' => $now,
                    'verified_at' => $pengguna->verified_at,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_kuota_sekolah_jalur');
        Schema::dropIfExists('tb_zonasi_sekolah');
        Schema::dropIfExists('tb_jalur_pendaftaran');
        Schema::dropIfExists('tb_riwayat_verifikasi_akun');
        Schema::dropIfExists('tb_registrasi_akun');
        Schema::dropIfExists('pengguna_sekolah');
        Schema::dropIfExists('pengguna_role');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('tb_sekolah');
        Schema::dropIfExists('tb_periode_spmb');
    }
};
