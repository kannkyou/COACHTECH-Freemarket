<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Item;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_details_are_displayed()
    {
        $item = Item::factory()->create([
            'title'=>'Test Item',
            'price'=>1000,
            'description'=>'desc'
        ]);

        $this->get("/item/{$item->id}")
            ->assertSee('Test Item')
            ->assertSee('￥1,000')
            ->assertSee('desc');
    }
}
