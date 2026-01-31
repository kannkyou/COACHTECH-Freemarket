<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $item = Item::factory()->create([
            'seller_id' => $seller->id,
        ]);

        return [
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $item->id,

            'status' => 2, // 購入完了
            'item_price' => $this->faker->numberBetween(1000, 10000),
            'payment_method' => 1,

            'shipping_postal_code' => '1234567',
            'shipping_address' => '東京都テスト1-1-1',
            'shipping_building' => 'テストビル101',

            'stripe_session_id' => null,
            'paid_at' => now(),
        ];
    }
}
