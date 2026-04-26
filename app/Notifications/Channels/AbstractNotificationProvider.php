<?php

namespace App\Notifications\Channels;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
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
        try {
            $success = $this->deliver($data);
            
            if ($success) {
                $this->logRepository->log($data);
                // Here we could also fire a SystemLog event for the terminal view
            }

            return $success;
        } catch (\Exception $e) {
            Log::error("Failed to send notification via {$this->getChannelName()}: " . $e->getMessage());
            return false;
        }
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
