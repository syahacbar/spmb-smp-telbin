<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class CalonSiswaImportReader
{
    private const REQUIRED_COLUMNS = [
        'nisn',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'asal_sekolah',
    ];

    public function read(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $rows = match ($extension) {
            'xlsx' => $this->readXlsx($path),
            'csv', 'txt' => $this->readCsv($path),
            default => throw new RuntimeException('Format file whitelist harus XLSX, CSV, atau TXT.'),
        };

        if ($rows === []) {
            return $this->emptyResult(['File tidak memiliki baris data.']);
        }

        $header = array_map(fn ($value) => $this->normalizeHeader((string) $value), array_shift($rows));
        $missingColumns = array_values(array_diff(self::REQUIRED_COLUMNS, $header));

        if ($missingColumns !== []) {
            return $this->emptyResult([
                'Kolom wajib tidak ditemukan: '.implode(', ', $missingColumns).'.',
            ]);
        }

        $valid = collect();
        $errors = [];
        $seenNisn = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $row = array_slice(array_pad($row, count($header), ''), 0, count($header));
            $data = array_combine($header, $row);

            if (! $data || collect($data)->every(fn ($value) => trim((string) $value) === '')) {
                continue;
            }

            $nisn = str_pad(preg_replace('/\D+/', '', (string) ($data['nisn'] ?? '')), 10, '0', STR_PAD_LEFT);
            $tanggalLahir = $this->normalizeDate($data['tanggal_lahir'] ?? null);
            $nilaiMatematika = $this->normalizeScore($data['nilai_tka_matematika'] ?? null);
            $nilaiBahasaIndonesia = $this->normalizeScore($data['nilai_tka_bahasa_indonesia'] ?? null);
            $rowErrors = [];

            if (! preg_match('/^\d{10}$/', $nisn)) {
                $rowErrors[] = 'NISN harus 10 digit';
            }

            foreach (['nama', 'tempat_lahir', 'asal_sekolah'] as $field) {
                if (trim((string) ($data[$field] ?? '')) === '') {
                    $rowErrors[] = "{$field} kosong";
                }
            }

            if (! $tanggalLahir) {
                $rowErrors[] = 'tanggal lahir tidak valid';
            }

            if ($nilaiMatematika === false) {
                $rowErrors[] = 'nilai Matematika harus berada pada rentang 0-100';
            }

            if ($nilaiBahasaIndonesia === false) {
                $rowErrors[] = 'nilai Bahasa Indonesia harus berada pada rentang 0-100';
            }

            if (isset($seenNisn[$nisn])) {
                $rowErrors[] = "NISN duplikat dengan baris {$seenNisn[$nisn]}";
            }

            if ($rowErrors !== []) {
                $errors[] = "Baris {$rowNumber}: ".implode('; ', $rowErrors).'.';
                continue;
            }

            $seenNisn[$nisn] = $rowNumber;
            $valid->push([
                'nisn' => $nisn,
                'nama' => trim((string) $data['nama']),
                'tempat_lahir' => trim((string) $data['tempat_lahir']),
                'tanggal_lahir' => $tanggalLahir,
                'asal_sekolah' => trim((string) $data['asal_sekolah']),
                'nilai_tka_matematika' => $nilaiMatematika,
                'nilai_tka_bahasa_indonesia' => $nilaiBahasaIndonesia,
            ]);
        }

        return [
            'valid' => $valid,
            'skipped' => count($errors),
            'errors' => $errors,
            'missing_score_count' => $valid->filter(
                fn (array $row) => $row['nilai_tka_matematika'] === null
                    || $row['nilai_tka_bahasa_indonesia'] === null,
            )->count(),
        ];
    }

    private function readCsv(string $path): array
    {
        $file = fopen($path, 'r');

        if (! $file) {
            throw new RuntimeException('File whitelist tidak dapat dibuka.');
        }

        $rows = [];

        while (($row = fgetcsv($file)) !== false) {
            $rows[] = $row;
        }

        fclose($file);

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException('Workbook XLSX tidak dapat dibuka.');
        }

        try {
            $sharedStrings = $this->sharedStrings($zip);
            $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

            if ($worksheetXml === false) {
                throw new RuntimeException('Sheet pertama pada workbook tidak ditemukan.');
            }

            $worksheet = $this->loadXml($worksheetXml);
            $worksheet->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $rows = [];

            foreach ($worksheet->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]') ?: [] as $row) {
                $values = [];

                foreach ($row->xpath('./*[local-name()="c"]') ?: [] as $cell) {
                    $reference = (string) $cell['r'];
                    $columnIndex = $this->columnIndex(preg_replace('/\d+/', '', $reference));
                    $type = (string) $cell['t'];
                    $value = '';
                    $valueNodes = $cell->xpath('./*[local-name()="v"]') ?: [];
                    $inlineNodes = $cell->xpath('./*[local-name()="is"]/*[local-name()="t"]') ?: [];
                    $rawValue = isset($valueNodes[0]) ? (string) $valueNodes[0] : null;

                    if ($type === 's' && $rawValue !== null) {
                        $value = $sharedStrings[(int) $rawValue] ?? '';
                    } elseif ($type === 'inlineStr' && isset($inlineNodes[0])) {
                        $value = (string) $inlineNodes[0];
                    } elseif ($rawValue !== null) {
                        $value = $rawValue;
                    }

                    $values[$columnIndex] = $value;
                }

                if ($values !== []) {
                    $maxIndex = max(array_keys($values));
                    $rows[] = array_map(
                        fn (int $index) => $values[$index] ?? '',
                        range(0, $maxIndex),
                    );
                }
            }

            return $rows;
        } finally {
            $zip->close();
        }
    }

    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        $shared = $this->loadXml($xml);
        $shared->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        return array_map(
            fn (SimpleXMLElement $item) => implode('', array_map(
                fn (SimpleXMLElement $text) => (string) $text,
                $item->xpath('.//*[local-name()="t"]') ?: [],
            )),
            $shared->xpath('//*[local-name()="si"]') ?: [],
        );
    }

    private function loadXml(string $xml): SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);

        try {
            $document = simplexml_load_string($xml, SimpleXMLElement::class, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        if (! $document) {
            throw new RuntimeException('Struktur XML pada workbook tidak valid.');
        }

        return $document;
    }

    private function normalizeHeader(string $header): string
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $header)));

        return match ($normalized) {
            'nisn' => 'nisn',
            'nama', 'nama siswa', 'nama calon siswa' => 'nama',
            'tempat lahir' => 'tempat_lahir',
            'tanggal lahir', 'tgl lahir' => 'tanggal_lahir',
            'asal sekolah', 'sekolah asal' => 'asal_sekolah',
            'nilai matematika', 'matematika', 'nilai tka matematika' => 'nilai_tka_matematika',
            'nilai bahasa indonesia', 'bahasa indonesia', 'nilai tka bahasa indonesia' => 'nilai_tka_bahasa_indonesia',
            default => str_replace(' ', '_', $normalized),
        };
    }

    private function normalizeDate(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return CarbonImmutable::create(1899, 12, 30)
                ->addDays((int) floor((float) $value))
                ->format('Y-m-d');
        }

        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$/u', $value, $matches)) {
            $month = $months[strtolower($matches[2])] ?? null;

            if ($month && checkdate($month, (int) $matches[1], (int) $matches[3])) {
                return sprintf('%04d-%02d-%02d', $matches[3], $month, $matches[1]);
            }
        }

        $timestamp = strtotime(str_replace('/', '-', $value));

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }

    private function normalizeScore(mixed $value): float|null|false
    {
        $value = trim(str_replace(',', '.', (string) $value));

        if ($value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return false;
        }

        $score = round((float) $value, 2);

        return $score >= 0 && $score <= 100 ? $score : false;
    }

    private function columnIndex(string $column): int
    {
        $index = 0;

        foreach (str_split(strtoupper($column)) as $character) {
            $index = ($index * 26) + (ord($character) - 64);
        }

        return $index - 1;
    }

    private function emptyResult(array $errors = []): array
    {
        return [
            'valid' => new Collection(),
            'skipped' => 0,
            'errors' => $errors,
            'missing_score_count' => 0,
        ];
    }
}
