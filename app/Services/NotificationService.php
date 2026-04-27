<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\NotificationData;
use App\Events\SystemLogBroadcast;
use App\Jobs\SendProviderNotificationJob;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;


class NotificationService
{
    /** @var NotificationProviderInterface[] */
    protected array $providers = [];

    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    /**
     * Add a notification provider to the service.
     */
    public function addProvider(NotificationProviderInterface $provider): void
    {
        $this->providers[$provider->getChannelName()] = $provider;
    }

    /**
     * Notify all users subscribed to a category.
     *
     * Each user/channel combination is dispatched as an independent queued Job.
     * This ensures:
     *  - The HTTP request returns immediately (non-blocking).
     *  - Each Job has its own retry policy (3 attempts, with backoff).
     *  - A single provider failure does not affect other users or channels.
     */
    public function notifyByCategory(string $category, string $message, bool $chaosMonkey = false): void
    {
        event(new SystemLogBroadcast('INFO', "Initiating notification process for category: {$category}"));

        $users = $this->userRepository->getSubscribersByCategory($category);

        event(new SystemLogBroadcast('INFO', "Found " . $users->count() . " subscribers for {$category}. Queuing jobs..."));

        foreach ($users as $user) {
            foreach ($user->channels as $channel) {
                if (isset($this->providers[$channel->name])) {
                    $data = new NotificationData(
                        userId: $user->id,
                        userName: $user->name,
                        userEmail: $user->email,
                        category: $category,
                        channel: $channel->name,
                        message: $message,
                        userPhone: $user->phone
                    );

                    // Dispatch a queued Job for each user/channel pair.
                    // The Job handles fault tolerance: if the provider fails (or Chaos Monkey
                    // throws an exception), the queue worker retries it automatically.
                    SendProviderNotificationJob::dispatch($data, $channel->name, $chaosMonkey);

                    event(new SystemLogBroadcast('INFO', "Job queued: [{$channel->name}] for {$user->name}."));
                }
            }
        }
    }
}

