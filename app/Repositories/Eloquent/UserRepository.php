<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users subscribed to a specific category.
     *
     * @param string $categoryName
     * @return Collection
     */
    public function getSubscribersByCategory(string $categoryName): Collection
    {
        return User::whereHas('categories', function ($query) use ($categoryName) {
            $query->where('name', $categoryName);
        })->with('channels')->get();
    }
}
