<?php

namespace App\Contracts\Repositories;

use App\DTOs\NotificationData;
use Illuminate\Support\Collection;

interface NotificationLogRepositoryInterface
{
    /**
     * Store a notification log entry.
     *
     * @param NotificationData $data
     * @return void
     */
    public function log(NotificationData $data): void;

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection;
}
