<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class ID16_MailVerifyTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_verification_email_is_sent(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'user1',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);

    }

    public function test_verify_email_page_has_mail_link(): void
    {
        $user = User::factory()->create(
            [
                'email_verified_at' => null,
            ]
        );
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertStatus(200)
            ->assertSee('mailto:');

    }

    public function test_verified_user_is_redirected_to_attendance_page(): void
    {
        $user = User::factory()->create(
            [
                'email_verified_at' => null,
            ]
        );
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $response = $this->actingAs($user)->get($verificationUrl);
        $response->assertRedirect('/attendance?verified=1');
    }
}
