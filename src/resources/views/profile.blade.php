@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css')}}">
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
<div class="top-content">
  <div class="top-content__inner">
    <div class="top-content__profile">
      <img class="profile__image" for="image" src="{{ asset('storage/' . $user->user_image) }}" alt="プロフィール画像" >
      <label class="profile__name">{{ $user->name }}</label>
      <a class="profile__button-update" href="/mypage/profile">プロフィールを編集</a>
    </div>

    <div class="top-content__group">
      <div class="top-content__title">
        <a class="top-content__ttl-sell {{ Request::is('mypage') ||  Request::is('mypage/sell') ? 'active' : '' }}" href="/mypage/sell">出品した商品</a>
        <a class="top-content__ttl-buy {{ Request::is('mypage/buy') ? 'active' : '' }}" href="/mypage/buy">購入した商品</a>
      </div>
      <div class="item-contents">
          @foreach ($items as $item)
          <div class="item-content">
              <a href="/item/{{$item->id}}" class="item-link">
                <img src="{{ asset('storage/' . rawurlencode($item->item_image)) }}" alt="商品画像" class="item-img" />
              </a>
              <div class="item-name">
                  <p>{{$item->item_name}}</p>
              </div>
          </div>
          @endforeach
      </div>
    </div>
  </div>
</div>
@endsection('content')
