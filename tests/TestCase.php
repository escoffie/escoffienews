<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $token = env('ADMIN_TOKEN', 'escoffie_secret_2026');
        $this->withHeader('Authorization', $token);
    }
}
