<?php

namespace App\Providers;

use App\Contracts\Repositories\UserRepositoryInterface;
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
        $this->app->singleton(NotificationService::class, function ($app) {
            $service = new NotificationService(
                $app->make(UserRepositoryInterface::class)
            );

            // Register concrete providers
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
