<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class MylistController extends Controller
{
    public function toggle(Request $request, Item $item)
    {
        $user = $request->user();

        // すでに登録済みか？
        $already = $user->mylistItems()
            ->where('items.id', $item->id)
            ->exists();

        if ($already) {
            $user->mylistItems()->detach($item->id);
            $isFavorited = false;
        } else {
            $user->mylistItems()->attach($item->id);
            $isFavorited = true;
        }

        // 最新の件数を返す
        $count = $item->favoritedUsers()->count();

        return response()->json([
            'isFavorited' => $isFavorited,
            'count' => $count,
        ]);
    }
}