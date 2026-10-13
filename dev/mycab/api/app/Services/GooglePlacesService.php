<?php

namespace App\Services;

use App\Support\HimachalGeocoding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    private const GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public function __construct(
        private readonly ?string $apiKey = null,
    ) {
    }

    public static function fromConfig(): self
    {
        return new self((string) config('himcab.google_places_api_key'));
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    public function search(string $query): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(12)->get(self::GEOCODE_URL, [
                'address' => HimachalGeocoding::searchQuery($query),
                'key' => $this->apiKey,
                'region' => 'in',
                'components' => 'country:IN',
                'bounds' => '30.4,75.5|33.6,79.6',
                'language' => 'en',
            ]);

            if (! $response->successful()) {
                return [];
            }

            $results = $response->json('results');
            if (! is_array($results)) {
                return [];
            }

            $mapped = collect($results)
                ->map(function (array $item): ?array {
                    $label = $item['formatted_address'] ?? null;
                    $lat = $item['geometry']['location']['lat'] ?? null;
                    $lng = $item['geometry']['location']['lng'] ?? null;

                    if (! is_string($label) || $label === '' || $lat === null || $lng === null) {
                        return null;
                    }

                    return [
                        'label' => $label,
                        'lat' => (float) $lat,
                        'lng' => (float) $lng,
                    ];
                })
                ->filter()
                ->take(12)
                ->values()
                ->all();

            return HimachalGeocoding::rankResults($query, $mapped);
        } catch (\Throwable $e) {
            Log::warning('Google geocode search failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * Reverse geocode using Google — keeps exact GPS lat/lng for map pin.
     *
     * @return array{label: string, lat: float, lng: float}|null
     */
    public function reverse(float $lat, float $lng): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(12)->get(self::GEOCODE_URL, [
                'latlng' => $lat.','.$lng,
                'key' => $this->apiKey,
                'region' => 'in',
                'language' => 'en',
                'result_type' => 'street_address|route|locality|sublocality|administrative_area_level_3',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $results = $response->json('results');
            if (! is_array($results) || $results === []) {
                return null;
            }

            $item = $this->pickBestReverseResult($results, $lat, $lng);
            if ($item === null) {
                return null;
            }

            $label = $item['formatted_address'] ?? null;
            if (! is_string($label) || $label === '') {
                return null;
            }

            return [
                'label' => $label,
                'lat' => $lat,
                'lng' => $lng,
            ];
        } catch (\Throwable $e) {
            Log::warning('Google reverse geocode failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @return array<string, mixed>|null
     */
    private function pickBestReverseResult(array $results, float $lat, float $lng): ?array
    {
        $best = null;
        $bestScore = -INF;

        foreach ($results as $item) {
            $types = $item['types'] ?? [];
            if (! is_array($types)) {
                $types = [];
            }

            $rlat = (float) ($item['geometry']['location']['lat'] ?? $lat);
            $rlng = (float) ($item['geometry']['location']['lng'] ?? $lng);
            $distKm = sqrt((($rlat - $lat) ** 2) + (($rlng - $lng) ** 2)) * 111.0;

            $score = 0.0;
            if (in_array('street_address', $types, true)) {
                $score += 12;
            }
            if (in_array('route', $types, true)) {
                $score += 8;
            }
            if (in_array('locality', $types, true) || in_array('sublocality', $types, true)) {
                $score += 10;
            }
            if (in_array('administrative_area_level_3', $types, true)) {
                $score += 6;
            }
            if (in_array('administrative_area_level_1', $types, true)) {
                $score += 2;
            }
            $score -= min($distKm, 50.0) * 2;

            $label = mb_strtolower((string) ($item['formatted_address'] ?? ''));
            if (str_contains($label, 'himachal')) {
                $score += 5;
            }
            if (str_contains($label, 'chandigarh') && $distKm > 15) {
                $score -= 20;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $item;
            }
        }

        return $best;
    }
}
