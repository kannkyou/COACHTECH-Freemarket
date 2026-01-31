<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_required_message_is_shown()
    {
        $res = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $res->assertSessionHasErrors(['email']);
        $this->assertTrue(in_array('メールアドレスを入力してください', session('errors')->get('email')));
    }

    public function test_password_required_message_is_shown()
    {
        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
        $this->assertTrue(in_array('パスワードを入力してください', session('errors')->get('password')));
    }

    public function test_login_failed_message_is_shown()
    {
        $res = $this->from('/login')->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $res->assertRedirect('/login');
        $res->assertSessionHasErrors('email');
        $this->assertTrue(in_array('ログイン情報が登録されていません', session('errors')->get('email')));
    }

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'a@example.com',
            'password' => Hash::make('password123'),
        ]);

        $res = $this->post('/login', [
            'email' => 'a@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $res->assertRedirect('/'); // Fortify::redirects('login','/') を想定
    }
}
