<?php

namespace Tests\Unit\Repositories;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function test_it_can_get_subscribers_by_category(): void
    {
        // 1. Create categories
        $sports = Category::create(['name' => CategoryType::SPORTS->value]);
        $finance = Category::create(['name' => CategoryType::FINANCE->value]);

        // 2. Create users and attach categories
        $user1 = User::create(['name' => 'User 1', 'email' => 'u1@example.com', 'password' => 'pwd', 'phone' => '111']);
        $user1->categories()->attach($sports->id);

        $user2 = User::create(['name' => 'User 2', 'email' => 'u2@example.com', 'password' => 'pwd', 'phone' => '222']);
        $user2->categories()->attach($sports->id);
        $user2->categories()->attach($finance->id);

        // 3. Act
        $subscribers = $this->repository->getSubscribersByCategory(CategoryType::SPORTS->value);

        // 4. Assert
        $this->assertCount(2, $subscribers);
        $this->assertTrue($subscribers->contains($user1));
        $this->assertTrue($subscribers->contains($user2));
    }

    public function test_it_returns_empty_collection_if_no_subscribers(): void
    {
        $subscribers = $this->repository->getSubscribersByCategory(CategoryType::MOVIES->value);
        $this->assertCount(0, $subscribers);
    }
}
