<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    @yield('css')
</head>

<body>

    @php
        // login / register ページの判定
        $isAuthPage = request()->routeIs('login', 'register');
    @endphp

    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/COACHTECH.png') }}" alt="COACHTECHフリマ">
            </a>

    @unless($isAuthPage)
        {{-- Authページの場合は以下を全部非表示 --}}
       <form class="header__search" method="GET" action="{{ route('items.index') }}">
            {{-- タブ状態を維持 --}}
             @if(request('tab'))
                <input type="hidden" name="tab" value="{{ request('tab') }}">
            @endif
            <input type="text" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') }}">
        </form>

      <div class="header__button">
        @auth
             {{-- ログイン済みの場合 --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
            <a href="{{ url('/mypage') }}">マイページ</a>
            <a class="btn" href="{{ url('/sell') }}">出品</a>

        @else
            {{-- 未ログイン --}}
            <a href="{{ route('login') }}">ログイン</a>
            <a href="{{ url('/mypage') }}">マイページ</a>
            <a class="btn" href="{{ url('/sell') }}">出品</a>
        @endauth
         </div>
    @endunless
    </header>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>