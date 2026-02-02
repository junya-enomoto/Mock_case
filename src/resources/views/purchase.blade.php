@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<div class="purchase-container">
    <div class="purchase-wrapper">
        
        <div class="purchase-left">
            
            <div class="purchase-item-info">
                <div class="item-image">
                    <img src="{{ asset($item->item_image) }}" alt="{{ $item->name }}">
                </div>
                <div class="item-details">
                    <h2 class="item-name">{{ $item->name }}</h2>
                    <p class="item-price">¥{{ number_format($item->price) }} <small>(税込)</small></p>
                </div>
            </div>

            <hr class="divider">

            <div class="payment-method-section">
                <h3>支払い方法</h3>
                <div class="select-wrapper">
                    <select name="payment_method" id="payment_method" class="payment-select @error('payment_method') is-invalid @enderror">
                        <option value="" hidden>選択してください</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>カード支払い</option>
                        <option value="konbini" {{ old('payment_method') == 'konbini' ? 'selected' : '' }}>コンビニ払い</option>
                    </select>
                </div>
                @error('payment_method')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <hr class="divider">

            {{-- 配送先 --}}
            <div class="delivery-section">
                <div class="section-header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="change-link">変更する</a>
                </div>
                <div class="address-info">
                    <p>〒 {{ Auth::user()->postal_code ?? '---' }}</p>
                    <p>{{ Auth::user()->street_address ?? '住所が登録されていません' }}</p>
                    <p>{{ Auth::user()->building_name ?? '' }}</p>
                </div>
                @error('address_registered')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <hr class="divider">

        </div>

        <div class="purchase-right">
            <div class="order-summary">
                <div class="summary-row">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>
                <div class="summary-row">
                    <span>支払い方法</span>
                    <span id="selected-payment-method"></span>
                </div>
            </div>

            <form action="{{ route('purchase.process', ['item_id' => $item->id]) }}" method="POST" id="purchase-form">
                @csrf
                <input type="hidden" name="payment_method" id="hidden_payment_method" value="{{ old('payment_method') ?? '' }}">
                
                <button type="submit" class="purchase-submit-btn">購入する</button>
            </form>
        </div>

    </div>
</div>

<script src="https://js.stripe.com/v3/"></script> {
<script>
    const stripePublicKey = "{{ config('services.stripe.public_key') }}";
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const hiddenPaymentMethodInput = document.getElementById('hidden_payment_method');
        const selectedPaymentMethodSpan = document.getElementById('selected-payment-method');
        const purchaseForm = document.getElementById('purchase-form');

        const updatePaymentMethodDisplay = () => {
            const selectedOption = paymentMethodSelect.options[paymentMethodSelect.selectedIndex];
            selectedPaymentMethodSpan.textContent = selectedOption.textContent;
            hiddenPaymentMethodInput.value = selectedOption.value;
        };

        const initialPaymentMethod = hiddenPaymentMethodInput.value;
        if (initialPaymentMethod) {
            paymentMethodSelect.value = initialPaymentMethod;
        } else if (paymentMethodSelect.options.length > 0 && paymentMethodSelect.options[0].value === "") {
            paymentMethodSelect.value = "";
            hiddenPaymentMethodInput.value = "";
        }
        
        updatePaymentMethodDisplay();
        paymentMethodSelect.addEventListener('change', updatePaymentMethodDisplay);
    });
</script>
@endsection


