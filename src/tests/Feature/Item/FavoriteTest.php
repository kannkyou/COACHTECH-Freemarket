<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_item()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create();

        $this->actingAs($user)
            ->post("/items/{$item->id}/mylist/toggle");

        $this->assertDatabaseHas('mylist', [
            'user_id'=>$user->id,
            'item_id'=>$item->id
        ]);
    }

    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create([
            'postal_code'=>'123',
            'address'=>'Tokyo',
            'email_verified_at'=>now()
        ]);

        $item = Item::factory()->create();

        $user->mylistItems()->attach($item);

        $this->actingAs($user)
            ->post("/items/{$item->id}/mylist/toggle");

        $this->assertDatabaseMissing('mylist', [
            'user_id'=>$user->id,
            'item_id'=>$item->id
        ]);
    }
}
