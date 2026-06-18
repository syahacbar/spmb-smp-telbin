<?php

namespace Tests\Unit;

use App\Services\CalonSiswaImportReader;
use PHPUnit\Framework\TestCase;

class CalonSiswaImportReaderTest extends TestCase
{
    public function test_membaca_workbook_whitelist_dan_mempertahankan_nisn_nol_di_depan(): void
    {
        $path = dirname(__DIR__, 2).'/database/import/HASIL TKA SD BINTUNI.xlsx';

        $this->assertFileExists($path);

        $result = (new CalonSiswaImportReader())->read($path);

        $this->assertCount(1425, $result['valid']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(4, $result['missing_score_count']);
        $this->assertSame('0108672654', $result['valid']->first()['nisn']);
        $this->assertSame('2013-11-21', $result['valid']->first()['tanggal_lahir']);
        $this->assertSame(30.0, $result['valid']->first()['nilai_tka_matematika']);
        $this->assertSame(30.0, $result['valid']->first()['nilai_tka_bahasa_indonesia']);
    }
}
