<?php

namespace App\Notifications\Channels;

use App\Enums\ChannelType;
use App\DTOs\NotificationData;
use Illuminate\Support\Facades\Log;

class SmsProvider extends AbstractNotificationProvider
{
    protected function deliver(NotificationData $data): bool
    {
        Log::info("SMS SENT to {$data->userName} ({$data->userPhone}): {$data->message}");
        return true;
    }

    public function getChannelName(): string
    {
        return ChannelType::SMS->value;
    }
}
