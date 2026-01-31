<h1>メール認証</h1>

<p>下のボタンを押して認証を完了してください。</p>

<form method="POST" action="{{ $signedUrl }}">
    @csrf
    <button type="submit">
        認証はこちらから
    </button>
</form>
