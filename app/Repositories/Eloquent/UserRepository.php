<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\Category;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users subscribed to a specific category.
     */
    public function getSubscribersByCategory(string $categoryName): Collection
    {
        return User::whereHas('categories', function ($query) use ($categoryName) {
            $query->where('name', $categoryName);
        })->with('channels')->get();
    }

    /**
     * Get all users with their category and channel relationships.
     */
    public function getAllUsers(): Collection
    {
        return User::with(['categories', 'channels'])->orderByDesc('id')->get();
    }

    /**
     * Create a user and attach category + channel subscriptions within a transaction.
     */
    public function createUser(array $userData, array $categoryNames, array $channelNames): User
    {
        return DB::transaction(function () use ($userData, $categoryNames, $channelNames) {
            $user = User::create($userData);

            $categoryIds = Category::whereIn('name', $categoryNames)->pluck('id');
            $user->categories()->attach($categoryIds);

            $channelIds = Channel::whereIn('name', $channelNames)->pluck('id');
            $user->channels()->attach($channelIds);

            return $user->load(['categories', 'channels']);
        });
    }
}
