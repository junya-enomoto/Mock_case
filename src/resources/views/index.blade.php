@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <div class="tabs-wrapper">
        <div class="tabs">
            <a href="{{ route('item.index', ['keyword' => $keyword, 'filter' => 'recommend']) }}" 
            class="tab {{ request('filter', 'recommend') == 'recommend' ? 'active' : '' }}">
                おすすめ</a>
            <a href="{{ route('item.index', ['keyword' => $keyword, 'filter' => 'mylist']) }}" class="tab {{ request('filter') == 'mylist' ? 'active' : '' }}">マイリスト</a>
        </div>
    </div>

    <div class="products">
        @forelse($items as $item)
            <div class="product-card">
                @if($item->is_sold)
                    <div class="product-content-disabled">
                        <div class="product-image">
                            <img src="{{ $item->item_image }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div class="sold-badge">SOLD</div>
                        </div>
                        <div class="product-name">{{ $item->name }}</div>
                    </div>
                @else
                    <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" style="text-decoration: none; color: inherit;">
                        <div class="product-image">
                            <img src="{{ $item->item_image }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="product-name">{{ $item->name }}</div>
                    </a>
                @endif
            </div>
        @empty
            <p>まだ商品がありません。</p>
        @endforelse
    </div>
@endsection

