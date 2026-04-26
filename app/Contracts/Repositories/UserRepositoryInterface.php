<?php

namespace App\Contracts\Repositories;

use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users subscribed to a specific category.
     *
     * @param string $categoryName
     * @return Collection
     */
    public function getSubscribersByCategory(string $categoryName): Collection;
}
