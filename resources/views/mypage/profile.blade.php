@extends('layouts.app')

@section('css')
  <link rel="stylesheet" href="{{ asset('css/mypage/profile.css') }}">
@endsection

@section('content')
<div class="profile-page">
  <div class="profile-page__inner">
    <h1 class="profile-page__title">プロフィール設定</h1>

    <form class="profile-form"
          method="POST"
          action="{{ route('mypage.profile.update') }}"
          enctype="multipart/form-data">
      @csrf

      <div class="profile-form__top">
        <div class="profile-avatar">
          <img
            id="profilePreview"
            class="profile-avatar__image"
            src="{{ $user->user_image ? asset('storage/' . $user->user_image) : '' }}"
            alt="プロフィール画像"
          >
        </div>

        <div class="profile-avatar__actions">
          <label class="btn-outline">
            画像を選択する
            <input
              id="userImageInput"
              class="btn-outline__file"
              type="file"
              name="user_image"
              accept="image/*"
            >
          </label>

          <p class="form-error">
            @error('user_image')
              {{ $message }}
            @enderror
          </p>
        </div>
      </div>

      <div class="profile-form__body">
        <div class="form-group">
          <label class="form-group__label" for="name">ユーザー名</label>
          <input class="form-group__input"
                 id="name"
                 type="text"
                 name="name"
                 value="{{ old('name', $user->name) }}">
          <p class="form-error">
            @error('name')
              {{ $message }}
            @enderror
          </p>
        </div>

        <div class="form-group">
          <label class="form-group__label" for="postal_code">郵便番号</label>
          <input class="form-group__input"
                 id="postal_code"
                 type="text"
                 name="postal_code"
                 value="{{ old('postal_code', $user->postal_code) }}">
          <p class="form-error">
            @error('postal_code')
              {{ $message }}
            @enderror
          </p>
        </div>

        <div class="form-group">
          <label class="form-group__label" for="address">住所</label>
          <input class="form-group__input"
                 id="address"
                 type="text"
                 name="address"
                 value="{{ old('address', $user->address) }}">
          <p class="form-error">
            @error('address')
              {{ $message }}
            @enderror
          </p>
        </div>

        <div class="form-group">
          <label class="form-group__label" for="building">建物名</label>
          <input class="form-group__input"
                 id="building"
                 type="text"
                 name="building"
                 value="{{ old('building', $user->building) }}">
          <p class="form-error">
            @error('building')
              {{ $message }}
            @enderror
          </p>
        </div>

        <button class="btn-primary" type="submit">更新する</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('userImageInput');
    const preview = document.getElementById('profilePreview');

    if (!input || !preview) return;

    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        alert('画像ファイルを選択してください');
        this.value = '';
        return;
      }

      const objectUrl = URL.createObjectURL(file);
      preview.src = objectUrl;
      preview.onload = () => URL.revokeObjectURL(objectUrl);
    });
  });
</script>
@endsection

@if (session('status'))
  <script>
    alert(@json(session('status')));
  </script>
@endif
