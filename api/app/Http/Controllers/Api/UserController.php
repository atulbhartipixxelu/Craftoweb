<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::query()->orderByDesc('created_at')->get()->map->toApiArray(),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->toApiArray()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['user', 'super_admin'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return response()->json(['data' => $user->toApiArray()], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['sometimes', Rule::in(['user', 'super_admin'])],
        ]);

        if (
            isset($validated['role'])
            && $validated['role'] !== 'super_admin'
            && $user->isSuperAdmin()
            && User::where('role', 'super_admin')->count() <= 1
        ) {
            return response()->json(['message' => 'Cannot remove the only super admin.'], 422);
        }

        $user->name = $validated['name'] ?? $user->name;
        $user->email = $validated['email'] ?? $user->email;
        $user->role = $validated['role'] ?? $user->role;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json(['data' => $user->fresh()->toApiArray()]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        if ($user->isSuperAdmin() && User::where('role', 'super_admin')->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the only super admin.'], 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
