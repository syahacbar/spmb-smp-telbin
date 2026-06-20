<?php

namespace Tests\Unit;

use App\Services\PrestasiOpportunityCalculator;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class PrestasiOpportunityCalculatorTest extends TestCase
{
    private PrestasiOpportunityCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new PrestasiOpportunityCalculator;
    }

    public function test_menghitung_rata_rata_dan_peringkat_sementara(): void
    {
        $score = $this->calculator->score(80, 90);
        $ranking = $this->calculator->rank($score, collect([92, 87, 80]));

        $this->assertSame(85.0, $score);
        $this->assertSame(['rank' => 3, 'total' => 4], $ranking);
    }

    public function test_cutoff_hanya_terbentuk_ketika_pendaftar_memenuhi_kuota(): void
    {
        $this->assertNull($this->calculator->cutoff(collect([90, 80]), 3));
        $this->assertSame(80.0, $this->calculator->cutoff(collect([70, 90, 80]), 2));
    }

    public function test_estimasi_peluang_memiliki_label_visual(): void
    {
        $high = $this->calculator->estimate(90, 80, 20, 18);
        $low = $this->calculator->estimate(65, 80, 20, 30);

        $this->assertSame('high', $high['level']);
        $this->assertSame('Tinggi', $high['label']);
        $this->assertSame('low', $low['level']);
        $this->assertSame('Rendah', $low['label']);
        $this->assertGreaterThan($low['percentage'], $high['percentage']);
    }
}
