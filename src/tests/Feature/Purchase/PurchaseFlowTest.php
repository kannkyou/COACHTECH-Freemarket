<?php

namespace Tests\Feature\Purchase;

use App\Http\Middleware\EnsureProfileIsCompleted;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_the_purchase(): void
    {
        $this->withoutMiddleware(EnsureProfileIsCompleted::class);

        [$buyer, $item] = $this->seedBuyerAndItem();

        $this->actingAs($buyer);

        $this->get(route('items.show', $item->id))
            ->assertOk();

        $this->post(route('purchase.create', $item->id))
            ->assertRedirect();

        $order = $this->forceCompletePurchase($buyer, $item);

        $this->assertNotNull($order->paid_at);

        $this->assertDatabaseHas('items', [
            'id'     => $item->id,
            'status' => 2,
        ]);
    }

    public function test_displayed_as_sold(): void
    {
        $this->withoutMiddleware(EnsureProfileIsCompleted::class);

        [$buyer, $item] = $this->seedBuyerAndItem();

        $this->actingAs($buyer);

        $this->post(route('purchase.create', $item->id))
            ->assertRedirect();

        $this->forceCompletePurchase($buyer, $item);

        $res = $this->get(route('items.index'))
            ->assertOk();

        $res->assertSee('SOLD');
    }

    public function test_added_to_mypage(): void
    {
        $this->withoutMiddleware(EnsureProfileIsCompleted::class);

        [$buyer, $item] = $this->seedBuyerAndItem();

        $this->actingAs($buyer);

        $this->post(route('purchase.create', $item->id))
            ->assertRedirect();

        $this->forceCompletePurchase($buyer, $item);

        $res = $this->get(route('mypage.index'))
            ->assertOk();

        $res->assertSee($item->name);
    }

    // helper

    private function seedBuyerAndItem(): array
    {
        $buyer = User::factory()->create([
            'postal_code'       => '1234567',
            'address'           => 'Tokyo',
            'email_verified_at' => now(),
        ]);

        $seller = User::factory()->create([
            'postal_code'       => '1234567',
            'address'           => 'Tokyo',
            'email_verified_at' => now(),
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'status'    => 1,
        ]);

        return [$buyer, $item];
    }

    private function forceCompletePurchase(User $buyer, Item $item): Order
    {
        $order = Order::factory()->create([
            'buyer_id'   => $buyer->id,
            'seller_id'  => $item->seller_id,
            'item_id'    => $item->id,
            'item_price' => $item->price ?? 3000,
        ]);

        $item->update([
            'status' => 2,
        ]);

        return $order;
    }
}
