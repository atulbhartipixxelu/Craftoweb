<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Ride;
use App\Services\DriverCommissionService;
use App\Support\DriverAvatars;
use App\Support\VehicleTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DriverRideController extends Controller
{
    /**
     * Logged-in driver's profile (linked `drivers` row).
     */
    public function me(Request $request): JsonResponse
    {
        $driver = $this->resolveDriver($request);
        $commission = app(DriverCommissionService::class)->commissionSummaryForDriver($driver);

        return response()->json([
            'user' => $request->user(),
            'driver' => $driver,
            'commission' => $commission,
        ]);
    }

    /**
     * Quick GPS ping while on duty (so passengers see the cab moving on the map).
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $driver->update([
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        return response()->json($driver->fresh());
    }

    /**
     * Update driver profile (text fields + optional avatar image). Syncs linked `users` row for name/email/phone.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $driver = $this->resolveDriver($request);
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:32', Rule::unique('users', 'phone')->ignore($user->id)],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'vehicle_type' => ['sometimes', 'string', VehicleTypes::validationRule()],
            'cab_model' => ['sometimes', 'string', 'max:100'],
            'seating_capacity' => ['sometimes', 'integer', 'min:1', 'max:12'],
            'rate_per_km' => ['sometimes', 'numeric', 'min:1', 'max:9999'],
            'plate_number' => ['sometimes', 'string', 'max:32', Rule::unique('drivers', 'plate_number')->ignore($driver->id)],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'location_name' => ['sometimes', 'nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('avatar')) {
            DriverAvatars::delete($driver->avatar);
            $validated['avatar'] = DriverAvatars::store($request->file('avatar'));
        }

        $userFields = array_intersect_key($validated, array_flip(['name', 'email', 'phone']));
        if ($userFields !== []) {
            $user->update($userFields);
        }

        $driverFields = array_intersect_key($validated, array_flip([
            'name',
            'email',
            'phone',
            'vehicle_type',
            'cab_model',
            'seating_capacity',
            'rate_per_km',
            'plate_number',
            'latitude',
            'longitude',
            'location_name',
            'avatar',
        ]));
        if ($driverFields !== []) {
            $driver->update($driverFields);
        }

        $driver->refresh();

        return response()->json([
            'user' => $user->fresh(),
            'driver' => $driver,
            'message' => 'Profile updated',
        ]);
    }

    /**
     * Rides assigned to this driver (newest first).
     */
    public function index(Request $request): JsonResponse
    {
        $driver = $this->resolveDriver($request);

        $rides = Ride::query()
            ->where('driver_id', $driver->id)
            ->with(['user', 'driver'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rides);
    }

    public function acceptRide(Request $request, Ride $ride): JsonResponse
    {
        $driver = $this->resolveDriver($request);
        $this->abortUnlessRideBelongsToDriver($ride, $driver);

        if ($ride->status !== 'requested') {
            return response()->json(['message' => 'Only requested rides can be accepted.'], 422);
        }

        $ride->update(['status' => 'accepted']);
        $driver->update(['is_available' => false]);

        return response()->json($ride->fresh()->load(['user', 'driver']));
    }

    public function rejectRide(Request $request, Ride $ride): JsonResponse
    {
        $driver = $this->resolveDriver($request);
        $this->abortUnlessRideBelongsToDriver($ride, $driver);

        if ($ride->status !== 'requested') {
            return response()->json(['message' => 'Only requested rides can be rejected.'], 422);
        }

        $ride->update(['status' => 'rejected']);
        $driver->update(['is_available' => true]);

        return response()->json($ride->fresh()->load(['user', 'driver']));
    }

    /**
     * Mark an active ride as finished and return the driver to available.
     */
    public function completeRide(Request $request, Ride $ride): JsonResponse
    {
        $driver = $this->resolveDriver($request);
        $this->abortUnlessRideBelongsToDriver($ride, $driver);

        if (! in_array($ride->status, ['accepted', 'in_progress'], true)) {
            return response()->json(['message' => 'Only accepted or in-progress rides can be completed.'], 422);
        }

        $validated = $request->validate([
            'fare_paid' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $farePaid = array_key_exists('fare_paid', $validated)
            ? (float) $validated['fare_paid']
            : (float) $ride->fare_estimate;

        $ride->update([
            'status' => 'completed',
            'payment_method' => config('himcab.payment_method', 'cash'),
            'payment_status' => 'paid',
            'fare_paid' => $farePaid,
            'paid_at' => now(),
        ]);
        $driver->update(['is_available' => true]);

        return response()->json($ride->fresh()->load(['user', 'driver']));
    }

    private function resolveDriver(Request $request): Driver
    {
        abort_unless($request->user()->role === 'driver', 403, 'Only drivers can access this resource.');

        /** @var Driver|null $driver */
        $driver = Driver::query()->where('user_id', $request->user()->id)->first();

        abort_if($driver === null, 404, 'Driver profile not found. Complete registration or contact support.');

        return $driver;
    }

    private function abortUnlessRideBelongsToDriver(Ride $ride, Driver $driver): void
    {
        abort_unless($ride->driver_id === $driver->id, 403, 'This ride is not assigned to your driver profile.');
    }
}
