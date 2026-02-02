<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'user_image' => ['nullable', 'image', 'mimes:jpeg,png'], 
            'user_name' => ['required', 'string', 'max:20'], 
            'postal_code' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], 
            'street_address' => ['required', 'string', 'max:255'], 
            'building_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'user_image' => 'プロフィール画像',
            'user_name' => 'ユーザー名',
            'postal_code' => '郵便番号',
            'street_address' => '住所',
            'building_name' => '建物名',
        ];
    }

    public function messages()
    {
        return [
            'user_image.mimes' => 'プロフィール画像は .jpeg または .png 形式でアップロードしてください。',
            'user_name.required' => 'ユーザー名を入力してください。',
            'user_name.max' => 'ユーザー名は20文字以内で入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください。',
            'street_address.required' => '住所を入力してください。',
        ];
    }
}
