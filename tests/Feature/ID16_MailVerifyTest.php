<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\VerifyEmail;
use App\Models\User;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use Tests\TestCase;

class ID16_MailVerifyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_test(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
