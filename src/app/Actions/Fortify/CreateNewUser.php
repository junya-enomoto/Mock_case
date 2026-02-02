<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Validator; // ★不要になるのでコメントアウトまたは削除
use Laravel\Fortify\Contracts\CreatesNewUsers;
// use Laravel\Jetstream\Jetstream; // Jetstreamを使っていないなら削除
use App\Http\Requests\RegisterRequest; // ★追加: 作成したForm Requestをインポート

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * 新しい登録ユーザーをバリデートして作成する
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // ★↓↓ ここを修正 ↓↓★
        // Form Request を使ってバリデーションを実行
        // ただし、Form Request は通常コントローラーで使用するため、
        // ここでは Request インスタンスを渡してバリデーションをトリガーする形にする。
        // もしくは、$input を直接バリデーターに渡す形に戻し、ルールは Form Request と同期させる。
        // Fortify の場合は、基本的に Validator::make を直接使う方が一般的です。
        // Form Request を直接ここで使うと、$this->authorize() のコンテキストが FormRequest ではないため、少しトリッキーになります。

        // よりFortifyの標準的なやり方で Form Request のルールを適用
        $request = new RegisterRequest(); // 仮のRequestインスタンスを作成
        $rules = $request->rules();
        $messages = $request->messages();
        $attributes = $request->attributes();

        \Illuminate\Support\Facades\Validator::make($input, $rules, $messages, $attributes)->validate();
        // ★↑↑ ここを修正 ↑↑★

        return User::create([
            'user_name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}

