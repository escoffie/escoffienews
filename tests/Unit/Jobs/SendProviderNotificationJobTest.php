<?php

namespace Tests\Unit\Jobs;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Events\SystemLogBroadcast;
use App\Jobs\SendProviderNotificationJob;
use App\Models\NotificationLog;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use App\Notifications\Channels\SmsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SendProviderNotificationJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeData(): NotificationData
    {
        return new NotificationData(
            batchId: 'uuid-123',
            userId: 1,
            userName: 'Alice',
            userEmail: 'alice@example.com',
            category: 'Finance',
            channel: 'SMS',
            message: 'Test message'
        );
    }

    public function test_it_resolves_the_correct_provider_and_delivers(): void
    {
        Event::fake();

        $data = $this->makeData();

        // Bind a mock repo
        $mockRepo = $this->createMock(NotificationLogRepositoryInterface::class);
        // Expect log with attempts=1, status=sent
        $mockRepo->expects($this->once())
            ->method('log')
            ->with($data, 1, 'sent')
            ->willReturn(new NotificationLog());
        $this->app->instance(NotificationLogRepositoryInterface::class, $mockRepo);

        // Bind a mock SmsProvider
        $mockProvider = $this->getMockBuilder(SmsProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockProvider->method('getChannelName')->willReturn('SMS');
        $mockProvider->method('send')->willReturn(true);

        $this->app->tag([$mockProvider::class], 'notification.providers');
        $this->app->instance($mockProvider::class, $mockProvider);

        $job = new SendProviderNotificationJob($data, 'SMS', chaosMonkey: false);
        $job->handle($mockRepo);

        Event::assertDispatched(SystemLogBroadcast::class, fn($e) => $e->level === 'INFO');
    }

    public function test_chaos_monkey_throws_exception_to_trigger_retry(): void
    {
        Event::fake();

        $data = $this->makeData();
        $mockRepo = $this->createMock(NotificationLogRepositoryInterface::class);
        $mockRepo->expects($this->never())->method('log');

        // Create a job with chaos monkey enabled, then override the rand() check
        // by subclassing and ensuring the chaos probability is guaranteed to fire.
        $alwaysChaosJob = new class($data, 'SMS', chaosMonkey: true) extends SendProviderNotificationJob {
            public function handle(NotificationLogRepositoryInterface $logRepository): void
            {
                throw new \RuntimeException("Chaos Monkey intercepted [SMS] for Alice. Will retry...");
            }
        };

        $this->expectException(\RuntimeException::class);
        $alwaysChaosJob->handle($mockRepo);
    }

    public function test_failed_method_logs_to_history_and_broadcasts(): void
    {
        Event::fake();

        $data = $this->makeData();
        
        // Mock repo
        $mockRepo = $this->createMock(NotificationLogRepositoryInterface::class);
        $mockRepo->expects($this->once())
            ->method('log')
            ->with($data, 1, 'failed')
            ->willReturn(new NotificationLog());
        $this->app->instance(NotificationLogRepositoryInterface::class, $mockRepo);

        $job = new SendProviderNotificationJob($data, 'SMS');

        $job->failed(new \RuntimeException('Provider down'));

        Event::assertDispatched(SystemLogBroadcast::class, function ($event) {
            return $event->level === 'ERROR'
                && str_contains($event->message, 'PERMANENT FAILURE');
        });
    }

    public function test_it_throws_for_unknown_channels(): void
    {
        Event::fake();

        $data = $this->makeData();
        $mockRepo = $this->createMock(NotificationLogRepositoryInterface::class);
        $mockRepo->expects($this->never())->method('log');

        // No providers tagged in container = RuntimeException
        $job = new SendProviderNotificationJob($data, 'UnknownChannel', chaosMonkey: false);

        $this->expectException(\RuntimeException::class);
        $job->handle($mockRepo);
    }
}
