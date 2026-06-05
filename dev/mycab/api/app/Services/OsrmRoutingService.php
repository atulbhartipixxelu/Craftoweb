<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OsrmRoutingService
{
    private const BASE_URL = 'https://router.project-osrm.org/route/v1/driving';

    /**
     * Driving route between two points. OSRM uses lng,lat order.
     *
     * @return array{distance_km: float, duration_min: float|null, coordinates: array<int, array{0: float, 1: float}>}|null
     *   coordinates are [lat, lng] pairs for map polylines
     */
    public function drivingRoute(float $pickupLat, float $pickupLng, float $dropoffLat, float $dropoffLng): ?array
    {
        $url = sprintf(
            '%s/%s,%s;%s,%s?overview=full&geometries=geojson',
            self::BASE_URL,
            $pickupLng,
            $pickupLat,
            $dropoffLng,
            $dropoffLat,
        );

        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            $route = $data['routes'][0] ?? null;

            if (! $route || ! isset($route['distance'])) {
                return null;
            }

            $coordinates = [];
            $raw = $route['geometry']['coordinates'] ?? [];

            foreach ($raw as $pair) {
                if (! is_array($pair) || count($pair) < 2) {
                    continue;
                }
                $coordinates[] = [(float) $pair[1], (float) $pair[0]];
            }

            return [
                'distance_km' => round(((float) $route['distance']) / 1000, 2),
                'duration_min' => isset($route['duration']) ? round(((float) $route['duration']) / 60, 1) : null,
                'coordinates' => $coordinates,
            ];
        } catch (\Throwable $e) {
            Log::warning('OSRM routing failed', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
