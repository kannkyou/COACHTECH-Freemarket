<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    public function index(Request $request) //マイページ
    {
        $user = $request->user();
        $tab = $request->query('tab', 'sell'); // sell / buy

        // 出品した商品
        $sellingItems = $user->sellingItems()
            ->with('images')
            ->latest()
            ->get();

        // 購入した商品
        $buyOrders = $user->buyingOrders()
            ->with(['item.images'])
            ->latest()
            ->get();

        return view('mypage.mypage', compact('user', 'tab', 'sellingItems', 'buyOrders'));
    }

    public function editProfile(Request $request)
    {
        return view('mypage.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(ProfileRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        //旧画像パスを保持
        $oldImagePath = $user->user_image;

        //新しい画像がアップロードされた場合に新画像を保管して旧画像削除
        if ($request->hasFile('user_image')) {
            $path = $request->file('user_image')->store('user_images', 'public');
            $user->user_image = $path;
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }

        $user->name        = $data['name'];
        $user->postal_code = $data['postal_code'];
        $user->address     = $data['address'];
        $user->building    = $data['building'] ?? null;

        $user->save();

        return redirect()
            ->route('mypage.profile.edit')
            ->with('status', '更新されました');
    }
}