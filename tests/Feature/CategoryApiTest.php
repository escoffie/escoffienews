<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_fetch_categories(): void
    {
        Category::create(['name' => 'Tech']);
        Category::create(['name' => 'Health']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2)
            ->assertJsonFragment(['name' => 'Tech'])
            ->assertJsonFragment(['name' => 'Health']);
    }
}
