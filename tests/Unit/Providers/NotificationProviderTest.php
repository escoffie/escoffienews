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
}
