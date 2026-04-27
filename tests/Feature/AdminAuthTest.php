<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;
    protected bool $useDefaultAuth = false;

    public function test_it_rejects_requests_without_token(): void
    {
        $response = $this->getJson('/api/categories');
        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized. Invalid admin token.']);
    }

    public function test_it_rejects_requests_with_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'wrong-token')
                         ->getJson('/api/categories');
        
        $response->assertStatus(401)
                 ->assertJson(['message' => 'Unauthorized. Invalid admin token.']);
    }

    public function test_it_accepts_requests_with_valid_token(): void
    {
        $token = env('ADMIN_TOKEN', 'escoffie_secret_2026');
        
        $response = $this->withHeader('Authorization', $token)
                         ->getJson('/api/categories');
        
        $response->assertStatus(200);
    }
}
