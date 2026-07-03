<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ID16_MailVerifyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_test(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
