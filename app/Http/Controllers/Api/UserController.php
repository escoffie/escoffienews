<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Display a listing of users with their subscriptions.
     */
    public function index(): JsonResponse
    {
        return response()->json($this->userRepository->getAllUsers());
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'phone'        => 'required|string|max:20',
            'categories'   => 'required|array|min:1',
            'categories.*' => 'string|exists:categories,name',
            'channels'     => 'required|array|min:1',
            'channels.*'   => 'string|exists:channels,name',
        ]);

        try {
            $user = $this->userRepository->createUser(
                userData: [
                    'name'     => $validated['name'],
                    'email'    => $validated['email'],
                    'phone'    => $validated['phone'],
                    // Default password for demo/seeded users — no real auth in this challenge.
                    'password' => bcrypt('password123'),
                ],
                categoryNames: $validated['categories'],
                channelNames:  $validated['channels'],
            );

            return response()->json([
                'message' => 'User created successfully',
                'user'    => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
