@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css')}}">
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
<div class="address-form">
  <div class="address-form__inner">
    <h1 class="address-form__heading">住所の変更</h1>
    <form class="address-form__form" action="/purchase/address/{{ $item_id }}" method="post">
      @csrf
      <div class="address-form__group">
        <label class="address-form__label" for="postal_code">郵便番号</label>
        <input class="address-form__input" type="text" name="postal_code" id="postal_code" value="{{old('postal_code', $user->user_postal_code) }}">
        @error('postal_code')
        <p class="address-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="address-form__group">
        <label class="address-form__label" for="address">住所</label>
        <input class="address-form__input" type="text" name="address" id="address" value="{{ old('address',$user->user_address) }}">
        @error('address')
        <p class="address-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="address-form__group">
        <label class="address-form__label" for="building">建物名</label>
        <input class="address-form__input" type="text" name="building" id="building" value="{{ old('building',$user->user_building) }}">
        @error('building')
        <p class="address-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <input class="address-form__btn btn" type="submit" value="更新する">
    </form>
  </div>
</div>
@endsection