<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mockup;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MockupController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json([
            'data' => Mockup::query()->orderByDesc('created_at')->get()->map->toApiArray(),
        ]);
    }

    public function index(Project $project): JsonResponse
    {
        return response()->json([
            'data' => $project->mockups()->orderByDesc('created_at')->get()->map->toApiArray(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'projectId' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'imageUrl' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,approved,rejected',
        ]);

        $mockup = Mockup::create([
            'project_id' => $validated['projectId'],
            'title' => $validated['title'],
            'image_url' => $validated['imageUrl'] ?? null,
            'description' => $validated['description'] ?? '',
            'status' => $validated['status'] ?? 'draft',
        ]);

        return response()->json(['data' => $mockup->toApiArray()], 201);
    }

    public function destroy(Mockup $mockup): JsonResponse
    {
        $mockup->delete();

        return response()->json(['message' => 'Mockup deleted']);
    }
}
