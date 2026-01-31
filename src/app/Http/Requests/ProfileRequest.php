<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:20'],
            'postal_code' => ['required', 'regex:/^\d{7}$/'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'user_image'  => ['nullable', 'file', 'mimes:jpeg,png', 'max:2048'], // 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'user_image.mimes' => 'jpegもしくはpngの画像をアップロードしてください',
            'name.required' => 'ユーザー名を入力してください',
            'name.max' => 'ユーザー名は20文字以内で入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフン抜きの7桁で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}