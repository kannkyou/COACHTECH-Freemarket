<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'postal_code' => ['required', 'regex:/^\d{7}$/'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号はハイフン抜きの7桁で入力してください',
            'address.required'     => '住所を入力してください',
            'address.max'          => '住所は255文字以下で入力してください',
            'building.max'         => '建物名は255文字以下で入力してください',
        ];
    }
}
