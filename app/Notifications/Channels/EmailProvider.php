<?php

namespace App\Notifications\Channels;

use App\Enums\ChannelType;
use App\DTOs\NotificationData;
use Illuminate\Support\Facades\Log;

class EmailProvider extends AbstractNotificationProvider
{
    protected function deliver(NotificationData $data): bool
    {
        Log::info("EMAIL SENT to {$data->userName} ({$data->userEmail}): {$data->message}");
        return true;
    }

    public function getChannelName(): string
    {
        return ChannelType::EMAIL->value;
    }
}
