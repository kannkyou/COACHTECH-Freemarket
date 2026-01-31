<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;

class PurchaseController extends Controller
{
    public function create(Request $request, Item $item)
    {
        if ((int)$item->seller_id === (int)$request->user()->id) {
            return redirect()->route('items.show', $item->id)
                ->with('status', '自分が出品した商品は購入できません');
        }

        $item->load('images');

        $key = "purchase.shipping.{$item->id}";
        $shipping = $request->session()->get($key, [
            'postal_code' => $request->user()->postal_code,
            'address'     => $request->user()->address,
            'building'    => $request->user()->building,
        ]);

        return view('purchase.purchase', [
            'item'     => $item,
            'shipping' => $shipping,
        ]);
    }

    /**
     * Stripe決済へ遷移（Checkout Session作成 -> リダイレクト）
     */
    public function store(Request $request, Item $item)
    {
        if ((int)$item->seller_id === (int)$request->user()->id) {
            abort(403, '自分が出品した商品は購入できません');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:1,2'],
        ]);

        $user = $request->user();

        $key = "purchase.shipping.{$item->id}";
        $shipping = $request->session()->get($key, [
            'postal_code' => $user->postal_code,
            'address'     => $user->address,
            'building'    => $user->building,
        ]);

        // Stripe初期化（config/services.phpに stripe.secret を設定しておく）
        Stripe::setApiKey(config('services.stripe.secret'));

        $paymentMethodTypes = ((int)$validated['payment_method'] === 1)
            ? ['konbini']
            : ['card'];

        return DB::transaction(function () use ($request, $item, $user, $shipping, $validated, $paymentMethodTypes, $key) {

            // 未決済の注文を作る
            $order = Order::create([
                'buyer_id'              => $user->id,
                'seller_id'             => $item->seller_id,
                'item_id'               => $item->id,
                'status'                => 1, // 仮：未決済/受付 など
                'item_price'            => $item->price,
                'payment_method'        => (int)$validated['payment_method'],
                'shipping_postal_code'  => $shipping['postal_code'],
                'shipping_address'      => $shipping['address'],
                'shipping_building'     => $shipping['building'],
                'paid_at'               => null,
            ]);

            $session = CheckoutSession::create([
                'mode' => 'payment',
                'payment_method_types' => $paymentMethodTypes,

                // 商品情報
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->title,
                        ],
                        'unit_amount' => (int) $item->price,
                    ],
                    'quantity' => 1,
                ]],

                // どの注文の決済か紐付け
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'item_id'  => (string) $item->id,
                    'buyer_id' => (string) $user->id,
                ],

                'success_url' => route('purchase.success', $item->id) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => route('purchase.create', $item->id),
            ]);

            // ordersテーブルのstripe_session_id カラムで照合）
            $order->stripe_session_id = $session->id;
            $order->save();

            // セッションの配送情報は「決済完了後に確定」したら消す
            // ここではまだ消さない（キャンセルで戻る可能性があるため）

            return redirect()->away($session->url);
        });
    }

    /**
     * Stripe決済完了後に戻ってきた先
     */
    public function success(Request $request, Item $item)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('purchase.create', $item->id)
                ->with('status', '決済セッションが取得できませんでした');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = CheckoutSession::retrieve($sessionId);

        // 支払い完了
        if (($session->payment_status ?? null) !== 'paid') {
            return redirect()->route('purchase.create', $item->id)
                ->with('status', '決済が完了していません');
        }

        $orderId = $session->metadata->order_id ?? null;
        if (!$orderId) {
            return redirect()->route('purchase.create', $item->id)
                ->with('status', '注文情報が取得できませんでした');
        }

        $key = "purchase.shipping.{$item->id}";

        DB::transaction(function () use ($orderId, $item, $key, $request) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // 二重確定防止
            if ($order->paid_at === null) {
                $order->paid_at = now();
                $order->status = 2;
                $order->save();

                // 商品を売却済みに
                $item->status = 2;
                $item->save();
            }

            // 配送情報セッションの削除
            $request->session()->forget($key);
        });

        return redirect()->route('items.index')->with('purchased', true);
    }
}
