@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase_address.css') }}">
@endsection

@section('content')
<div class="address-change-container">
    <div class="address-change-wrapper">
        <h2 class="page-title">住所の変更</h2>

        <form action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="postal_code">郵便番号</label>
                <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="form-control @error('postal_code') is-invalid @enderror">
                @error('postal_code')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">住所</label>
                <input type="text" id="address" name="address" value="{{ old('address', $user->street_address) }}" class="form-control @error('address') is-invalid @enderror">
                @error('address')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="building_name">建物名</label>
                <input type="text" id="building_name" name="building_name" value="{{ old('building_name', $user->building_name) }}" class="form-control @error('building_name') is-invalid @enderror">
                @error('building_name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="update-btn">更新する</button>
        </form>
    </div>
</div>
@endsection
