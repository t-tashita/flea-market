@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/buy.css')}}">
@endsection

@section('navigation')
<div class="search">
  <form action="/" method="get" class="search-form" id="search-form">
    <input type="text" class="search-form__input" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') ?? '' }}">
  </form>
</div>
<nav class="navigation">
  <form action="/logout" method="post">
    @csrf
    <input class="header__link" type="submit" value="ログアウト">
  </form>
  <a class="header__link-mypage" href="/mypage">マイページ</a>
  <a class="header__link-sell" href="/sell">出品</a>
</nav>
@endsection

@section('content')
<form class="all-contents" action="/purchase/{{ $item->id }}" method="post">
  @csrf
  <div class="left-content">
    <div class="item-detail">
      <img src="{{ asset('storage/' . $item->item_image) }}" alt="商品画像" class="item-img" />
      <div class="item-detail__info">
        <h2 class="item-name">{{ $item->item_name }}</h2>
        <p class="item-price">&yen; <span class=item-value>{{ number_format($item->price) }}</span></p>
      </div>
    </div>
    <div class="order-form__group">
      <div class="order-form__detail">
        <label class="order-form__label">支払い方法</label>
        <div class="order-form__condition-inputs">
          <select class="order-form__condition-select" name="payment_method" id="paymentSelect">
            <option disabled selected>選択してください</option>
            @foreach($payment_methods as $payment_method)
            <option value="{{ $payment_method->id }}" {{ old('payment_method')==$payment_method->id ? 'selected' : '' }}>{{$payment_method->payment_method }}</option>
            @endforeach
          </select>
        </div>
        <p class="order-form__error-message">
          @error('payment_method')
          {{ $message }}
          @enderror
        </p>
      </div>
    </div>
    <div class="order-form__group">
      <div class="order-form__detail">
        <div class="order-form__change">
          <label class="order-form__label" >配送先</label>
          <a class="address-form__btn" href='/purchase/address/{{ $item->id }}'>
            変更する
          </a>
        </div>
        <div class="order-form__address">
          <p class="order-form__error-message">
            @error('delivery_info')
            {{ $message }}
            @enderror
          </p>
          <p class="order-form__postal_code">&#12306; <input class="order-form__postal_code_input" type="text" value="{{ $address['postal_code'] ?? $user->user_postal_code }}" name="order_postal_code" readonly></p>
          <input class="order-form__address_input" type="text" name="order_address" value="{{ $address['address'] ?? $user->user_address }}" readonly>
          <input class="order-form__address_input" type="text" name="order_building" value="{{ $address['building'] ?? $user->user_building }}" readonly>
        </div>
      </div>
    </div>
  </div>

  <div class="right-content">
    <table class="pay-detail__table">
      <tr class="pay-detail__price">
        <th class="pay-detail__price-ttl">商品代金</th>
        <td class="pay-detail__price-value">&yen;{{number_format($item->price) }}</td>
      </tr>
      <tr class="pay-detail__method">
        <th class="pay-detail__method-ttl">支払い方法</th>
        <td class="pay-detail__method-name" id="selected-method-display" >
          @php
            $selected_payment = $payment_methods->firstWhere('id', old('payment_method'));
          @endphp
          {{ $selected_payment ? $selected_payment->payment_method : '選択してください' }}
        </td>
      </tr>
    </table>
    <div class="address-form__buy">
      <input class="address-form__btn-buy" type="submit" value="購入する">
    </div>
  </div>
</form>
@endsection

@section('script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const paymentSelect = document.getElementById('paymentSelect');
    const display = document.getElementById('selected-method-display');
    const hiddenInput = document.getElementById('selected-method-id');

    paymentSelect.addEventListener('change', function () {
      const selectedText = paymentSelect.options[paymentSelect.selectedIndex].text;
      const selectedValue = paymentSelect.value;
      display.textContent = selectedText;
      hiddenInput.value = selectedValue;
    });
  });
</script>
@endsection