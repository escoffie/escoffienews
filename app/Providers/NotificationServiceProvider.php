<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use App\Notifications\Channels\EmailProvider;
use App\Notifications\Channels\PushProvider;
use App\Notifications\Channels\SmsProvider;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Tag all providers so the queued Job can resolve them by channel name.
        $this->app->tag([SmsProvider::class, EmailProvider::class, PushProvider::class], 'notification.providers');

        $this->app->singleton(NotificationService::class, function ($app) {
            $service = new NotificationService(
                $app->make(UserRepositoryInterface::class)
            );

            // Register concrete providers for the synchronous service
            $service->addProvider($app->make(SmsProvider::class));
            $service->addProvider($app->make(EmailProvider::class));
            $service->addProvider($app->make(PushProvider::class));

            return $service;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
