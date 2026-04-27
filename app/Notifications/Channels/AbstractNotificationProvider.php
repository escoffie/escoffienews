<?php

namespace App\Notifications\Channels;

use App\DTOs\NotificationData;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;

abstract class AbstractNotificationProvider implements NotificationProviderInterface
{
    /**
     * Delegate delivery to the concrete provider implementation.
     * 
     * Logging and event broadcasting are handled by SendProviderNotificationJob,
     * which calls this method. Keeping them here would cause double-logging.
     */
    public function send(NotificationData $data): bool
    {
        return $this->deliver($data);
    }

    /**
     * Concrete delivery logic to be implemented by children.
     */
    abstract protected function deliver(NotificationData $data): bool;

    /**
     * Return the channel name.
     */
    abstract public function getChannelName(): string;
}
