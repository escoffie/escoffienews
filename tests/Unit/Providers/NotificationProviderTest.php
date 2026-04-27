<?php

namespace Tests\Unit\Providers;

use App\DTOs\NotificationData;
use App\Notifications\Channels\EmailProvider;
use App\Notifications\Channels\PushProvider;
use App\Notifications\Channels\SmsProvider;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationProviderTest extends TestCase
{
    public function test_sms_provider_returns_correct_channel_name(): void
    {
        $provider = new SmsProvider();
        $this->assertEquals('SMS', $provider->getChannelName());
    }

    public function test_email_provider_returns_correct_channel_name(): void
    {
        $provider = new EmailProvider();
        $this->assertEquals('E-Mail', $provider->getChannelName());
    }

    public function test_push_provider_returns_correct_channel_name(): void
    {
        $provider = new PushProvider();
        $this->assertEquals('Push Notification', $provider->getChannelName());
    }

    public function test_provider_returns_success_on_send(): void
    {
        Log::shouldReceive('info')->once();
        
        $provider = new SmsProvider();
        
        $data = new NotificationData(
            batchId: 'uuid-1', userId: 1, userName: 'J', userEmail: 'j@e.com',
            category: 'Finance', channel: 'SMS', message: 'Hi'
        );

        $result = $provider->send($data);
        $this->assertTrue($result);
    }

    public function test_all_three_providers_have_distinct_channel_names(): void
    {
        $names = [
            (new SmsProvider())->getChannelName(),
            (new EmailProvider())->getChannelName(),
            (new PushProvider())->getChannelName(),
        ];

        $this->assertCount(3, array_unique($names), 'Each provider must have a unique channel name.');
    }

    public function test_provider_returns_false_on_delivery_failure(): void
    {
        Log::shouldReceive('info')->zeroOrMoreTimes();

        // SmsProvider's deliver() always returns true in a test environment,
        // so we test via a partial mock that forces deliver() to return false
        $provider = $this->getMockBuilder(SmsProvider::class)
            ->onlyMethods(['deliver'])
            ->getMock();

        $provider->method('deliver')->willReturn(false);

        $data = new NotificationData(
            batchId: 'uuid-2', userId: 1, userName: 'J', userEmail: 'j@e.com',
            category: 'Finance', channel: 'SMS', message: 'Hi'
        );

        $result = $provider->send($data);
        $this->assertFalse($result);
    }
}
