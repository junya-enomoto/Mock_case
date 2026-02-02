<div class="products">
    @forelse($items as $item)
        <div class="product-card">
            @if($item->is_sold)
                <div class="product-content-disabled">
                    <div class="product-image">
                        <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="sold-badge">SOLD</div>
                    </div>
                    <div class="product-body">
                        <h3 class="product-name">{{ $item->name }}</h3>
                        <span class="product-price">¥{{ number_format($item->price) }}</span>
                    </div>
                </div>
            @else
                <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" style="text-decoration: none; color: inherit;">
                    <div class="product-image">
                        <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="product-body">
                        <h3 class="product-name">{{ $item->name }}</h3>
                        <span class="product-price">¥{{ number_format($item->price) }}</span>
                    </div>
                </a>
            @endif
        </div>
    @empty
        <p>出品した商品がありません。</p>
    @endforelse
</div>
