<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;


class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab');
        $keyword = trim((string) $request->query('keyword', ''));

        $query = Item::with('images')
            ->whereIn('status', [1, 2])
            ->latest();

        // 自分の出品物は見えないように
        if (auth()->check()) {
            $query->where('seller_id', '!=', auth()->id());
        }

        // マイリスト
        if ($tab === 'mylist') {
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            $userId = auth()->id();

            $query->whereHas('favoritedUsers', function ($q) use ($userId) {
                $q->where('users.id', $userId);
            });
        }

        // 検索（商品名 title 部分一致）
        if ($keyword !== '') {
            $query->where('title', 'like', "%{$keyword}%");
        }

        $items = $query->get();

        return view('index', compact('items', 'tab', 'keyword'));
    }
    
    public function show(Item $item)
    {
        $item->load(['images', 'categories'])
            ->loadCount('favoritedUsers');

        $favoriteCount = (int) $item->favorited_users_count;

        $user = auth()->user();
        $isFavorited = $user
            ? $user->mylistItems()->where('items.id', $item->id)->exists()
            : false;

        $comments = $item->comments()
            ->with('user')
            ->latest()
            ->get();

        return view('items.show', compact('item', 'comments', 'isFavorited', 'favoriteCount'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('items.sell', compact('categories'));
    }

    public function store(ExhibitionRequest $request)
    {
        $data = $request->validated();

        $item = Item::create([
            'seller_id'   => $request->user()->id,
            'status'      => 1, // 出品中
            'title'       => $data['title'],
            'brand_name'  => $data['brand_name'],
            'price'       => $data['price'],
            'description' => $data['description'],
            'condition'   => $data['condition'],
        ]);

        $item->categories()->sync($data['category_ids']);

        foreach ($request->file('images') as $image) {
            $path = $image->store('item_images', 'public');

            ItemImage::create([
                'item_id'   => $item->id,
                'image_url' => $path,
            ]);
        }

        return redirect()
            ->route('items.index')
            ->with('status', '出品しました');
    }
}
