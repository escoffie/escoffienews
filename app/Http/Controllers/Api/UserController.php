<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users with their subscriptions.
     */
    public function index(): JsonResponse
    {
        $users = User::with(['categories', 'channels'])->orderByDesc('id')->get();
        return response()->json($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string|exists:categories,name',
            'channels' => 'required|array|min:1',
            'channels.*' => 'string|exists:channels,name',
        ]);

        try {
            DB::beginTransaction();

            // Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => bcrypt('password123'), // Default password
            ]);

            // Map category names to IDs
            $categoryIds = Category::whereIn('name', $validated['categories'])->pluck('id');
            $user->categories()->attach($categoryIds);

            // Map channel names to IDs
            $channelIds = Channel::whereIn('name', $validated['channels'])->pluck('id');
            $user->channels()->attach($channelIds);

            DB::commit();

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user->load(['categories', 'channels'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
