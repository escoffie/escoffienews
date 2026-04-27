<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\Events\MessageReceived;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationLogRepositoryInterface $logRepository
    ) {}

    /**
     * Display a listing of notification logs.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->logRepository->getAllLogs());
    }

    /**
     * Store a newly created notification in the system.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', Rule::exists('categories', 'name')],
            'message' => ['required', 'string', 'min:1'],
            'chaos_monkey' => ['nullable', 'boolean'],
        ]);

        // Dispatch the event to trigger the Pub-Sub flow
        event(new MessageReceived(
            $validated['category'],
            $validated['message'],
            $validated['chaos_monkey'] ?? false,
            (string) Str::uuid()
        ));

        return response()->json([
            'message' => 'Notification request received and queued for delivery.',
        ], 202);
    }

    /**
     * Remove all notification logs from the system.
     */
    public function destroyAll(): JsonResponse
    {
        $this->logRepository->clearAllLogs();
        return response()->json(['message' => 'Logs cleared successfully']);
    }
}
