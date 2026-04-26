<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTOs\NotificationData;
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
     */
    public function notifyByCategory(string $category, string $message): void
    {
        event(new \App\Events\SystemLogBroadcast('INFO', "Initiating notification process for category: {$category}"));
        
        $users = $this->userRepository->getSubscribersByCategory($category);
        
        event(new \App\Events\SystemLogBroadcast('INFO', "Found " . $users->count() . " subscribers for {$category}"));

        foreach ($users as $user) {
            foreach ($user->channels as $channel) {
                if (isset($this->providers[$channel->name])) {
                    event(new \App\Events\SystemLogBroadcast('INFO', "Routing message to {$user->name} via {$channel->name}"));
                    $data = new NotificationData(
                        userId: $user->id,
                        userName: $user->name,
                        userEmail: $user->email,
                        category: $category,
                        channel: $channel->name,
                        message: $message,
                        userPhone: $user->phone
                    );

                    $this->providers[$channel->name]->send($data);
                }
            }
        }
    }
}
