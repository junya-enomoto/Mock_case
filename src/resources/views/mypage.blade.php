@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}"> {{-- 商品一覧グリッド用 --}}
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}"> {{-- マイページ固有のデザイン用 --}}
@endsection

@section('content')
<div class="mypage-container">
    
    <div class="profile-header">
        <div class="profile-avatar">
            @if(isset($user->user_image) && $user->user_image)
                <img src="{{ asset($user->user_image) }}" alt="{{ $user->user_name }}" class="avatar-img">
            @else
                <div class="avatar-placeholder"></div> 
            @endif
        </div>
        <p class="profile-username">{{ $user->user_name }}</p>
        <a href="{{ route('mypage.edit') }}" class="profile-edit-btn">プロフィールを編集</a>
    </div>

    <div class="mypage-tabs">
        <a href="{{ route('mypage', ['type' => 'sell']) }}" 
            class="tab-item {{ $type == 'sell' ? 'active' : '' }}">
            出品した商品
        </a>
        <a href="{{ route('mypage', ['type' => 'buy']) }}" 
            class="tab-item {{ $type == 'buy' ? 'active' : '' }}">
            購入した商品
        </a>
    </div>

    <div class="content-section">
        {{-- $items 変数を @include 先に渡すことを忘れない！ --}}
        @if ($type === 'sell')
            @include('mypage_profile_sell', ['items' => $items])
        @elseif ($type === 'buy')
            @include('mypage_profile_buy', ['items' => $items])
        @else
            {{-- デフォルト表示（例：出品した商品） --}}
            @include('mypage_profile_sell', ['items' => $items])
        @endif
    </div>
</div>
@endsection
