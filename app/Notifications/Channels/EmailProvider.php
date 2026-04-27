<?php

namespace App\Notifications\Channels;

use App\Enums\ChannelType;
use App\DTOs\NotificationData;
use Illuminate\Support\Facades\Log;

class EmailProvider extends AbstractNotificationProvider
{
    protected function deliver(NotificationData $data): bool
    {
        // NOTE: In a real-world project, this is where you would integrate with
        // an actual Email provider service (e.g., SendGrid, Mailgun, AWS SES).
        // Example: return Mail::to($data->userEmail)->send(new NotificationMail($data));

        Log::info("EMAIL SENT to {$data->userName} ({$data->userEmail}): {$data->message}");
        return true;
    }

    public function getChannelName(): string
    {
        return ChannelType::EMAIL->value;
    }
}
