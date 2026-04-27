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

    public function test_it_returns_empty_array_when_no_categories_exist(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(0)
            ->assertExactJson([]);
    }

    public function test_it_returns_only_id_and_name_fields(): void
    {
        Category::create(['name' => 'Finance']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);

        $category = $response->json()[0];
        $this->assertArrayHasKey('id', $category);
        $this->assertArrayHasKey('name', $category);
        // Timestamps should not be exposed in the category list
        $this->assertArrayNotHasKey('created_at', $category);
        $this->assertArrayNotHasKey('updated_at', $category);
    }

    public function test_it_returns_all_three_seeded_categories(): void
    {
        Category::create(['name' => 'Sports']);
        Category::create(['name' => 'Finance']);
        Category::create(['name' => 'Movies']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonFragment(['name' => 'Sports'])
            ->assertJsonFragment(['name' => 'Finance'])
            ->assertJsonFragment(['name' => 'Movies']);
    }

    public function test_category_names_are_strings(): void
    {
        Category::create(['name' => 'Sports']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200);

        $name = $response->json()[0]['name'];
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }
}
