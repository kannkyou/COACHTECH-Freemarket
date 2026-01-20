@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@php
    // 商品状態
    $conditionText = match ((int) $item->condition) {
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    };

    // 自分がこの商品をマイリスト済みか
    $isFavorited = isset($isFavorited) ? (bool) $isFavorited : false;

    // ハート画像
    $heartSrc = $isFavorited ? asset('images/pinkheart.png') : asset('images/heart.png');
@endphp

@section('content')
<div class="item-show">
    <div class="item-show__inner">

        <div class="item-show__top">
            {{-- 商品画像 --}}
            <div class="item-show__image">
                @php
                    $mainImage = $item->images->isNotEmpty()
                        ? asset('storage/' . $item->images->first()->image_url)
                        : null;
                @endphp

                @if ($mainImage)
                    <img src="{{ $mainImage }}" alt="{{ $item->title }}">
                @else
                    <div class="item-show__placeholder">商品画像</div>
                @endif
            </div>

            {{-- 右：商品情報 --}}
            <div class="item-show__info">
                <h1 class="item-show__title">{{ $item->title }}</h1>
                <div class="item-show__brand">{{ $item->brand_name }}</div>

                <div class="item-show__price">
                    ￥{{ number_format($item->price) }} <span class="item-show__tax">(税込)</span>
                </div>

                <div class="item-show__meta">
                    <button
                        type="button"
                        class="meta-button"
                        id="favoriteButton"
                        data-url="{{ route('items.mylist.toggle', $item->id) }}"
                        data-heart="{{ asset('images/heart.png') }}"
                        data-pink="{{ asset('images/pinkheart.png') }}"
                        aria-label="マイリストに追加"
                    >
                        <img id="favoriteIcon" src="{{ $heartSrc }}" alt="">
                        <span class="meta-button__count" id="favoriteCount">{{ $favoriteCount }}</span>
                    </button>

                    {{-- コメント数（今は表示だけ） --}}
                    <div class="meta-button meta-button--static">
                        <img src="{{ asset('images/comment.png') }}" alt="">
                        <span class="meta-button__count">{{ $comments->count() }}</span>
                    </div>
                </div>

                {{-- 購入ボタン（今は見た目だけ） --}}
                <button type="button" class="item-show__buy">購入手続きへ</button>

                {{-- 商品説明 --}}
                <h2 class="item-show__h2">商品説明</h2>
                <p class="item-show__desc">
                    {{ $item->description }}
                </p>

                {{-- 商品情報 --}}
                <h2 class="item-show__h2">商品の情報</h2>

                <div class="item-show__spec">
                    <div class="spec-row">
                        <div class="spec-row__label">カテゴリー</div>
                        <div class="spec-row__value">
                            @forelse ($item->categories as $category)
                                <span class="chip">{{ $category->name }}</span>
                            @empty
                                <span class="chip">未設定</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="spec-row">
                        <div class="spec-row__label">商品の状態</div>
                        <div class="spec-row__value">
                            {{ $conditionText }}
                        </div>
                    </div>
                </div>

                {{-- コメント --}}
                <h2 class="item-show__h2">コメント({{ $comments->count() }})</h2>

                <div class="item-show__comments">
                    @forelse ($comments as $comment)
                        <div class="comment">
                            <div class="comment__avatar"></div>
                            <div class="comment__body">
                                <div class="comment__name">{{ $comment->user->name ?? 'user' }}</div>
                                <div class="comment__text">{{ $comment->body }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="item-show__no-comment">まだコメントはありません</div>
                    @endforelse
                </div>

                {{-- コメント投稿（器だけ：後で route を実装） --}}
                <h2 class="item-show__h2">商品へのコメント</h2>

                <form method="POST" action="#" class="comment-form">
                    @csrf
                    <textarea class="comment-form__textarea" name="comment" rows="5"></textarea>
                    <button type="submit" class="comment-form__submit">コメントを送信する</button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
const favoriteButton = document.getElementById('favoriteButton');
const favoriteIcon = document.getElementById('favoriteIcon');
const favoriteCountEl = document.getElementById('favoriteCount');

if (favoriteButton && favoriteIcon && favoriteCountEl) {
  favoriteButton.addEventListener('click', async () => {
    const url = favoriteButton.dataset.url;

    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenMeta ? tokenMeta.getAttribute('content') : null;

    console.log('clicked', url, 'csrf?', !!csrf);

    if (!csrf) {
      console.error('CSRF meta missing. Add <meta name="csrf-token" ...> to layouts.app');
      return;
    }

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify({}),
    });

    const contentType = res.headers.get('content-type') || '';
    console.log('status', res.status, 'content-type', contentType);

    if (!contentType.includes('application/json')) {
      window.location.href = '/login';
      return;
    }

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      console.error('toggle failed', res.status, err);
      return;
    }

    const data = await res.json();
    const isFavorited = !!data.isFavorited;

    favoriteIcon.src = isFavorited ? favoriteButton.dataset.pink : favoriteButton.dataset.heart;
    favoriteCountEl.textContent = String(data.count);
  });
}
</script>
@endpush