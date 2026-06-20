<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class EstimatedLocationGeocoder
{
    public function geocode(string $query): ?array
    {
        $query = trim($query);

        if ($query === '') {
            return null;
        }

        $cached = Cache::remember(
            'geocode:'.sha1(mb_strtolower($query)),
            now()->addDays(30),
            function () use ($query): array {
                $location = $this->request($query);

                return $location
                    ? ['found' => true, 'location' => $location]
                    : ['found' => false];
            },
        );

        return ($cached['found'] ?? false) ? $cached['location'] : null;
    }

    private function request(string $query): ?array
    {
        try {
            $result = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => config('services.nominatim.user_agent'),
                ])
                ->timeout(8)
                ->retry(2, 300, throw: false)
                ->get(config('services.nominatim.url'), [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'countrycodes' => 'id',
                ])
                ->json('0');
        } catch (Throwable) {
            return null;
        }

        if (! is_array($result) || ! isset($result['lat'], $result['lon'])) {
            return null;
        }

        return [
            'latitude' => (float) $result['lat'],
            'longitude' => (float) $result['lon'],
        ];
    }
}
