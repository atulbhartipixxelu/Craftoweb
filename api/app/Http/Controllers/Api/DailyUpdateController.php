<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyUpdateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DailyUpdate::query()->with('project')->orderByDesc('date');

        if ($request->filled('projectId')) {
            $query->where('project_id', $request->projectId);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        return response()->json([
            'data' => $query->get()->map->toApiArray(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'projectId' => 'required|exists:projects,id',
            'date' => 'required|date',
            'description' => 'required|string',
            'hours' => 'nullable|numeric|min:0',
        ]);

        $update = DailyUpdate::create([
            'project_id' => $validated['projectId'],
            'date' => $validated['date'],
            'description' => $validated['description'],
            'hours' => $validated['hours'] ?? 0,
        ]);

        return response()->json(['data' => $update->toApiArray()], 201);
    }

    public function destroy(DailyUpdate $dailyUpdate): JsonResponse
    {
        $dailyUpdate->delete();

        return response()->json(['message' => 'Daily update deleted']);
    }
}
