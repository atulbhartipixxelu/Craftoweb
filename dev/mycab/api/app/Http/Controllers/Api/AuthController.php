<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use App\Support\VehicleTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['nullable', 'string', 'max:32'],
            'accepted_terms' => ['required', 'accepted'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'passenger',
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Register a cab driver: creates `users` (role driver) + linked `drivers` profile.
     */
    public function registerDriver(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:32', Rule::unique('users', 'phone')],
            'accepted_terms' => ['required', 'accepted'],
            'vehicle_type' => ['required', 'string', VehicleTypes::validationRule()],
            'cab_model' => ['required', 'string', 'max:100'],
            'seating_capacity' => ['required', 'integer', 'min:1', 'max:12'],
            'rate_per_km' => ['required', 'numeric', 'min:1', 'max:9999'],
            'plate_number' => ['required', 'string', 'max:32', Rule::unique('drivers', 'plate_number')],
        ]);

        $payload = DB::transaction(function () use ($validated) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'],
                'role' => 'driver',
            ]);

            $driver = Driver::query()->create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'vehicle_type' => $validated['vehicle_type'],
                'cab_model' => $validated['cab_model'],
                'seating_capacity' => $validated['seating_capacity'],
                'rate_per_km' => $validated['rate_per_km'],
                'plate_number' => $validated['plate_number'],
                'is_available' => true,
                'latitude' => null,
                'longitude' => null,
            ]);

            $token = $user->createToken('auth')->plainTextToken;

            return [
                'user' => $user->fresh(),
                'driver' => $driver,
                'token' => $token,
            ];
        });

        return response()->json($payload, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            $message = 'Invalid credentials';
            if ($user && $user->google_id) {
                $message = 'This account uses Google sign-in. Use “Continue with Google” below.';
            }

            return response()->json(['message' => $message], 422);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
