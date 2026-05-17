<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\Admin;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase; 

     public function test_email_is_required_for_login()
    {
        $response = $this->from('/admin/login')->post('/login', [
            'email' => '',
            'password' => 'password1234',
            'type' =>'admin',
        ]);
    
        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['email']);

        $response = $this->followRedirects($response);
        $response->assertSee('メールアドレスを入力してください');
    }

    public function test_password_is_required_for_login()
    {
        $response = $this->from('/admin/login')->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
            'type' =>'admin',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors(['password']);

        $response = $this->followRedirects($response);
        $response->assertSee('パスワードを入力してください');
    }

     public function test_user_cannot_login_with_invalid_credentials()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password1234'),
        ]);

        $response = $this->from('/admin/login')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
            'type' =>'admin',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();

        $response = $this->followRedirects($response);
        $response->assertSee('ログイン情報が登録されていません');
    }

}
