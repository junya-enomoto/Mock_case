<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; 

class PurchaseRequest extends FormRequest
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
        $user = Auth::user();

        return [
            'payment_method' => ['required', 'string', 'in:konbini,card'], 
            'address_registered' => [
                function ($attribute, $value, $fail) use ($user) {
                    if (!$user || empty($user->postal_code) || empty($user->street_address)) {
                        $fail('配送先情報が不足しています。プロフィールから住所を登録してください。');
                    }
                },
            ],
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
            'payment_method' => '支払い方法',
            'address_registered' => '配送先',
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
            'payment_method.required' => '支払い方法を選択してください。',
            'payment_method.in' => '無効な支払い方法が選択されました。'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'address_registered' => Auth::user()->postal_code && Auth::user()->street_address ? 'true' : null,
        ]);
    }
}

