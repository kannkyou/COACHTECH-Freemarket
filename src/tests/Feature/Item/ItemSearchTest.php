<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_can_be_searched_by_partial_title()
    {
        Item::factory()->create(['title'=>'Apple Watch']);
        Item::factory()->create(['title'=>'Galaxy']);

        $this->get('/?keyword=Apple')
            ->assertSee('Apple Watch')
            ->assertDontSee('Galaxy');
    }

    public function test_search_keyword_is_kept_in_mylist()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create(['title'=>'MacBook']);

        $user->mylistItems()->attach($item);

        $this->actingAs($user)
            ->get('/?tab=mylist&keyword=Mac')
            ->assertSee('MacBook');
    }
}
