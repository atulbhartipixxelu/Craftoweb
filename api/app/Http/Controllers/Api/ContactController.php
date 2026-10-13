<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Public — marketing site contact form.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $contact = Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        return response()->json([
            'message' => 'Thank you. We will get back to you soon.',
            'data' => $contact->toApiArray(),
        ], 201);
    }

    /**
     * Admin dashboard — list enquiries (newest first).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contact::query()->orderByDesc('created_at');

        if ($request->boolean('unread_only')) {
            $query->where('is_read', false);
        }

        return response()->json([
            'data' => $query->get()->map->toApiArray(),
        ]);
    }

    /**
     * Mark one enquiry as read.
     */
    public function markRead(Contact $contact): JsonResponse
    {
        $contact->update(['is_read' => true]);

        return response()->json(['data' => $contact->fresh()->toApiArray()]);
    }

    /**
     * Delete an enquiry.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json(['message' => 'Contact enquiry deleted.']);
    }
}
