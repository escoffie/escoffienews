<?php

namespace App\Contracts\Repositories;

use App\DTOs\NotificationData;
use App\Enums\NotificationStatus;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;

interface NotificationLogRepositoryInterface
{
    /**
     * Store a notification log entry.
     *
     * @param NotificationData $data
     * @param int $attempts
     * @param string|NotificationStatus $status
     * @return NotificationLog
     */
    public function log(NotificationData $data, int $attempts = 1, string|NotificationStatus $status = NotificationStatus::SENT): NotificationLog;

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection;

    /**
     * Delete all notification logs from the database.
     */
    public function clearAllLogs(): void;
}
