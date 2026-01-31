<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_works()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $res->assertRedirect('/'); // Fortifyの既定
    }
}
