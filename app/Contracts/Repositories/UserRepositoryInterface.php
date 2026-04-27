<?php

namespace App\Contracts\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users subscribed to a specific category.
     */
    public function getSubscribersByCategory(string $categoryName): Collection;

    /**
     * Get all users with their category and channel relationships.
     */
    public function getAllUsers(): Collection;

    /**
     * Create a new user and attach their category and channel subscriptions.
     *
     * @param array $userData   Scalar user fields (name, email, phone, password).
     * @param array $categoryNames  Category names to subscribe the user to.
     * @param array $channelNames   Channel names to assign to the user.
     */
    public function createUser(array $userData, array $categoryNames, array $channelNames): User;
}
