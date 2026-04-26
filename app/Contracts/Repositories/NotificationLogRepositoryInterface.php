<?php

namespace App\Contracts\Repositories;

use App\DTOs\NotificationData;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;

interface NotificationLogRepositoryInterface
{
    /**
     * Store a notification log entry.
     *
     * @param NotificationData $data
     * @return NotificationLog
     */
    public function log(NotificationData $data): NotificationLog;

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection;
}
