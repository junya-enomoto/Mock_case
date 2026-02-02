@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <div class="sell-wrapper">
        <h2 class="sell-title">商品の出品</h2>

        <form action="{{ route('item.store') }}" method="POST" class="sell-form" enctype="multipart/form-data">
            @csrf

            <div class="form-section">
                <h3 class="section-title">商品画像</h3>
                <div class="image-upload-area">
                    <label for="item_image" class="upload-box">
                        <input type="file" id="item_image" name="item_image" accept="image/*" style="display: none;">
                        <span class="upload-text">画像を選択する</span>
                        <img id="image-preview" src="#" alt="プレビュー" style="display: none; max-width: 100%; max-height: 150px; margin-top: 10px;">
                    </label>
                </div>
                <div>    
                    @error('item_image')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>            
            </div>

            <hr class="divider">

            <div class="form-section">
                <h3 class="section-title">商品の詳細</h3>


                <div class="form-group">
                    <label for="category">カテゴリー</label>
                    <div class="category-tags-wrapper">
                        @foreach($categories as $category)
                            <div class="category-tag-item">
                                <input type="checkbox" id="category_{{ $category->id }}" name="category_ids[]" value="{{ $category->id }}" 
                                       {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }} style="display: none;">
                                <label for="category_{{ $category->id }}" class="category-tag-label">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('category_ids')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="condition">商品の状態</label>
                    <select id="condition" name="condition" class="form-control @error('condition') is-invalid @enderror">
                        <option value="">選択してください</option>
                        <option value="良好" {{ old('condition') == '良好' ? 'selected' : '' }}>良好</option>
                        <option value="目立った傷や汚れなし" {{ old('condition') == '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="やや傷や汚れあり" {{ old('condition') == 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="状態が悪い" {{ old('condition') == '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
                    </select>
                    @error('condition')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name">商品名</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="brand_name">ブランド名</label>
                    <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" class="form-control @error('brand_name') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label for="description">商品についての説明</label>
                    <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price">販売価格</label>
                    <div class="price-input-wrapper">
                        <span class="price-yen">¥</span>
                        <input type="text" id="price" name="price" value="{{ old('price') }}" class="form-control price-input @error('price') is-invalid @enderror" min="0">
                    </div>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="sell-button">出品する</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('item_image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const preview = document.getElementById('image-preview');
            const uploadText = document.querySelector('.upload-text');
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
            uploadText.style.display = 'none';
        }
    });
</script>
@endsection

