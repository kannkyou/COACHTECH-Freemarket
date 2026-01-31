@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="index-page">

    {{-- タブ --}}
    @php
        $tab = $tab ?? request('tab');
        $keyword = $keyword ?? request('keyword');
        $query = request()->query();
    @endphp

    <div class="index-tabs">
        @php
            $recommendQuery = $query;
            unset($recommendQuery['tab']);
        @endphp
        <a href="{{ url('/' . (!empty($recommendQuery) ? ('?' . http_build_query($recommendQuery)) : '')) }}"
           class="index-tabs__item {{ $tab !== 'mylist' ? 'index-tabs__item--active' : '' }}">
            おすすめ
        </a>

        {{-- マイリストkeywordは維持 --}}
        @php
            $mylistQuery = $query;
            $mylistQuery['tab'] = 'mylist';
        @endphp
        <a href="{{ url('/' . ('?' . http_build_query($mylistQuery))) }}"
           class="index-tabs__item {{ $tab === 'mylist' ? 'index-tabs__item--active' : '' }}">
            マイリスト
        </a>
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
                    @if ((int)$item->status === 2)
                        <div class="sold-badge">
                            <span class="sold-badge__text">SOLD</span>
                        </div>
                    @endif

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
