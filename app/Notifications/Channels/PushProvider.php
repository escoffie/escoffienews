<?php

namespace App\Notifications\Channels;

use App\Enums\ChannelType;
use App\DTOs\NotificationData;
use Illuminate\Support\Facades\Log;

class PushProvider extends AbstractNotificationProvider
{
    protected function deliver(NotificationData $data): bool
    {
        // NOTE: In a real-world project, this is where you would integrate with
        // an actual Push provider service (e.g., Firebase FCM, OneSignal, Apple APNs).
        // Example: return FCM::send($data->userToken, $data->message);

        Log::info("PUSH NOTIFICATION SENT to {$data->userName}: {$data->message}");
        return true;
    }

    public function getChannelName(): string
    {
        return ChannelType::PUSH->value;
    }
}
