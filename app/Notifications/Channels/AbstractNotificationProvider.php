<?php

namespace App\Notifications\Channels;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Events\NotificationLogged;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use Illuminate\Support\Facades\Log;

abstract class AbstractNotificationProvider implements NotificationProviderInterface
{
    public function __construct(
        protected NotificationLogRepositoryInterface $logRepository
    ) {}

    /**
     * Common logic for all notifications.
     */
    public function send(NotificationData $data): bool
    {
        $success = $this->deliver($data);

        if ($success) {
            $log = $this->logRepository->log($data);
            event(new NotificationLogged($log));
        }

        return $success;
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
