<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Enums\NotificationStatus;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;

class NotificationLogRepository implements NotificationLogRepositoryInterface
{
    private const DEFAULT_LOG_LIMIT = 100;

    /**
     * Store a notification log entry.
     *
     * @param NotificationData $data
     * @param int $attempts
     * @param string|NotificationStatus $status
     * @return NotificationLog
     */
    public function log(NotificationData $data, int $attempts = 1, string|NotificationStatus $status = NotificationStatus::SENT): NotificationLog
    {
        return NotificationLog::create([
            'batch_id' => $data->batchId,
            'user_id' => $data->userId,
            'user_name' => $data->userName,
            'user_email' => $data->userEmail,
            'category' => $data->category,
            'channel' => $data->channel,
            'message' => $data->message,
            'attempts' => $attempts,
            'status' => $status instanceof NotificationStatus ? $status->value : $status,
        ]);
    }

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection
    {
        return NotificationLog::with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::DEFAULT_LOG_LIMIT)
            ->get();
    }

    /**
     * Delete all notification logs from the database.
     */
    public function clearAllLogs(): void
    {
        NotificationLog::truncate();
    }
}
