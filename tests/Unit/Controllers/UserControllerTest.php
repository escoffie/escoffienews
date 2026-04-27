<?php

namespace Tests\Unit\Controllers;

use App\Models\Category;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_fetch_users()
    {
        $user = User::create(['name' => 'John', 'email' => 'john@example.com', 'phone' => '123456', 'password' => bcrypt('password')]);
        
        $response = $this->getJson('/api/users');
        
        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'John']);
    }

    public function test_it_can_create_user_with_categories_and_channels()
    {
        $category = Category::create(['name' => 'Finance']);
        $channel = Channel::create(['name' => 'SMS']);

        $response = $this->postJson('/api/users', [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'phone' => '987654',
            'categories' => ['Finance'],
            'channels' => ['SMS']
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Jane']);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertTrue($user->categories->contains($category));
        $this->assertTrue($user->channels->contains($channel));
    }
}
