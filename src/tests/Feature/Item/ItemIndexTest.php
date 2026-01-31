<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_items_are_displayed()
    {
        Item::factory()->count(3)->create();

        $this->get('/')
            ->assertOk()
            ->assertSee(Item::first()->title);
    }

    public function test_sold_items_show_sold_label()
    {
        $item = Item::factory()->create([
            'status' => 2 // 売却済み
        ]);

        $this->get('/')
            ->assertSee('SOLD');
    }

    public function test_users_own_items_are_hidden()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $myItem = Item::factory()->create([
            'seller_id' => $user->id
        ]);

        Item::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertDontSee($myItem->title);
    }
}
