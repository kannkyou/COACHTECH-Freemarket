<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;

class MypageController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with('images')
            ->latest()
            ->get();

        return view('index', [
            'items' => $items,
        ]);
    }

    public function editProfile(Request $request)
    {
        return view('mypage.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:7'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'user_image'  => ['nullable', 'image', 'max:2048'],
        ]);

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