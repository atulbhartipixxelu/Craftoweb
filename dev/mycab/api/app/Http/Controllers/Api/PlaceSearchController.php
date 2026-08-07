<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GooglePlacesService;
use App\Support\HimachalGeocoding;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlaceSearchController extends Controller
{
    private const NOMINATIM_BASE = 'https://nominatim.openstreetmap.org';

    private function nominatimHttp(): PendingRequest
    {
        return Http::timeout(12)
            ->withOptions([
                'verify' => config('services.nominatim.ssl_verify', true),
            ])
            ->withHeaders([
                'User-Agent' => (string) config('services.nominatim.user_agent'),
                'Accept-Language' => 'en,hi',
            ]);
    }

    private function useGoogle(): bool
    {
        return config('himcab.location_provider') === 'google'
            && GooglePlacesService::fromConfig()->isConfigured();
    }

    /**
     * Which geocoder is active (Book + Driver profile use the same APIs).
     */
    public function config(): JsonResponse
    {
        $google = GooglePlacesService::fromConfig()->isConfigured();
        $provider = $this->useGoogle() ? 'google' : 'nominatim';

        return response()->json([
            'provider' => $provider,
            'google_configured' => $google,
            'fallback' => $google ? 'nominatim' : null,
            'label' => $provider === 'google' ? 'Google Geocoding API' : 'OpenStreetMap (Nominatim)',
        ]);
    }

    /**
     * Forward geocode: address / place text → suggestions.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        if ($this->useGoogle()) {
            $cacheKey = 'places:google:search:'.md5(mb_strtolower($q));
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return response()->json($cached);
            }

            $mapped = GooglePlacesService::fromConfig()->search($q);
            if ($mapped !== []) {
                Cache::put($cacheKey, $mapped, now()->addMinutes(30));

                return response()->json($mapped);
            }

            Log::info('Google place search empty — falling back to Nominatim', ['q' => $q]);
        }

        return response()->json($this->nominatimSearch($q));
    }

    /**
     * Reverse geocode: GPS coordinates → single address line.
     */
    public function reverse(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];

        if ($this->useGoogle()) {
            $cacheKey = 'places:google:reverse:'.round($lat, 5).'_'.round($lng, 5);
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return response()->json($cached);
            }

            $payload = GooglePlacesService::fromConfig()->reverse($lat, $lng);
            if ($payload !== null) {
                Cache::put($cacheKey, $payload, now()->addHours(24));

                return response()->json($payload);
            }

            Log::info('Google reverse geocode failed — falling back to Nominatim', [
                'lat' => $lat,
                'lng' => $lng,
            ]);
        }

        $payload = $this->nominatimReverse($lat, $lng);
        if ($payload === null) {
            return response()->json(['message' => 'Could not resolve location'], 422);
        }

        return response()->json($payload);
    }

    /**
     * @return array<int, array{label: string, lat: float, lng: float}>
     */
    private function nominatimSearch(string $q): array
    {
        $cacheKey = 'places:search:'.md5(mb_strtolower($q));
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $searchQ = HimachalGeocoding::searchQuery($q);

        $response = $this->nominatimHttp()->get(self::NOMINATIM_BASE.'/search', [
            'q' => $searchQ,
            'format' => 'json',
            'limit' => 12,
            'addressdetails' => 1,
            'countrycodes' => 'in',
            'viewbox' => HimachalGeocoding::VIEWBOX,
            'bounded' => 0,
        ]);

        if (! $response->successful()) {
            return [];
        }

        $rows = $response->json();
        if (! is_array($rows)) {
            return [];
        }

        $mapped = collect($rows)->map(function (array $item) {
            $lat = isset($item['lat']) ? (float) $item['lat'] : null;
            $lng = isset($item['lon']) ? (float) $item['lon'] : null;
            $label = self::formatNominatimLabel($item);

            if ($label === '' || $lat === null || $lng === null) {
                return null;
            }

            return [
                'label' => $label,
                'lat' => $lat,
                'lng' => $lng,
            ];
        })->filter()->values()->all();

        $mapped = HimachalGeocoding::rankResults($q, $mapped);

        if ($mapped !== []) {
            Cache::put($cacheKey, $mapped, now()->addMinutes(30));
        }

        return $mapped;
    }

    /**
     * @return array{label: string, lat: float, lng: float}|null
     */
    private function nominatimReverse(float $lat, float $lng): ?array
    {
        $cacheKey = 'places:reverse:'.round($lat, 5).'_'.round($lng, 5);
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $response = $this->nominatimHttp()->get(self::NOMINATIM_BASE.'/reverse', [
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
            'zoom' => 18,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $item = $response->json();
        if (! is_array($item)) {
            return null;
        }

        $label = $item['display_name'] ?? null;
        if (! is_string($label) || $label === '') {
            return null;
        }

        $payload = [
            'label' => $label,
            'lat' => $lat,
            'lng' => $lng,
        ];

        Cache::put($cacheKey, $payload, now()->addHours(24));

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private static function formatNominatimLabel(array $item): string
    {
        $addr = $item['address'] ?? null;
        if (is_array($addr)) {
            $parts = array_filter([
                $addr['road'] ?? $addr['pedestrian'] ?? $addr['neighbourhood'] ?? null,
                $addr['suburb'] ?? $addr['town'] ?? $addr['city'] ?? $addr['village'] ?? $addr['county'] ?? null,
                $addr['state'] ?? 'Himachal Pradesh',
            ]);

            if ($parts !== []) {
                return implode(', ', $parts);
            }
        }

        return (string) ($item['display_name'] ?? '');
    }
}
