<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_users(): void
    {
        User::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'p', 'phone' => '111']);
        User::create(['name' => 'Bob', 'email' => 'bob@example.com', 'password' => 'p', 'phone' => '222']);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Alice'])
            ->assertJsonFragment(['name' => 'Bob']);
    }

    public function test_it_returns_empty_array_when_no_users(): void
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_it_can_create_a_user_with_categories_and_channels(): void
    {
        Category::create(['name' => 'Finance']);
        Channel::create(['name' => 'SMS']);

        $response = $this->postJson('/api/users', [
            'name'       => 'Jane Doe',
            'email'      => 'jane@example.com',
            'phone'      => '+1234567890',
            'categories' => ['Finance'],
            'channels'   => ['SMS'],
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Jane Doe'])
            ->assertJsonFragment(['email' => 'jane@example.com']);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertCount(1, $user->categories);
        $this->assertCount(1, $user->channels);
    }

    public function test_it_rejects_user_creation_with_missing_required_fields(): void
    {
        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'categories', 'channels']);
    }

    public function test_it_rejects_duplicate_email(): void
    {
        Category::create(['name' => 'Sports']);
        Channel::create(['name' => 'SMS']);
        User::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'p', 'phone' => '111']);

        $response = $this->postJson('/api/users', [
            'name'       => 'Alice Clone',
            'email'      => 'alice@example.com',
            'phone'      => '999',
            'categories' => ['Sports'],
            'channels'   => ['SMS'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_rejects_non_existent_categories(): void
    {
        Channel::create(['name' => 'SMS']);

        $response = $this->postJson('/api/users', [
            'name'       => 'Ghost',
            'email'      => 'ghost@example.com',
            'phone'      => '000',
            'categories' => ['NonExistentCategory'],
            'channels'   => ['SMS'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['categories.0']);
    }

    public function test_it_rejects_non_existent_channels(): void
    {
        Category::create(['name' => 'Finance']);

        $response = $this->postJson('/api/users', [
            'name'       => 'Ghost',
            'email'      => 'ghost@example.com',
            'phone'      => '000',
            'categories' => ['Finance'],
            'channels'   => ['Fax'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['channels.0']);
    }
}
