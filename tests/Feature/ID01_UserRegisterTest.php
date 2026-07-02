<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ID01_UserRegisterTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:1
    public function test_name_is_required_for_registration()
    {

        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);

        $response = $this->followRedirects($response);
        $response->assertSee('お名前を入力してください');
    }

    public function test_email_is_required_for_registration()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '一般ユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);

        $response = $this->followRedirects($response);
        $response->assertSee('メールアドレスを入力してください');
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '一般ユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $response = $this->followRedirects($response);
        $response->assertSee('パスワードは8文字以上で入力してください');
    }

    public function test_password_confirmation_must_match(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '一般ユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'errors123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);

        $response = $this->followRedirects($response);
        $response->assertSee('パスワードと一致しません');
    }

    public function test_user_can_register_successfully(): void
    {
        $response = $this->post('/register', [
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('attendance');

        $this->assertDatabaseHas('users', [
            'name' => '一般ユーザー',
            'email' => 'user@example.com',
        ]);

        $this->assertAuthenticated();
    }
}
