@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/purchase.css') }}">
@endsection

@section('content')
@php
    $mainImage = $item->images->isNotEmpty()
        ? asset('storage/' . $item->images->first()->image_url)
        : null;
@endphp

<div class="purchase">
    <div class="purchase__inner">

        <div class="purchase__grid">
            <div class="purchase__left">
                <div class="purchase-item">
                    <div class="purchase-item__thumb">
                        @if ($mainImage)
                            <img src="{{ $mainImage }}" alt="{{ $item->title }}">
                        @else
                            <div class="purchase-item__placeholder">商品画像</div>
                        @endif
                    </div>

                    <div class="purchase-item__meta">
                        <div class="purchase-item__title">{{ $item->title }}</div>
                        <div class="purchase-item__price">¥ {{ number_format($item->price) }}</div>
                    </div>
                </div>

                <div class="purchase-section">
                    <div class="purchase-section__head">支払い方法</div>

                    <select class="purchase-select" name="payment_method" id="paymentMethod">
                        <option value="" selected disabled>選択してください</option>
                        <option value="1">コンビニ支払い</option>
                        <option value="2">カード支払い</option>
                    </select>

                    @error('payment_method')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

            <div class="purchase-section">
                <div class="purchase-section__head purchase-section__head--row">
                    <span>配送先</span>
                    <a href="{{ route('purchase.shipping.edit', $item->id) }}" class="purchase-link">変更する</a>
                </div>

                <div class="purchase-address">
                    <div>〒 {{ $shipping['postal_code'] }}</div>
                    <div>{{ $shipping['address'] }}</div>
                    <div>{{ $shipping['building'] }}</div>
                </div>
            </div>
            </div>

            <div class="purchase__right">
                <form method="POST" action="{{ route('purchase.store', $item->id) }}" id="purchaseForm">
                    @csrf
                    <input type="hidden" name="payment_method" id="paymentMethodHidden" value="">

                    <div class="purchase-box">
                        <div class="purchase-box__row">
                            <div class="purchase-box__label">商品代金</div>
                            <div class="purchase-box__value">¥ {{ number_format($item->price) }}</div>
                        </div>
                        <div class="purchase-box__row">
                            <div class="purchase-box__label">支払い方法</div>
                            <div class="purchase-box__value" id="paymentMethodText">未選択</div>
                        </div>
                    </div>

                    <button type="submit" class="purchase-btn" id="purchaseBtn" disabled>
                        購入する
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const select = document.getElementById('paymentMethod');
    const hidden = document.getElementById('paymentMethodHidden');
    const text = document.getElementById('paymentMethodText');
    const btn = document.getElementById('purchaseBtn');

    const labels = { "1": "コンビニ支払い", "2": "カード支払い" };

    select.addEventListener('change', () => {
        const v = select.value;
        hidden.value = v;
        text.textContent = labels[v] || '未選択';
        btn.disabled = !v;
    });
</script>
@endpush

@endsection