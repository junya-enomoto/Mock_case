<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:20'], 
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'), 
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'], 
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'name' => 'お名前', 
            'email' => 'メールアドレス',
            'password' => 'パスワード',
            'password_confirmation' => '確認用パスワード',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください', 
            'name.max' => 'お名前は20文字以内で入力してください',

            'email.required' => 'メールアドレスを入力してください', 
            'email.email' => '有効なメールアドレス形式で入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています',

            'password.required' => 'パスワードを入力してください', 
            'password.min' => 'パスワードは8文字以上で入力してください', 
            'password.confirmed' => 'パスワードと一致しません', 
        ];
    }
}

