<?php

namespace Tests\Feature\Mypage;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_user_info_and_lists()
    {
        $user = User::factory()->create([
            'name' => 'taro',
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => 'Tokyo',
        ]);

        $sellItem = Item::factory()->create([
            'seller_id' => $user->id,
            'title' => '出品商品A',
        ]);

        $buyItem = Item::factory()->create([
            'title' => '購入商品B',
        ]);

        $this->actingAs($user)
            ->get('/mypage')
            ->assertOk()
            ->assertSee('taro')
            ->assertSee('出品商品A');
    }

    public function test_profile_edit_form_has_initial_values()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => 'Initial Address',
            'name' => 'initial name',
        ]);

        $res = $this->actingAs($user)->get('/mypage/profile');

        $res->assertOk();

        $res->assertSee('value="initial name"', false);
        $res->assertSee('value="1234567"', false);
        $res->assertSee('value="Initial Address"', false);
    }
}
