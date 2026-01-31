<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_required_message_is_shown()
    {
        $res = $this->post('/register', [
            'name' => '',
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertSessionHasErrors(['name']);
        $this->assertTrue(
            in_array('お名前を入力してください', session('errors')->get('name')),
            'expected message not found'
        );
    }

    public function test_email_required_message_is_shown()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $res->assertSessionHasErrors(['email']);
        $this->assertTrue(in_array('メールアドレスを入力してください', session('errors')->get('email')));
    }

    public function test_password_required_message_is_shown()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $res->assertSessionHasErrors(['password']);
        $this->assertTrue(in_array('パスワードを入力してください', session('errors')->get('password')));
    }

    public function test_password_min_message_is_shown()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $res->assertSessionHasErrors(['password']);
        $this->assertTrue(in_array('パスワードは8文字以上で入力してください', session('errors')->get('password')));
    }

    public function test_password_confirmed_message_is_shown()
    {
        $res = $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $res->assertSessionHasErrors(['password']);
        $this->assertTrue(in_array('パスワードと一致しません', session('errors')->get('password')));
    }

    public function test_user_without_profile_is_redirected_to_profile()
    {
        $this->post('/register', [
            'name' => 'taro',
            'email' => 'a@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = \App\Models\User::first();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/mypage/profile');
    }
}
