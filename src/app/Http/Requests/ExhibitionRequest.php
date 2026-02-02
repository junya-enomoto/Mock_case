<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'item_image' => ['required', 'image', 'mimes:jpeg,png'],
            'category_ids' => ['required', 'array', 'min:1'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => '商品名',
            'description' => '商品説明',
            'item_image' => '商品画像',
            'category_ids' => 'カテゴリー',
            'condition' => '商品の状態',
            'price' => '販売価格',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください。',
            'description.required' => '商品説明を入力してください。',
            'description.max' => '商品説明は255文字以内で入力してください。',
            'item_image.required' => '商品画像をアップロードしてください。',
            'item_image.mimes' => '商品画像は.jpegまたは.png形式でアップロードしてください。',
            'category_ids.required' => 'カテゴリーを選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'price.required' => '販売価格を入力してください。',
            'price.integer' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は0円以上で入力してください。',
        ];
    }
}

