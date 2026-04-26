<?php

namespace App\Listeners;

use App\Events\MessageReceived;
use App\Services\NotificationService;

class SendNotificationToSubscribers
{
    /**
     * Create the event listener.
     */
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
        $this->notificationService->notifyByCategory(
            $event->category,
            $event->message
        );
    }
}
