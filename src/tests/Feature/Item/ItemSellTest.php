<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemSellTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_with_required_fields()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '1234567',
            'address' => 'Tokyo',
        ]);

        $category = Category::create([
            'name' => 'テストカテゴリ',
        ]);

        $payload = [
            'title' => 'TEST',
            'brand_name' => 'TEST',
            'price' => 1000,
            'description' => 'TEST',
            'condition' => 2,
            'category_ids' => [$category->id],
            'images' => [
                UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg'),
            ],
        ];

        $res = $this->actingAs($user)->post('/sell', $payload);

        $res->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'seller_id' => $user->id,
            'status' => 1,
            'title' => 'TEST',
            'brand_name' => 'TEST',
            'price' => 1000,
            'description' => 'TEST',
            'condition' => 2,
        ]);

        $item = Item::where('title', 'TEST')->firstOrFail();

        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('item_images', [
            'item_id' => $item->id,
        ]);

        $img = ItemImage::where('item_id', $item->id)->firstOrFail();
        Storage::disk('public')->assertExists($img->image_url);
    }
}
