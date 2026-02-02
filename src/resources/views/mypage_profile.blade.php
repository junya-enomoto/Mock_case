@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage_profile.css') }}">
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-wrapper">
        <h2 class="profile-title">プロフィール設定</h2>

        <form action="{{ route('mypage.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            
            <div class="profile-image-section">    
                <label for="image-upload" class="upload-btn">画像を選択する</label>
                <input type="file" id="image-upload" name="user_image" style="display: none;" accept="image/png, image/jpeg">
            </div>

            @error('user_image')
                <div class="error-message" style="color: red; text-align: center; margin-bottom: 20px;">{{ $message }}</div>
            @enderror

            <div class="form-group">
                <label for="name">ユーザー名</label>
                <input type="text" id="name" name="user_name" value="{{ old('user_name', $user->user_name) }}" class="form-control">
                @error('user_name')
                    <span class="error-message" style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-control">
                @error('postal_code')
                    <span class="error-message" style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="street_address">住所</label>
                <input type="text" id="street_address" name="street_address" value="{{ old('street_address', $user->street_address) }}" class="form-control">
                @error('street_address')
                    <span class="error-message" style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="building_name">建物名</label>
                <input type="text" id="building_name" name="building_name" value="{{ old('building_name', $user->building_name) }}" class="form-control">
                @error('building_name')
                    <span class="error-message" style="color: red;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-btn">更新する</button>
        </form>
    </div>
</div>
@endsection
