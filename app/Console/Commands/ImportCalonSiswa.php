<?php

namespace App\Console\Commands;

use App\Models\CalonSiswa;
use App\Services\CalonSiswaImportReader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCalonSiswa extends Command
{
    protected $signature = 'spmb:import-calon-siswa
        {file : Path file XLSX/CSV whitelist}
        {--year= : Tahun pendaftaran}
        {--deactivate-missing : Nonaktifkan data tahun yang tidak ada dalam file}';

    protected $description = 'Mengimpor whitelist calon siswa beserta nilai TKA';

    public function handle(CalonSiswaImportReader $reader): int
    {
        $path = $this->argument('file');
        $path = $this->resolvePath($path);

        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");

            return self::FAILURE;
        }

        $tahun = (string) ($this->option('year') ?: date('Y'));
        $result = $reader->read($path);

        if ($result['valid']->isEmpty()) {
            $this->error('Tidak ada data valid yang dapat diimpor.');

            foreach (array_slice($result['errors'], 0, 10) as $error) {
                $this->line("- {$error}");
            }

            return self::FAILURE;
        }

        $importedNisn = $result['valid']->pluck('nisn')->all();

        DB::transaction(function () use ($tahun, $result, $importedNisn): void {
            if ($this->option('deactivate-missing')) {
                CalonSiswa::query()
                    ->where('tahun_pendaftaran', $tahun)
                    ->whereNotIn('nisn', $importedNisn)
                    ->update(['is_active' => false]);
            }

            foreach ($result['valid'] as $row) {
                CalonSiswa::updateOrCreate(
                    ['nisn' => $row['nisn']],
                    [...$row, 'tahun_pendaftaran' => $tahun, 'is_active' => true],
                );
            }
        });

        $this->info("Berhasil mengimpor {$result['valid']->count()} calon siswa untuk tahun {$tahun}.");

        if ($result['missing_score_count'] > 0) {
            $this->warn("{$result['missing_score_count']} siswa memiliki nilai TKA yang belum lengkap.");
        }

        if ($result['skipped'] > 0) {
            $this->warn("{$result['skipped']} baris dilewati karena tidak valid.");
        }

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) || str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        return base_path($path);
    }
}
