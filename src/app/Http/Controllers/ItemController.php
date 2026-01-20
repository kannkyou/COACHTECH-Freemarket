<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemImage;


class ItemController extends Controller
{
     public function index()
    {
        $items = Item::with('images')
            ->where('status', 1) // 出品中のみ
            ->latest()
            ->get();
        return view('index', compact('items'));
    }
    
    public function show(Item $item)
    {
        $item->load(['images', 'categories']);
        $favoriteCount = $item->favoritedUsers()->count();

        $user = auth()->user();
        $isFavorited = false;
        if ($user) {
            $isFavorited = $user->mylistItems()
                ->where('items.id', $item->id)
                ->exists();
        }

        $comments = collect();
        return view('items.show', compact('item', 'comments' ,'isFavorited', 'favoriteCount'));
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
