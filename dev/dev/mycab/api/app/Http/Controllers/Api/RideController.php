<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\OsrmRoutingService;
use App\Services\RideBookingNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RideController extends Controller
{
    private const DRIVER_SEARCH_RADIUS_KM = 5.0;

    /**
     * Active bookings for the authenticated passenger.
     */
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required.');

        $rides = Ride::query()
            ->where('user_id', $request->user()->id)
            ->with(['driver', 'user'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rides);
    }

    /**
     * Book a new ride.
     */
    public function store(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required to book a ride.');

        $validated = $request->validate([
            'pickup_address' => ['required', 'string', 'max:500'],
            'dropoff_address' => ['required', 'string', 'max:500'],
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'vehicle_type' => ['required', 'string', 'in:mini,sedan,suv'],
            'driver_id' => ['required', 'integer', 'exists:drivers,id'],
        ]);

        $distanceKm = $this->resolveDistanceKm($validated);

        $driverOption = collect($this->nearbyDriverOptions($validated))
            ->firstWhere('id', (int) $validated['driver_id']);

        if (! $driverOption) {
            return response()->json(['message' => 'Selected driver is not available within 5 km. Please choose another driver.'], 422);
        }

        $ride = Ride::query()->create([
            'user_id' => $request->user()->id,
            'driver_id' => $validated['driver_id'],
            'pickup_address' => $validated['pickup_address'],
            'dropoff_address' => $validated['dropoff_address'],
            'pickup_lat' => $validated['pickup_lat'] ?? null,
            'pickup_lng' => $validated['pickup_lng'] ?? null,
            'dropoff_lat' => $validated['dropoff_lat'] ?? null,
            'dropoff_lng' => $validated['dropoff_lng'] ?? null,
            'vehicle_type' => $validated['vehicle_type'],
            'status' => 'requested',
            'distance_km' => $distanceKm,
            'fare_estimate' => $driverOption['fare_estimate'],
        ]);

        Driver::query()->whereKey($validated['driver_id'])->update(['is_available' => false]);

        $ride->refresh()->load(['driver', 'user']);

        $notifications = app(RideBookingNotifier::class)->sendEmails($ride);

        $payload = $ride->toArray();
        $payload['notifications'] = $notifications;

        return response()->json($payload, 201);
    }

    /**
     * Show available drivers within 5 km of the pickup point.
     */
    public function driverOptions(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required to book a ride.');

        $validated = $request->validate([
            'pickup_address' => ['required', 'string', 'max:500'],
            'dropoff_address' => ['required', 'string', 'max:500'],
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_lng' => ['required', 'numeric', 'between:-180,180'],
            'dropoff_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'dropoff_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'vehicle_type' => ['required', 'string', 'in:mini,sedan,suv'],
        ]);

        return response()->json([
            'radius_km' => self::DRIVER_SEARCH_RADIUS_KM,
            'distance_km' => $this->resolveDistanceKm($validated),
            'drivers' => $this->nearbyDriverOptions($validated),
        ]);
    }

    /**
     * Single ride details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required.');

        $ride = Ride::query()->with(['driver', 'user'])->findOrFail($id);

        if ($ride->user_id !== $request->user()->id) {
            abort(403);
        }

        return response()->json($ride);
    }

    /**
     * Cancel a ride that has not finished yet.
     */
    public function cancel(Request $request, Ride $ride): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required.');

        if ($ride->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! in_array($ride->status, ['pending', 'requested', 'accepted', 'in_progress'], true)) {
            return response()->json(['message' => 'This ride cannot be cancelled'], 422);
        }

        if ($ride->driver_id) {
            Driver::query()->whereKey($ride->driver_id)->update(['is_available' => true]);
        }

        $ride->update(['status' => 'cancelled']);

        $ride->load(['driver', 'user']);

        return response()->json($ride);
    }

    /**
     * Passenger shares current GPS so the assigned driver can see them on the map.
     */
    public function updatePassengerLocation(Request $request, Ride $ride): JsonResponse
    {
        abort_unless($request->user()->role === 'passenger', 403, 'Passenger account required.');

        if ($ride->user_id !== $request->user()->id) {
            abort(403);
        }

        if (! in_array($ride->status, ['requested', 'accepted', 'in_progress'], true)) {
            return response()->json(['message' => 'Location can only be updated for active rides.'], 422);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $ride->update([
            'passenger_live_lat' => $validated['latitude'],
            'passenger_live_lng' => $validated['longitude'],
        ]);

        return response()->json($ride->fresh()->load(['driver', 'user']));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveDistanceKm(array $validated): float
    {
        $plat = $validated['pickup_lat'] ?? null;
        $plng = $validated['pickup_lng'] ?? null;
        $dlat = $validated['dropoff_lat'] ?? null;
        $dlng = $validated['dropoff_lng'] ?? null;

        if ($plat !== null && $plng !== null && $dlat !== null && $dlng !== null) {
            $road = app(OsrmRoutingService::class)->drivingRoute(
                (float) $plat,
                (float) $plng,
                (float) $dlat,
                (float) $dlng,
            );

            if ($road !== null) {
                return $road['distance_km'];
            }

            return round($this->haversineKm((float) $plat, (float) $plng, (float) $dlat, (float) $dlng), 2);
        }

        return 5.0;
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $earthKm * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }

    private function estimateFare(string $vehicleType, float $distanceKm, float | null $ratePerKm = null): float
    {
        $base = match ($vehicleType) {
            'mini' => 49,
            'sedan' => 69,
            'suv' => 99,
            default => 59,
        };

        return round($base + $distanceKm * ($ratePerKm ?? 14), 2);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<int, array<string, mixed>>
     */
    private function nearbyDriverOptions(array $validated): array
    {
        $pickupLat = (float) $validated['pickup_lat'];
        $pickupLng = (float) $validated['pickup_lng'];
        $rideDistanceKm = $this->resolveDistanceKm($validated);

        return Driver::query()
            ->where('is_available', true)
            ->where('vehicle_type', $validated['vehicle_type'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderByRaw('case when user_id is not null then 0 else 1 end')
            ->orderBy('id')
            ->get()
            ->map(function (Driver $driver) use ($pickupLat, $pickupLng, $rideDistanceKm, $validated): array {
                $distanceToPickupKm = $this->haversineKm($pickupLat, $pickupLng, (float) $driver->latitude, (float) $driver->longitude);
                $ratePerKm = $driver->rate_per_km ?? 14;

                return [
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'phone' => $driver->phone,
                    'avatar_url' => $driver->avatar_url,
                    'vehicle_type' => $driver->vehicle_type,
                    'cab_model' => $driver->cab_model,
                    'seating_capacity' => $driver->seating_capacity,
                    'plate_number' => $driver->plate_number,
                    'rate_per_km' => (float) $ratePerKm,
                    'latitude' => $driver->latitude,
                    'longitude' => $driver->longitude,
                    'location_name' => $driver->location_name,
                    'distance_to_pickup_km' => round($distanceToPickupKm, 2),
                    'fare_estimate' => $this->estimateFare($validated['vehicle_type'], $rideDistanceKm, (float) $ratePerKm),
                ];
            })
            ->filter(fn (array $driver): bool => $driver['distance_to_pickup_km'] <= self::DRIVER_SEARCH_RADIUS_KM)
            ->sortBy('distance_to_pickup_km')
            ->values()
            ->all();
    }
}
