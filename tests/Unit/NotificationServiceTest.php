<?php

namespace Tests\Unit;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Notifications\Channels\Contracts\NotificationProviderInterface;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_it_notifies_subscribers_through_correct_providers(): void
    {
        Event::fake();

        // 1. Setup Mocks
        $userRepo = $this->createMock(UserRepositoryInterface::class);
        $smsProvider = $this->createMock(NotificationProviderInterface::class);
        $emailProvider = $this->createMock(NotificationProviderInterface::class);

        $smsProvider->method('getChannelName')->willReturn('SMS');
        $emailProvider->method('getChannelName')->willReturn('E-Mail');

        // 2. Create a plain object that simulates a User with channels
        $mockUser = (object) [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '123456',
            'channels' => collect([
                (object) ['name' => 'SMS'],
                (object) ['name' => 'E-Mail']
            ])
        ];

        $userRepo->method('getSubscribersByCategory')
            ->with('Finance')
            ->willReturn(collect([$mockUser]));

        // 3. Expectations
        $smsProvider->expects($this->once())->method('send');
        $emailProvider->expects($this->once())->method('send');

        // 4. Execute
        $service = new NotificationService($userRepo);
        $service->addProvider($smsProvider);
        $service->addProvider($emailProvider);

        $service->notifyByCategory('Finance', 'Test Message');

        // 5. Assert System Logs were dispatched
        Event::assertDispatched(\App\Events\SystemLogBroadcast::class);
    }
}
