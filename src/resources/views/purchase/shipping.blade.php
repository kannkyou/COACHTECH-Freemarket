@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase/shipping.css') }}">
@endsection

@section('content')
<div class="shipping">
	<div class="shipping__inner">
		<h1 class="shipping__title">住所の変更</h1>

		<form method="POST" action="{{ route('purchase.shipping.update', $item->id) }}" class="shipping-form">
			@csrf

			<div class="shipping-field">
				<label class="shipping-field__label">郵便番号</label>
				<input class="shipping-field__input" type="text" name="postal_code" value="{{ old('postal_code', $shipping['postal_code'] ?? '') }}">
				@error('postal_code') <p class="form-error">{{ $message }}</p> @enderror
			</div>

			<div class="shipping-field">
				<label class="shipping-field__label">住所</label>
				<input class="shipping-field__input" type="text" name="address" value="{{ old('address', $shipping['address'] ?? '') }}">
				@error('address') <p class="form-error">{{ $message }}</p> @enderror
			</div>

			<div class="shipping-field">
				<label class="shipping-field__label">建物名</label>
				<input class="shipping-field__input" type="text" name="building" value="{{ old('building', $shipping['building'] ?? '') }}">
				@error('building') <p class="form-error">{{ $message }}</p> @enderror
			</div>

			<button type="submit" class="shipping-btn">更新する</button>
		</form>
	</div>
</div>
@endsection
