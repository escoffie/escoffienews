<?php

namespace Tests\Unit\Providers;

use App\Contracts\Repositories\NotificationLogRepositoryInterface;
use App\DTOs\NotificationData;
use App\Notifications\Channels\EmailProvider;
use App\Notifications\Channels\SmsProvider;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationProviderTest extends TestCase
{
    public function test_sms_provider_returns_correct_channel_name(): void
    {
        $repo = $this->createMock(NotificationLogRepositoryInterface::class);
        $provider = new SmsProvider($repo);
        $this->assertEquals('SMS', $provider->getChannelName());
    }

    public function test_email_provider_returns_correct_channel_name(): void
    {
        $repo = $this->createMock(NotificationLogRepositoryInterface::class);
        $provider = new EmailProvider($repo);
        $this->assertEquals('E-Mail', $provider->getChannelName());
    }

    public function test_provider_logs_and_returns_success_on_send(): void
    {
        Log::shouldReceive('info')->once();
        
        $repo = $this->createMock(NotificationLogRepositoryInterface::class);
        $repo->expects($this->once())->method('log');

        $provider = new SmsProvider($repo);
        
        $data = new NotificationData(
            userId: 1, userName: 'J', userEmail: 'j@e.com',
            category: 'Finance', channel: 'SMS', message: 'Hi'
        );

        $result = $provider->send($data);
        $this->assertTrue($result);
    }

    public function test_push_provider_returns_correct_channel_name(): void
    {
        $repo = $this->createMock(NotificationLogRepositoryInterface::class);
        $provider = new \App\Notifications\Channels\PushProvider($repo);
        $this->assertEquals('Push Notification', $provider->getChannelName());
    }

    public function test_all_three_providers_have_distinct_channel_names(): void
    {
        $repo = $this->createMock(NotificationLogRepositoryInterface::class);

        $names = [
            (new SmsProvider($repo))->getChannelName(),
            (new EmailProvider($repo))->getChannelName(),
            (new \App\Notifications\Channels\PushProvider($repo))->getChannelName(),
        ];

        $this->assertCount(3, array_unique($names), 'Each provider must have a unique channel name.');
    }

    public function test_provider_does_not_log_when_deliver_returns_false(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        $repo = $this->createMock(NotificationLogRepositoryInterface::class);
        // If deliver() returns false, log() should never be called
        $repo->expects($this->never())->method('log');

        // SmsProvider's deliver() always returns true in a test environment,
        // so we test via a partial mock that forces deliver() to return false
        $provider = $this->getMockBuilder(SmsProvider::class)
            ->setConstructorArgs([$repo])
            ->onlyMethods(['deliver'])
            ->getMock();

        $provider->method('deliver')->willReturn(false);

        $data = new NotificationData(
            userId: 1, userName: 'J', userEmail: 'j@e.com',
            category: 'Finance', channel: 'SMS', message: 'Hi'
        );

        $result = $provider->send($data);
        $this->assertFalse($result);
    }
}
