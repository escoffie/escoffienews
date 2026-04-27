<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected bool $useDefaultAuth = true;

    protected function setUp(): void
    {
        parent::setUp();
        
        if ($this->useDefaultAuth) {
            $token = env('ADMIN_TOKEN', 'escoffie_secret_2026');
            $this->withHeader('Authorization', $token);
        }
    }
}
