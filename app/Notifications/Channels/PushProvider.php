<?php

namespace App\Notifications\Channels;

use App\Enums\ChannelType;
use App\DTOs\NotificationData;
use Illuminate\Support\Facades\Log;

class PushProvider extends AbstractNotificationProvider
{
    protected function deliver(NotificationData $data): bool
    {
        Log::info("PUSH NOTIFICATION SENT to {$data->userName}: {$data->message}");
        return true;
    }

    public function getChannelName(): string
    {
        return ChannelType::PUSH->value;
    }
}
