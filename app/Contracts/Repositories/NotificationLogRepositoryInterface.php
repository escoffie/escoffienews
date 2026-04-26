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
     * @return \App\Models\NotificationLog
     */
    public function log(NotificationData $data): \App\Models\NotificationLog;

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection;
}
