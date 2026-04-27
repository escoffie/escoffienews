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
     * @param int $attempts
     * @param string $status
     * @return NotificationLog
     */
    public function log(NotificationData $data, int $attempts = 1, string $status = 'sent'): NotificationLog;

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
