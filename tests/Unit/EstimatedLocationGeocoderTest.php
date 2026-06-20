<?php

namespace Tests\Unit;

use App\Services\EstimatedLocationGeocoder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EstimatedLocationGeocoderTest extends TestCase
{
    public function test_geocoding_mengembalikan_koordinat_dan_menggunakan_cache(): void
    {
        Cache::flush();
        Http::fake([
            '*' => Http::response([
                ['lat' => '-2.1234', 'lon' => '133.5678'],
            ]),
        ]);

        $geocoder = app(EstimatedLocationGeocoder::class);
        $first = $geocoder->geocode('Bintuni, Papua Barat, Indonesia');
        $second = $geocoder->geocode('Bintuni, Papua Barat, Indonesia');

        $this->assertSame([
            'latitude' => -2.1234,
            'longitude' => 133.5678,
        ], $first);
        $this->assertSame($first, $second);
        Http::assertSentCount(1);
    }

    public function test_geocoding_gagal_secara_aman_ketika_hasil_tidak_tersedia(): void
    {
        Cache::flush();
        Http::fake(['*' => Http::response([])]);

        $this->assertNull(
            app(EstimatedLocationGeocoder::class)->geocode('Wilayah tidak ditemukan'),
        );
    }
}
