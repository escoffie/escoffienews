<?php

namespace App\Jobs;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Events\NotificationLogged;
use App\Events\SystemLogBroadcast;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Enums\NotificationStatus;
use Illuminate\Support\Facades\Log;

class SendProviderNotificationJob implements ShouldQueue
{
    use Queueable;

    private const MAX_TRIES = 3;
    private const RETRY_BACKOFF_SECONDS = [5, 10, 20];
    private const CHAOS_MONKEY_FAIL_PERCENTAGE = 30;

    /**
     * Max number of times this job will be attempted before being marked as failed.
     */
    public int $tries = self::MAX_TRIES;

    /**
     * Number of seconds to wait before retrying the job (backoff sequence).
     */
    public array $backoff = self::RETRY_BACKOFF_SECONDS;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly NotificationData $data,
        public readonly string $channelName,
        public readonly bool $chaosMonkey = false
    ) {}

    /**
     * Execute the job.
     * 
     * Runs in the background queue worker. If an exception is thrown,
     * the queue worker will automatically retry this job based on $tries and $backoff.
     */
    public function handle(
        NotificationLogRepositoryInterface $logRepository
    ): void {
        $attempt = $this->attempts();

        if ($attempt > 1) {
            event(new SystemLogBroadcast('INFO', "Retrying [{$this->channelName}] for {$this->data->userName} (attempt #{$attempt})..."));
        }

        // Chaos Monkey: randomly simulate a provider failure by throwing an exception.
        // This forces the Queue Worker to catch it and schedule a retry.
        if ($this->chaosMonkey && rand(1, 100) <= self::CHAOS_MONKEY_FAIL_PERCENTAGE) {
            throw new \RuntimeException("Chaos Monkey intercepted [{$this->channelName}] for {$this->data->userName}. Will retry...");
        }

        // Resolve the correct provider from the container by channel name.
        // Each provider is bound in a ServiceProvider (e.g. NotificationServiceProvider).
        $providers = app()->tagged('notification.providers');
        $provider = collect($providers)->first(
            fn (NotificationProviderInterface $p) => $p->getChannelName() === $this->channelName
        );

        if (! $provider) {
            throw new \RuntimeException("No provider registered for channel [{$this->channelName}].");
        }

        $success = $provider->send($this->data);

        if ($success) {
            $log = $logRepository->log($this->data, $this->attempts(), NotificationStatus::SENT);
            event(new NotificationLogged($log));
            event(new SystemLogBroadcast('INFO', "Delivered [{$this->channelName}] to {$this->data->userName}."));
        }
    }

    /**
     * Handle a permanent job failure after all retry attempts are exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Permanent notification failure", [
            'user' => $this->data->userName,
            'channel' => $this->channelName,
            'error' => $exception->getMessage(),
        ]);

        // Log the permanent failure to history
        $logRepository = app(NotificationLogRepositoryInterface::class);
        $log = $logRepository->log($this->data, $this->attempts(), NotificationStatus::FAILED);
        event(new NotificationLogged($log));

        event(new SystemLogBroadcast(
            'ERROR',
            "PERMANENT FAILURE after {$this->tries} attempts: [{$this->channelName}] for {$this->data->userName}. Message: {$exception->getMessage()}"
        ));
    }
}
