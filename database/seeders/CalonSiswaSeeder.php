<?php

namespace Database\Seeders;

use App\Models\CalonSiswa;
use Illuminate\Database\Seeder;

class CalonSiswaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/calon_siswa.csv');

        if (! is_file($path)) {
            $this->command?->warn('File database/data/calon_siswa.csv tidak ditemukan. Seeder calon siswa dilewati.');

            return;
        }

        $file = fopen($path, 'r');
        $header = fgetcsv($file);
        $importedNisn = [];

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            if (! $data || empty($data['nisn'])) {
                continue;
            }

            $importedNisn[] = $data['nisn'];
            CalonSiswa::updateOrCreate(['nisn' => $data['nisn']], $data);
        }

        fclose($file);

        CalonSiswa::whereNotIn('nisn', $importedNisn)->delete();
    }
}
