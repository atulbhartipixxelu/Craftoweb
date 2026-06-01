<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyUpdate;
use App\Models\Mockup;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()->orderByDesc('created_at');

        if ($request->filled('technology') && $request->technology !== 'All') {
            $query->where('technology', $request->technology);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json([
            'data' => $query->get()->map->toApiArray(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'client' => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'startDate' => 'required|date',
            'status' => 'required|in:active,pending,completed',
            'priority' => 'required|in:high,medium,low',
            'progress' => 'integer|min:0|max:100',
            'value' => 'nullable|string|max:255',
            'mockups' => 'nullable|array',
            'mockups.*.title' => 'required_with:mockups|string|max:255',
            'mockups.*.imageUrl' => 'nullable|string',
            'mockups.*.description' => 'nullable|string',
            'mockups.*.status' => 'nullable|in:draft,approved,rejected',
        ]);

        $project = DB::transaction(function () use ($validated) {
            $project = Project::create([
                'name' => $validated['name'],
                'client' => $validated['client'],
                'technology' => $validated['technology'],
                'start_date' => $validated['startDate'],
                'status' => $validated['status'],
                'priority' => $validated['priority'],
                'progress' => $validated['progress'] ?? 0,
                'value' => $validated['value'] ?? '$0',
            ]);

            foreach ($validated['mockups'] ?? [] as $mockupData) {
                Mockup::create([
                    'project_id' => $project->id,
                    'title' => $mockupData['title'],
                    'image_url' => $mockupData['imageUrl'] ?? null,
                    'description' => $mockupData['description'] ?? '',
                    'status' => $mockupData['status'] ?? 'draft',
                ]);
            }

            return $project;
        });

        return response()->json(['data' => $project->toApiArray()], 201);
    }

    public function show(Project $project): JsonResponse
    {
        return response()->json(['data' => $project->toApiArray()]);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'client' => 'sometimes|string|max:255',
            'technology' => 'sometimes|string|max:255',
            'startDate' => 'sometimes|date',
            'status' => 'sometimes|in:active,pending,completed',
            'priority' => 'sometimes|in:high,medium,low',
            'progress' => 'sometimes|integer|min:0|max:100',
            'value' => 'sometimes|string|max:255',
        ]);

        $project->update([
            'name' => $validated['name'] ?? $project->name,
            'client' => $validated['client'] ?? $project->client,
            'technology' => $validated['technology'] ?? $project->technology,
            'start_date' => $validated['startDate'] ?? $project->start_date,
            'status' => $validated['status'] ?? $project->status,
            'priority' => $validated['priority'] ?? $project->priority,
            'progress' => $validated['progress'] ?? $project->progress,
            'value' => $validated['value'] ?? $project->value,
        ]);

        return response()->json(['data' => $project->fresh()->toApiArray()]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json(['message' => 'Project deleted']);
    }

    public function stats(): JsonResponse
    {
        $total = Project::count();
        $active = Project::where('status', 'active')->count();
        $completed = Project::where('status', 'completed')->count();
        $pending = Project::where('status', 'pending')->count();

        return response()->json([
            'data' => [
                'totalProjects' => $total,
                'activeProjects' => $active,
                'completedProjects' => $completed,
                'pendingProjects' => $pending,
            ],
        ]);
    }
}
