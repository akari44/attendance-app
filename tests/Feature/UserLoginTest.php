<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase; 
    
    public function test_email_is_required_for_login()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password1234',
        ]);
    
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email']);

        $response = $this->followRedirects($response);
        $response->assertSee('メールアドレスを入力してください');
    }

    public function test_password_is_required_for_login()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password']);

        $response = $this->followRedirects($response);
        $response->assertSee('パスワードを入力してください');
    }

     public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password1234'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();

        $response = $this->followRedirects($response);
        $response->assertSee('ログイン情報が登録されていません');
    }

}
