<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_after_register()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'taro',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }


    public function test_email_link_opens_verification_page()
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.confirm',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertOk()
            ->assertSee('認証はこちらから');
    }


    public function test_user_can_complete_email_verification()
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute(
            'verification.complete',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $this->actingAs($user)
            ->post($url);

        $this->assertTrue(
            $user->fresh()->hasVerifiedEmail()
        );
    }

    public function test_verified_user_without_profile_is_redirected_to_profile()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => null,
            'address' => null,
        ]);

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect('/mypage/profile');
    }
}
