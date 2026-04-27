<?php

namespace Tests\Unit\Repositories;

use App\DTOs\NotificationData;
use App\Models\User;
use App\Repositories\Eloquent\NotificationLogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationLogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new NotificationLogRepository();
    }

    public function test_it_can_store_a_log(): void
    {
        // 1. Create a user for FK
        $user = User::create(['name' => 'John', 'email' => 'j@e.com', 'password' => 'p', 'phone' => '123']);

        // 2. Create DTO
        $data = new NotificationData(
            userId: $user->id,
            userName: $user->name,
            userEmail: $user->email,
            category: 'Finance',
            channel: 'SMS',
            message: 'Hello'
        );

        // 3. Act
        $log = $this->repository->log($data);

        // 4. Assert
        $this->assertDatabaseHas('notification_logs', [
            'id' => $log->id,
            'message' => 'Hello'
        ]);
    }

    public function test_it_can_get_all_logs_ordered_by_newest(): void
    {
        $user = User::create(['name' => 'John', 'email' => 'j@e.com', 'password' => 'p', 'phone' => '123']);
        
        $log1 = \App\Models\NotificationLog::create([
            'user_id' => $user->id, 'user_name' => 'J', 'user_email' => 'j@e.com',
            'category' => 'Finance', 'channel' => 'SMS', 'message' => 'First'
        ]);

        $log2 = \App\Models\NotificationLog::create([
            'user_id' => $user->id, 'user_name' => 'J', 'user_email' => 'j@e.com',
            'category' => 'Sports', 'channel' => 'Email', 'message' => 'Second'
        ]);

        $logs = $this->repository->getAllLogs();

        $this->assertCount(2, $logs);
        $this->assertEquals($log2->id, $logs->first()->id); // Latest first (by ID if same timestamp)
    }

    public function test_log_stores_all_required_fields(): void
    {
        $user = User::create(['name' => 'Jane', 'email' => 'jane@e.com', 'password' => 'p', 'phone' => '999']);

        $data = new NotificationData(
            userId: $user->id,
            userName: 'Jane',
            userEmail: 'jane@e.com',
            category: 'Sports',
            channel: 'E-Mail',
            message: 'Match starts at 8pm',
            userPhone: '999'
        );

        $log = $this->repository->log($data);

        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals('Jane', $log->user_name);
        $this->assertEquals('jane@e.com', $log->user_email);
        $this->assertEquals('Sports', $log->category);
        $this->assertEquals('E-Mail', $log->channel);
        $this->assertEquals('Match starts at 8pm', $log->message);
        $this->assertNotNull($log->created_at);
    }

    public function test_get_all_logs_returns_empty_collection_when_no_logs(): void
    {
        $logs = $this->repository->getAllLogs();
        $this->assertCount(0, $logs);
    }

    public function test_it_can_clear_all_logs(): void
    {
        $user = User::create(['name' => 'John', 'email' => 'j@e.com', 'password' => 'p', 'phone' => '123']);
        \App\Models\NotificationLog::create([
            'user_id' => $user->id, 'user_name' => 'J', 'user_email' => 'j@e.com',
            'category' => 'Finance', 'channel' => 'SMS', 'message' => 'First'
        ]);

        $this->assertEquals(1, \App\Models\NotificationLog::count());

        $this->repository->clearAllLogs();

        $this->assertEquals(0, \App\Models\NotificationLog::count());
    }
}
