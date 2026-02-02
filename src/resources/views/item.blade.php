@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
<div class="item-detail-container">
    <div class="item-detail-wrapper">
        
        <div class="item-image-section">
            <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}" class="item-image">
            @if($item->is_sold)
                <div class="sold-badge">SOLD</div>
            @endif
        </div>

        <div class="item-info-section">
            
            <h2 class="item-name">{{ $item->name }}</h2>
            <p class="brand-name">{{ $item->brand_name}}</p> {{-- ブランドがない場合の処理 --}}

            <div class="item-price-area">
                <span class="item-price">¥{{ number_format($item->price) }} <small>(税込)</small></span>
                
                <div class="item-actions">
                    <div class="action-icon">
                        @auth
                            @if ($item->isLikedByUser())
                                <form action="{{ route('like.destroy', ['item_id' => $item->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="like-btn liked">
                                        <i class="fa-solid fa-heart"></i> {{-- 塗りつぶしのハート --}}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('like.store', ['item_id' => $item->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="like-btn">
                                        <i class="fa-regular fa-heart"></i> 
                                    </button>
                                </form>
                            @endif
                        @else
                            <i class="fa-regular fa-heart"></i>
                        @endauth
                        <span>{{ $item->likes()->count() }}</span> 
                    </div>
                    <div class="action-icon">
                        <i class="fa-regular fa-comment"></i>
                        <span>{{ $item->comments->count() }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('purchase.index', ['item_id' => $item->id]) }}" method="GET">
                <button type="submit" class="purchase-btn">購入手続きへ</button>
            </form>

            <div class="item-description">
                <h3>商品説明</h3>
                <p>{{ $item->description }}</p>
            </div>

            <div class="item-meta-info">
                <h3>商品の情報</h3>
                <div class="meta-row">
                    <span class="meta-label">カテゴリー</span>
                    <div class="meta-categories">
                        @foreach($item->categories as $category)
                            <span class="category-tag">{{ $category->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="meta-row">
                    <span class="meta-label">商品の状態</span>
                    <span class="meta-value">{{ $item->condition }}</span>
                </div>
            </div>

            <div class="comments-section">
                <h3>コメント({{ $item->comments->count() }})</h3>
                
                @forelse($item->comments as $comment)
                <div class="comment-item">
                    <div class="comment-user-header">
                        @if(isset($comment->user->user_image) && $comment->user->user_image)
                            <img src="{{ asset($comment->user->user_image) }}" alt="{{ $comment->user->user_name }}" class="user-icon">
                        @else
                            <div class="user-icon default-icon"></div>
                        @endif
                        <span class="user-name">{{ $comment->user->user_name }}</span>
                    </div>
                    
                    <div class="comment-body">
                        {{ $comment->comment }} 
                    </div>
                </div>
                @empty
                    <p>まだコメントはありません。</p>
                @endforelse
            </div>

            <div class="comment-form-section">
                <h3>商品へのコメント</h3>
                
                <form action="{{ route('comment.store', ['item_id' => $item->id]) }}" method="POST">
                    @csrf
                    
                    <textarea name="content" class="comment-textarea" placeholder="コメントを入力してください。"></textarea>
                    
                    @error('content') 
                        <span style="color: red;">{{ $message }}</span>
                    @enderror
                    
                    <button type="submit" class="comment-submit-btn">コメントを送信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
