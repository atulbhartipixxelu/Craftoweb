<?php

namespace App\Services;

use App\Support\HimachalGeocoding;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesService
{
    private const GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    private const AUTOCOMPLETE_URL = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';

    private const TEXT_SEARCH_URL = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

    private const DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json';

    private const BIAS_LAT = 32.2198;

    private const BIAS_LNG = 76.3234;

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

        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $textSearch = $this->textSearch($query);
        if ($textSearch !== []) {
            return HimachalGeocoding::rankResults($query, $textSearch);
        }

        $autocomplete = $this->autocompleteSearch($query);
        if ($autocomplete !== []) {
            return HimachalGeocoding::rankResults($query, $autocomplete);
        }

        $geocoded = $this->geocodeSingle(HimachalGeocoding::searchQuery($query));
        if ($geocoded === null) {
            return [];
        }

        return HimachalGeocoding::rankResults($query, [$geocoded]);
    }

    /**
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    private function textSearch(string $query): array
    {
        try {
            $response = Http::timeout(10)->get(self::TEXT_SEARCH_URL, [
                'query' => HimachalGeocoding::searchQuery($query),
                'key' => $this->apiKey,
                'region' => 'in',
                'location' => self::BIAS_LAT.','.self::BIAS_LNG,
                'radius' => 200000,
                'language' => 'en',
            ]);

            return $this->mapPlaceResults($response->json(), $query);
        } catch (\Throwable $e) {
            Log::warning('Google text search failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    private function autocompleteSearch(string $query): array
    {
        try {
            $response = Http::timeout(10)->get(self::AUTOCOMPLETE_URL, [
                'input' => $query,
                'key' => $this->apiKey,
                'components' => 'country:in',
                'location' => self::BIAS_LAT.','.self::BIAS_LNG,
                'radius' => 200000,
                'language' => 'en',
            ]);

            $body = $response->json();
            if (! is_array($body)) {
                return [];
            }

            $status = (string) ($body['status'] ?? '');
            if (! in_array($status, ['OK', 'ZERO_RESULTS'], true)) {
                $this->logGoogleFailure('autocomplete', $status, $body, $query);

                return [];
            }

            $predictions = $body['predictions'] ?? [];
            if (! is_array($predictions) || $predictions === []) {
                return [];
            }

            $placeIds = collect($predictions)
                ->take(6)
                ->pluck('place_id')
                ->filter(fn ($id): bool => is_string($id) && $id !== '')
                ->values()
                ->all();

            if ($placeIds === []) {
                return [];
            }

            $responses = Http::pool(fn ($pool) => collect($placeIds)->map(
                fn (string $placeId) => $pool->as($placeId)->timeout(8)->get(self::DETAILS_URL, [
                    'place_id' => $placeId,
                    'fields' => 'formatted_address,geometry',
                    'key' => $this->apiKey,
                    'language' => 'en',
                ])
            )->all());

            $results = [];
            foreach ($placeIds as $placeId) {
                $detailResponse = $responses[$placeId] ?? null;
                if ($detailResponse === null) {
                    continue;
                }

                $mapped = $this->mapDetailsResponse($detailResponse->json());
                if ($mapped !== null) {
                    $results[] = $mapped;
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::warning('Google autocomplete search failed', ['message' => $e->getMessage()]);

            return [];
        }
    }

    /**
     * @param  array<string, mixed>|null  $body
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    private function mapPlaceResults(?array $body, string $query): array
    {
        if (! is_array($body)) {
            return [];
        }

        $status = (string) ($body['status'] ?? '');
        if (! in_array($status, ['OK', 'ZERO_RESULTS'], true)) {
            $this->logGoogleFailure('textsearch', $status, $body, $query);

            return [];
        }

        $rows = $body['results'] ?? [];
        if (! is_array($rows)) {
            return [];
        }

        return collect($rows)
            ->take(8)
            ->map(function (mixed $item): ?array {
                if (! is_array($item)) {
                    return null;
                }

                $label = $item['formatted_address'] ?? $item['name'] ?? null;
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
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>|null  $body
     * @return array{label: string, lat: float, lng: float}|null
     */
    private function mapDetailsResponse(?array $body): ?array
    {
        if (! is_array($body) || ($body['status'] ?? '') !== 'OK') {
            return null;
        }

        $result = $body['result'] ?? null;
        if (! is_array($result)) {
            return null;
        }

        $label = $result['formatted_address'] ?? null;
        $lat = $result['geometry']['location']['lat'] ?? null;
        $lng = $result['geometry']['location']['lng'] ?? null;

        if (! is_string($label) || $label === '' || $lat === null || $lng === null) {
            return null;
        }

        return [
            'label' => $label,
            'lat' => (float) $lat,
            'lng' => (float) $lng,
        ];
    }

    /**
     * @return array{label: string, lat: float, lng: float}|null
     */
    private function geocodeSingle(string $address): ?array
    {
        try {
            $response = Http::timeout(10)->get(self::GEOCODE_URL, [
                'address' => $address,
                'key' => $this->apiKey,
                'region' => 'in',
                'components' => 'country:IN',
                'bounds' => '30.4,75.5|33.6,79.6',
                'language' => 'en',
            ]);

            $body = $response->json();
            if (! is_array($body) || ($body['status'] ?? '') !== 'OK') {
                if (is_array($body) && ($body['status'] ?? '') !== 'ZERO_RESULTS') {
                    $this->logGoogleFailure('geocode', (string) ($body['status'] ?? ''), $body, $address);
                }

                return null;
            }

            $results = $body['results'] ?? [];
            if (! is_array($results) || $results === []) {
                return null;
            }

            $mapped = $this->mapPlaceResults(['status' => 'OK', 'results' => [$results[0]]], $address);

            return $mapped[0] ?? null;
        } catch (\Throwable $e) {
            Log::warning('Google geocode search failed', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function logGoogleFailure(string $api, string $status, array $body, string $query): void
    {
        Log::warning("Google {$api} failed", [
            'status' => $status,
            'error' => $body['error_message'] ?? null,
            'query' => $query,
        ]);
    }

    /**
     * @return array{label: string, lat: float, lng: float}|null
     */
    public function reverse(float $lat, float $lng): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(10)->get(self::GEOCODE_URL, [
                'latlng' => $lat.','.$lng,
                'key' => $this->apiKey,
                'region' => 'in',
                'language' => 'en',
                'result_type' => 'street_address|route|locality|sublocality|administrative_area_level_3',
            ]);

            if (! $response->successful()) {
                return null;
            }

            $body = $response->json();
            if (! is_array($body) || ($body['status'] ?? '') !== 'OK') {
                return null;
            }

            $results = $body['results'] ?? [];
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
