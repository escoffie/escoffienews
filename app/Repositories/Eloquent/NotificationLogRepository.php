<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Models\NotificationLog;
use Illuminate\Support\Collection;

class NotificationLogRepository implements NotificationLogRepositoryInterface
{
    /**
     * Store a notification log entry.
     *
     * @param NotificationData $data
     * @return NotificationLog
     */
    public function log(NotificationData $data): NotificationLog
    {
        return NotificationLog::create([
            'user_id' => $data->userId,
            'user_name' => $data->userName,
            'user_email' => $data->userEmail,
            'category' => $data->category,
            'channel' => $data->channel,
            'message' => $data->message,
        ]);
    }

    /**
     * Get all logs ordered by newest.
     *
     * @return Collection
     */
    public function getAllLogs(): Collection
    {
        return NotificationLog::with('user')->orderByDesc('created_at')->orderByDesc('id')->limit(100)->get();
    }
}
