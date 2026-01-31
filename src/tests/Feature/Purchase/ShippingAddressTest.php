<?php

namespace Tests\Feature\Purchase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShippingAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_address_update()
    {
        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1111111',
            'address' => 'Seller Tokyo',
        ]);

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '2222222',
            'address' => 'Buyer Tokyo',
            'building' => null,
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 1,
        ]);

        $key = "purchase.shipping.{$item->id}";

        $res = $this->actingAs($buyer)->post("/purchase/{$item->id}/shipping", [
            'postal_code' => '7654321',
            'address' => 'New Address',
            'building' => 'New Building',
        ]);

        $res->assertRedirect(route('purchase.create', $item->id));

        $res->assertSessionHas("{$key}.postal_code", '7654321');
        $res->assertSessionHas("{$key}.address", 'New Address');
        $res->assertSessionHas("{$key}.building", 'New Building');

        $this->actingAs($buyer)
            ->withSession([$key => [
                'postal_code' => '7654321',
                'address' => 'New Address',
                'building' => 'New Building',
            ]])
            ->get("/purchase/{$item->id}")
            ->assertOk();
    }

    public function test_shipping_address_is_saved_into_order()
    {
        $seller = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1111111',
            'address' => 'Seller Tokyo',
        ]);

        $buyer = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '2222222',
            'address' => 'Buyer Tokyo',
        ]);

        $item = Item::factory()->create([
            'seller_id' => $seller->id,
            'status' => 1,
        ]);

        $key = "purchase.shipping.{$item->id}";
        $shipping = [
            'postal_code' => '3333333',
            'address' => 'Order Address',
            'building' => 'Order Building',
        ];

        $this->actingAs($buyer)
            ->withSession([$key => $shipping])
            ->post("/purchase/{$item->id}", ['payment_method' => '1'])
            ->assertStatus(302);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
            'payment_method' => 1,
            'shipping_postal_code' => '3333333',
            'shipping_address' => 'Order Address',
            'shipping_building' => 'Order Building',
        ]);

        $order = Order::where('item_id', $item->id)->firstOrFail();
        $this->assertNotNull($order->stripe_session_id);
        $this->assertNull($order->paid_at);
    }
}
