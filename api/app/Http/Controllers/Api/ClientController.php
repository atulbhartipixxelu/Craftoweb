<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Client::query()
                ->orderByDesc('created_at')
                ->get()
                ->map->toApiArray(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $client = Client::create([
            'name' => $validated['name'],
            'contact_person' => $validated['contactPerson'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'message' => 'Client created successfully.',
            'data' => $client->toApiArray(),
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        return response()->json(['data' => $client->toApiArray()]);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'contactPerson' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $client->update([
            'name' => $validated['name'] ?? $client->name,
            'contact_person' => array_key_exists('contactPerson', $validated)
                ? $validated['contactPerson']
                : $client->contact_person,
            'email' => $validated['email'] ?? $client->email,
            'phone' => $validated['phone'] ?? $client->phone,
            'company' => $validated['company'] ?? $client->company,
            'address' => $validated['address'] ?? $client->address,
            'notes' => $validated['notes'] ?? $client->notes,
            'status' => $validated['status'] ?? $client->status,
        ]);

        return response()->json(['data' => $client->fresh()->toApiArray()]);
    }

    public function destroy(Client $client): JsonResponse
    {
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
