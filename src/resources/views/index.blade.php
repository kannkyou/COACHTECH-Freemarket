@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-page">

  {{-- タブ --}}
  <div class="index-tabs">
    <a href="{{ url('/') }}" class="index-tabs__item index-tabs__item--active">おすすめ</a>
    <a href="{{ url('/?tab=mylist') }}" class="index-tabs__item">マイリスト</a>
  </div>


    {{-- 商品グリッド --}}
    <div class="item-grid">
        @forelse ($items ?? collect() as $item)
            @php
                $firstImage = null;

                if ($item->images->isNotEmpty()) {
                    $firstImage = asset('storage/' . $item->images->first()->image_url);
                }
            @endphp
            <a class="item-card" href="{{ route('items.show', $item->id) }}">
                <div class="item-card__image">
                    @if ($firstImage)
                        <img src="{{ $firstImage }}" alt="{{ $item->title }}">
                    @else
                        <div class="item-card__placeholder">商品画像</div>
                    @endif
                </div>

                <div class="item-card__name">
                    {{ $item->title }}
                </div>
            </a>
        @empty
            <div class="item-empty">
                出品された商品はまだありません
            </div>
        @endforelse
    </div>

</div>
@endsection