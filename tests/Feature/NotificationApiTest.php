<?php

namespace Tests\Feature;

use App\Enums\CategoryType;
use App\Events\MessageReceived;
use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_validates_notification_request(): void
    {
        $response = $this->postJson('/api/notifications', [
            'category' => 'InvalidCategory',
            'message' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category', 'message']);
    }

    public function test_it_receives_notification_and_dispatches_event(): void
    {
        Event::fake();

        $response = $this->postJson('/api/notifications', [
            'category' => CategoryType::FINANCE->value,
            'message' => 'Your bill is ready.'
        ]);

        $response->assertStatus(202)
            ->assertJson(['message' => 'Notification request received and queued for delivery.']);

        Event::assertDispatched(MessageReceived::class, function ($event) {
            return $event->category === CategoryType::FINANCE->value &&
                   $event->message === 'Your bill is ready.';
        });
    }

    public function test_it_can_retrieve_notification_logs(): void
    {
        // 1. Create a user to satisfy FK constraint
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '12345'
        ]);

        // 2. Create dummy logs
        NotificationLog::create([
            'user_id' => $user->id,
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
            'category' => 'Finance',
            'channel' => 'Email',
            'message' => 'Test message'
        ]);

        // 3. Fetch via API
        $response = $this->getJson('/api/logs');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['user_name' => 'John Doe']);
    }
}
