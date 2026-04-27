<?php

namespace Tests\Unit\Repositories;

use App\DTOs\NotificationData;
use App\Models\User;
use App\Repositories\Eloquent\NotificationLogRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\NotificationStatus;
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
            batchId: 'uuid-1',
            userId: $user->id,
            userName: $user->name,
            userEmail: $user->email,
            category: 'Finance',
            channel: 'SMS',
            message: 'Hello'
        );

        // 3. Act
        $log = $this->repository->log($data, 2, NotificationStatus::FAILED);

        // 4. Assert
        $this->assertDatabaseHas('notification_logs', [
            'id' => $log->id,
            'batch_id' => 'uuid-1',
            'attempts' => 2,
            'status' => NotificationStatus::FAILED->value,
            'message' => 'Hello'
        ]);
    }

    public function test_it_can_get_all_logs_ordered_by_newest(): void
    {
        $user = User::create(['name' => 'John', 'email' => 'j@e.com', 'password' => 'p', 'phone' => '123']);
        
        $log1 = \App\Models\NotificationLog::create([
            'user_id' => $user->id, 'user_name' => 'J', 'user_email' => 'j@e.com',
            'category' => 'Finance', 'channel' => 'SMS', 'message' => 'First',
            'batch_id' => 'uuid-1'
        ]);

        $log2 = \App\Models\NotificationLog::create([
            'user_id' => $user->id, 'user_name' => 'J', 'user_email' => 'j@e.com',
            'category' => 'Sports', 'channel' => 'Email', 'message' => 'Second',
            'batch_id' => 'uuid-2'
        ]);

        $logs = $this->repository->getAllLogs();

        $this->assertCount(2, $logs);
        $this->assertEquals($log2->id, $logs->first()->id);
    }

    public function test_log_stores_all_required_fields(): void
    {
        $user = User::create(['name' => 'Jane', 'email' => 'jane@e.com', 'password' => 'p', 'phone' => '999']);

        $data = new NotificationData(
            batchId: 'uuid-3',
            userId: $user->id,
            userName: 'Jane',
            userEmail: 'jane@e.com',
            category: 'Sports',
            channel: 'E-Mail',
            message: 'Match starts at 8pm',
            userPhone: '999'
        );

        $log = $this->repository->log($data, 1, NotificationStatus::SENT);

        $this->assertEquals('uuid-3', $log->batch_id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals('Jane', $log->user_name);
        $this->assertEquals('jane@e.com', $log->user_email);
        $this->assertEquals('Sports', $log->category);
        $this->assertEquals('E-Mail', $log->channel);
        $this->assertEquals('Match starts at 8pm', $log->message);
        $this->assertEquals(1, $log->attempts);
        $this->assertEquals(NotificationStatus::SENT->value, $log->status);
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
            'category' => 'Finance', 'channel' => 'SMS', 'message' => 'First',
            'batch_id' => 'uuid-1'
        ]);

        $this->assertEquals(1, \App\Models\NotificationLog::count());

        $this->repository->clearAllLogs();

        $this->assertEquals(0, \App\Models\NotificationLog::count());
    }
}
