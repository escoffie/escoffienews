<?php

namespace App\Notifications\Channels\Contracts;

use App\DTOs\NotificationData;

interface NotificationProviderInterface
{
    /**
     * Send the notification using the specific channel.
     *
     * @param NotificationData $data
     * @return bool
     */
    public function send(NotificationData $data): bool;

    /**
     * Get the channel name.
     *
     * @return string
     */
    public function getChannelName(): string;
}
