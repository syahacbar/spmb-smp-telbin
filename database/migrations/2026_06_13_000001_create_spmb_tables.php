<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tb_pengguna')) {
            Schema::create('tb_pengguna', function (Blueprint $table): void {
                $table->string('id_pengguna', 11)->primary();
                $table->string('nama_pengguna', 100)->default('');
                $table->text('alamat')->nullable();
                $table->string('telpon', 20)->default('');
                $table->string('email', 100)->nullable()->unique();
                $table->string('username', 50)->default('');
                $table->string('password');
                $table->enum('level', ['Administrator', 'User'])->default('User');
                $table->boolean('is_active')->default(true);
            });
        } else {
            DB::statement('ALTER TABLE tb_pengguna MODIFY password varchar(255) NOT NULL');
        }

        if (! Schema::hasTable('tb_formulir')) {
            Schema::create('tb_formulir', function (Blueprint $table): void {
                $table->id();
                $table->string('nisn', 50)->index();
                $table->string('nama', 100);
                $table->string('tempat_lahir', 100);
                $table->date('tanggal_lahir');
                $table->string('nik', 30);
                $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
                $table->string('agama', 50);
                $table->string('hp', 20);
                $table->string('asal_sekolah', 100);
                $table->text('alamat');
                $table->string('nama_ayah', 100);
                $table->string('pekerjaan_ayah', 100);
                $table->string('nama_ibu', 100);
                $table->string('pekerjaan_ibu', 100);
                $table->string('hp_ortu', 20);
                $table->text('alamat_ortu');
                $table->string('program_keahlian_1', 100);
                $table->string('program_keahlian_2', 100);
                $table->string('surat_keterangan_lulus', 255);
                $table->string('kartu_keluarga', 255);
                $table->string('foto_selfie', 255);
                $table->timestamp('created_at')->useCurrent();
            });
        } else {
            Schema::table('tb_formulir', function (Blueprint $table): void {
                if (! Schema::hasColumn('tb_formulir', 'nik')) {
                    $table->string('nik', 30)->after('tanggal_lahir')->default('');
                }
                if (! Schema::hasColumn('tb_formulir', 'hp')) {
                    $table->string('hp', 20)->after('agama')->default('');
                }
                if (! Schema::hasColumn('tb_formulir', 'pekerjaan_ibu')) {
                    $table->string('pekerjaan_ibu', 100)->after('nama_ibu')->default('');
                }
                if (! Schema::hasColumn('tb_formulir', 'hp_ortu')) {
                    $table->string('hp_ortu', 20)->after('pekerjaan_ibu')->default('');
                }
                if (! Schema::hasColumn('tb_formulir', 'alamat_ortu')) {
                    $table->text('alamat_ortu')->after('hp_ortu')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // Tidak menjatuhkan tabel agar rollback tidak menghapus data legacy.
    }
};
