@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage/mypage.css') }}">
@endsection

@section('content')
@php
    $img = $user->user_image ?? null;
    $avatar = $img ? asset('storage/' . $img) : null;
@endphp

<div class="mypage">

  {{-- 上：プロフィール --}}
    <div class="mypage-profile">
        <div class="mypage-profile__left">
            <div class="mypage-profile__avatar">
                @if ($avatar)
                    <img src="{{ $avatar }}" alt="">
                @else
                    <div class="mypage-profile__avatar-placeholder"></div>
                @endif
            </div>

            <div class="mypage-profile__name">{{ $user->name }}</div>
        </div>

        <a class="mypage-profile__edit" href="{{ route('mypage.profile.edit') }}">
            プロフィールを編集
        </a>
    </div>

    {{-- タブ --}}
    <div class="mypage-tabs">
        <a href="{{ route('mypage.index') }}"
        class="mypage-tabs__item {{ $tab === 'sell' ? 'mypage-tabs__item--active' : '' }}">
            出品した商品
        </a>

        <a href="{{ route('mypage.index', ['tab' => 'buy']) }}"
        class="mypage-tabs__item {{ $tab === 'buy' ? 'mypage-tabs__item--active' : '' }}">
            購入した商品
        </a>
    </div>

    {{-- 下：商品グリッド --}}
    <div class="mypage-grid">

    @if ($tab === 'buy')
        @forelse ($buyOrders as $order)
            @php
            $item = $order->item;
            $firstImage = ($item && $item->images->isNotEmpty())
                ? asset('storage/' . $item->images->first()->image_url)
                : null;
            @endphp

            @if ($item)
            <a class="mypage-item-card" href="{{ route('items.show', $item->id) }}">
                <div class="mypage-item-card__image">
                @if ($firstImage)
                    <img src="{{ $firstImage }}" alt="{{ $item->title }}">
                @else
                    <div class="mypage-item-card__placeholder">商品画像</div>
                @endif
                </div>
                <div class="mypage-item-card__name">{{ $item->title }}</div>
            </a>
            @endif
        @empty
            <div class="mypage-empty">購入した商品はありません</div>
        @endforelse

        @else
        @forelse ($sellingItems as $item)
            @php
            $firstImage = $item->images->isNotEmpty()
                ? asset('storage/' . $item->images->first()->image_url)
                : null;
            @endphp

            <a class="mypage-item-card" href="{{ route('items.show', $item->id) }}">
            <div class="mypage-item-card__image">
                @if ($firstImage)
                <img src="{{ $firstImage }}" alt="{{ $item->title }}">
                @else
                <div class="mypage-item-card__placeholder">商品画像</div>
                @endif
            </div>
            <div class="mypage-item-card__name">{{ $item->title }}</div>
            </a>
        @empty
            <div class="mypage-empty">出品した商品はありません</div>
        @endforelse
        @endif

    </div>
</div>
@endsection
