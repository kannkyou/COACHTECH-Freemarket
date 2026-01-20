@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items/sell.css') }}">
@endsection

@section('content')
<div class="sell-page">
    <div class="sell-page__inner">
        <h1 class="sell-page__title">商品の出品</h1>

        <form class="sell-form" method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- 画像 --}}
            <div class="sell-section">
                <h2 class="sell-section__label">商品画像</h2>

                <div class="sell-image">
                    <div class="sell-image__box">
                        <div class="sell-image__previews" id="imagePreviews"></div>
                        <label class="btn-outline">
                            画像を選択する
                            <input id="imageInput" class="btn-outline__file" type="file" name="images[]" accept="image/*" multiple>
                        </label>
                    </div>
                </div>

                <p class="form-error">
                    @error('image') {{ $message }} @enderror
                </p>
            </div>

            {{-- カテゴリー --}}
            <div class="sell-section">
                <h2 class="sell-section__label">商品の詳細</h2>

                <div class="sell-field">
                    <label class="sell-field__label">カテゴリー</label>

                    <div class="category-list">
                        @foreach ($categories as $category)
                            <label class="category-chip">
                                <input
                                    class="category-chip__input"
                                    type="checkbox"
                                    name="category_ids[]"
                                    value="{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                                >
                                <span class="category-chip__text">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    <p class="form-error">
                        @error('category_ids') {{ $message }} @enderror
                        @error('category_ids.*') {{ $message }} @enderror
                    </p>
                </div>

                {{-- 状態（tinyintで送る） --}}
                <div class="sell-field">
                    <label class="sell-field__label" for="condition">商品の状態</label>

                    <select class="sell-field__select" id="condition" name="condition">
                        <option value="" disabled {{ old('condition') === null ? 'selected' : '' }}>
                            選択してください
                        </option>
                        <option value="1" {{ (string) old('condition') === '1' ? 'selected' : '' }}>
                            良好
                        </option>
                        <option value="2" {{ (string) old('condition') === '2' ? 'selected' : '' }}>
                            目立った傷や汚れなし
                        </option>
                        <option value="3" {{ (string) old('condition') === '3' ? 'selected' : '' }}>
                            やや傷や汚れあり
                        </option>
                        <option value="4" {{ (string) old('condition') === '4' ? 'selected' : '' }}>
                            状態が悪い
                        </option>
                    </select>

                    <p class="form-error">
                        @error('condition') {{ $message }} @enderror
                    </p>
                </div>
            </div>

            {{-- 商品名と説明 --}}
            <div class="sell-section">
                <h2 class="sell-section__label">商品名と説明</h2>

                <div class="sell-field">
                    <label class="sell-field__label" for="title">商品名</label>
                    <input
                        class="sell-field__input"
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                    >
                    <p class="form-error">
                        @error('title') {{ $message }} @enderror
                    </p>
                </div>

                <div class="sell-field">
                    <label class="sell-field__label" for="brand_name">ブランド名</label>
                    <input
                        class="sell-field__input"
                        id="brand_name"
                        type="text"
                        name="brand_name"
                        value="{{ old('brand_name') }}"
                    >
                    <p class="form-error">
                        @error('brand_name') {{ $message }} @enderror
                    </p>
                </div>

                <div class="sell-field">
                    <label class="sell-field__label" for="description">商品の説明</label>
                    <textarea
                        class="sell-field__textarea"
                        id="description"
                        name="description"
                        rows="6"
                    >{{ old('description') }}</textarea>
                    <p class="form-error">
                        @error('description') {{ $message }} @enderror
                    </p>
                </div>

                <div class="sell-field">
                    <label class="sell-field__label" for="price">販売価格</label>
                    <input
                        class="sell-field__input"
                        id="price"
                        type="text"
                        name="price"
                        inputmode="numeric"
                        value="{{ old('price') }}"
                    >
                    <p class="form-error">
                        @error('price') {{ $message }} @enderror
                    </p>
                </div>
            </div>

            <div class="sell-actions">
                <button class="btn-primary" type="submit">出品する</button>
            </div>
        </form>
    </div>
</div>

<script>
    const input = document.getElementById('imageInput');
    const previews = document.getElementById('imagePreviews');

    input.addEventListener('change', function () {
        previews.innerHTML = '';

        const files = Array.from(this.files);

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const img = document.createElement('img');
            img.classList.add('sell-image__preview-item');

            const objectUrl = URL.createObjectURL(file);
            img.src = objectUrl;

            img.onload = () => {
                URL.revokeObjectURL(objectUrl);
            };

            previews.appendChild(img);
        });
    });

        const priceInput = document.getElementById('price');

    if (priceInput) {
        // 入力時：￥＋カンマ表示
        priceInput.addEventListener('input', function () {
            const rawValue = this.value.replace(/[^\d]/g, '');
            if (rawValue === '') {
                this.value = '';
                return;
            }

            const formatted = Number(rawValue).toLocaleString('ja-JP');
            this.value = '￥' + formatted;
        });

        priceInput.form.addEventListener('submit', function () {
            priceInput.value = priceInput.value.replace(/[^\d]/g, '');
        });
    }
</script>

@endsection