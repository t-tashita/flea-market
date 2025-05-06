@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/top.css')}}">
@endsection

@section('navigation')
<div class="search">
  <form action="/" method="get" class="search-form" id="search-form">
    <input type="text" class="search-form__input" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') ?? '' }}">
  </form>
</div>
<nav class="navigation">
  @guest
    <a class="header__link-login" href="/login">ログイン</a>
  @endguest
  @auth
    <form class="header__link-logout" action="/logout" method="post">
      @csrf
      <input class="header__link" type="submit" value="ログアウト">
    </form>
  @endauth
  <a class="header__link-mypage" href="/mypage">マイページ</a>
  <a class="header__link-sell" href="/sell">出品</a>
</nav>
@endsection

@section('content')
<div class="top-content">
  <div class="top-content__inner">
    <div class="top-content__group">
      @php
        $keyword = request()->query('keyword'); // 現在のキーワード
      @endphp
      <div class="top-content__title">
        <a class="top-content__ttl {{ Request::is('/') ? 'active' : '' }}"
          href="{{ route('top', ['keyword' => $keyword]) }}">
          おすすめ
        </a>
        <a class="top-content__ttl-mylist {{ Request::is('mylist') ? 'active' : '' }}"
          href="{{ route('mylist', ['keyword' => $keyword]) }}">
          マイリスト
        </a>
      </div>
      <div class="item-contents">
          @foreach ($items as $item)
          <div class="item-content">
              <a href="/item/{{$item->id}}" class="item-link {{ $item->order ? 'sold' : '' }}">
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
@endsection

@section('script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.search-form__input');
    const searchForm = document.getElementById('search-form');

    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault(); // デフォルトのフォーム送信を防止（必要なら）
        searchForm.submit(); // 明示的に送信
      }
    });
  });
</script>
@endsection
