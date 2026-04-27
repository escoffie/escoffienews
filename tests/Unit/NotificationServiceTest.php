<?php

namespace Tests\Unit;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Events\SystemLogBroadcast;
use App\Jobs\SendProviderNotificationJob;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_it_queues_jobs_for_subscribers(): void
    {
        Event::fake();
        Queue::fake();

        // 1. Setup Mocks
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $smsProvider = $this->createMock(NotificationProviderInterface::class);
        $emailProvider = $this->createMock(NotificationProviderInterface::class);

        $smsProvider->method('getChannelName')->willReturn('SMS');
        $emailProvider->method('getChannelName')->willReturn('E-Mail');

        // 2. Create a plain object that simulates a User with channels
        $mockUser = (object) [
            'id'       => 1,
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'phone'    => '123456',
            'channels' => collect([
                (object) ['name' => 'SMS'],
                (object) ['name' => 'E-Mail']
            ])
        ];

        $userRepo->method('getSubscribersByCategory')
            ->with('Finance')
            ->willReturn(collect([$mockUser]));

        // 3. Execute
        $service = new NotificationService($userRepo);
        $service->addProvider($smsProvider);
        $service->addProvider($emailProvider);
        $service->notifyByCategory('Finance', 'Test Message', batchId: 'test-batch-id');

        // 4. Assert that Jobs were dispatched to the queue (not executed directly)
        Queue::assertPushed(SendProviderNotificationJob::class, 2);

        Queue::assertPushed(SendProviderNotificationJob::class, function ($job) {
            return $job->channelName === 'SMS' && $job->data->batchId === 'test-batch-id';
        });

        Queue::assertPushed(SendProviderNotificationJob::class, function ($job) {
            return $job->channelName === 'E-Mail' && $job->data->batchId === 'test-batch-id';
        });

        // 5. Assert System Logs were dispatched
        Event::assertDispatched(SystemLogBroadcast::class);
    }

    public function test_chaos_monkey_flag_is_passed_to_job(): void
    {
        Event::fake();
        Queue::fake();

        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $smsProvider = $this->createMock(NotificationProviderInterface::class);
        $smsProvider->method('getChannelName')->willReturn('SMS');

        $mockUser = (object) [
            'id'       => 1,
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'phone'    => '123456',
            'channels' => collect([(object) ['name' => 'SMS']])
        ];

        $userRepo->method('getSubscribersByCategory')->willReturn(collect([$mockUser]));

        $service = new NotificationService($userRepo);
        $service->addProvider($smsProvider);
        $service->notifyByCategory('Finance', 'Test Message', chaosMonkey: true, batchId: 'chaos-batch');

        Queue::assertPushed(SendProviderNotificationJob::class, function ($job) {
            return $job->chaosMonkey === true && $job->data->batchId === 'chaos-batch';
        });
    }
}
