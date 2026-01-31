<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_favorited_items_are_displayed()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $fav = Item::factory()->create();
        $notFav = Item::factory()->create();

        $user->mylistItems()->attach($fav);

        $this->actingAs($user)
            ->get('/?tab=mylist')
            ->assertSee($fav->title)
            ->assertDontSee($notFav->title);
    }

    public function test_sold_label_is_visible_in_mylist()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create([
            'status'=>2
        ]);

        $user->mylistItems()->attach($item);

        $this->actingAs($user)
            ->get('/?tab=mylist')
            ->assertSee('SOLD');
    }

    public function test_guest_cannot_view_mylist()
    {
        $this->get('/?tab=mylist')
            ->assertRedirect('/login');
    }
}
