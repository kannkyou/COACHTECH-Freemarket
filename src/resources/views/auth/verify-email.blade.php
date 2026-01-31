@extends('layouts.app')

@section('content')
<div style="max-width: 520px; margin: 40px auto;">
    <h1>メールアドレスの確認</h1>

    <p>
        登録したメールアドレスに確認メールを送信しました。<br>
        メール内のリンクをクリックして認証を完了してください。
    </p>

    @if (session('status') == 'verification-link-sent')
        <p style="color: green;">
            確認メールを再送信しました。
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">確認メールを再送信</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
        @csrf
        <button type="submit">ログアウト</button>
    </form>
</div>
@endsection
