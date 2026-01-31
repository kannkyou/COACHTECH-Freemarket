<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Requests\ShippingRequest;

class ShippingAddressController extends Controller
{
    public function edit(Request $request, Item $item)
    {
        $key = "purchase.shipping.{$item->id}";

        $shipping = $request->session()->get($key, [
            'postal_code' => $request->user()->postal_code,
            'address'     => $request->user()->address,
            'building'    => $request->user()->building,
        ]);

        return view('purchase.shipping', compact('item', 'shipping'));
    }

    public function update(ShippingRequest $request, Item $item)
    {
        $validated = $request->validated();

        $key = "purchase.shipping.{$item->id}";

        $request->session()->put($key, [
            'postal_code' => $validated['postal_code'],
            'address'     => $validated['address'],
            'building'    => $validated['building'] ?? null,
        ]);

        return redirect()->route('purchase.create', $item->id);
    }
}
