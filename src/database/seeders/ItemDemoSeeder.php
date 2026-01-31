<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;

class ItemDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seller = User::first() ?? User::factory()->create([
            'name' => 'demo_seller',
            'email' => 'demo@example.com',
            'postal_code' => '1111111',
            'address' => 'demo',
        ]);

        $items = [
            [
                'title' => '腕時計',
                'brand_name' => 'Rolax',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => 1,
                'categories' => ['メンズ', 'アクセサリー'],
                'image_files' => ['Rolax+Clock+15000+1.jpg'],
            ],
            [
                'title' => 'HDD',
                'brand_name' => '西芝',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => 2,
                'categories' => ['家電'],
                'image_files' => ['Seishiba+HDD+5000+2.jpg'],
            ],
            [
                'title' => '玉ねぎ3束',
                'brand_name' => null,
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 3,
                'categories' => ['ハンドメイド', 'キッチン'],
                'image_files' => ['Onion+300+3.jpg'],
            ],
            [
                'title' => '革靴',
                'brand_name' => null,
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'condition' => 4,
                'categories' => ['メンズ', 'ファッション'],
                'image_files' => ['LeatherShoes+4000+4.jpg'],
            ],
            [
                'title' => 'ノートPC',
                'brand_name' => null,
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'condition' => 1,
                'categories' => ['家電', 'ゲーム'],
                'image_files' => ['Laptop+45000+1.jpg'],
            ],
            [
                'title' => 'マイク',
                'brand_name' => null,
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'condition' => 2,
                'categories' => ['家電', 'スポーツ', 'ゲーム'],
                'image_files' => ['Mike+8000+2.jpg'],
            ],
            [
                'title' => 'ショルダーバッグ',
                'brand_name' => null,
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 3,
                'categories' => ['レディース', 'ファッション'],
                'image_files' => ['Shoulderbag+3500+3.jpg'],
            ],
            [
                'title' => 'タンブラー',
                'brand_name' => null,
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'condition' => 4,
                'categories' => ['キッチン'],
                'image_files' => ['Tumbler+500+4.jpg'],
            ],
            [
                'title' => 'コーヒーミル',
                'brand_name' => 'Starbacks',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'condition' => 1,
                'categories' => ['キッチン', 'インテリア'],
                'image_files' => ['Starbacks+Coffeegrinder+4000+1.jpg'],
            ],
            [
                'title' => 'メイクセット',
                'brand_name' => null,
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'condition' => 2,
                'categories' => ['レディース', 'コスメ'],
                'image_files' => ['Makeupset+2500+2.jpg'],
            ],
        ];
        // 画像のコピー元（プロジェクト内）
        $srcDir = database_path('seeders/assets/items');

        // 画像のコピー先（publicディスク）
        $dstDir = 'item_images/seed';

        foreach ($items as $row) {
            $item = Item::create([
                'seller_id' => $seller->id,
                'status' => 1,
                'title' => $row['title'],
                'brand_name' => $row['brand_name'],
                'price' => $row['price'],
                'description' => $row['description'],
                'condition' => $row['condition'],
            ]);

            $categoryIds = Category::whereIn('name', $row['categories'])->pluck('id')->all();
            $item->categories()->sync($categoryIds);

            // 画像コピー
            foreach ($row['image_files'] as $file) {
                $from = $srcDir . DIRECTORY_SEPARATOR . $file;
                $to   = $dstDir . '/' . $file;

                Storage::disk('public')->put($to, file_get_contents($from));

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_url' => $to, // asset('storage/'.$to)
                ]);
            }
        }
    }
}
