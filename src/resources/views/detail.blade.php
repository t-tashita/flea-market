@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css')}}">
@endsection

@section('navigation')
<section class="search">
  <form action="/" method="get" class="search-form" id="search-form">
    <input type="text" class="search-form__input" name="keyword" placeholder="なにをお探しですか？" value="{{ request('keyword') ?? '' }}">
  </form>
</section>
<nav class="navigation">
  @guest
  <a class="header__link-login" href="/login">ログイン</a>
  @endguest
  @auth
  <form action="/logout" method="post">
    @csrf
    <input class="header__link" type="submit" value="ログアウト">
  </form>
  @endauth
  <a class="header__link-mypage" href="/mypage">マイページ</a>
  <a class="header__link-sell" href="/sell">出品</a>
</nav>
@endsection

@section('content')
<div class="all-contents">
  <div class="left-content">
    <img src="{{ asset('storage/image_item/' . $item->item_image) }}" alt="商品画像" class="item-img" />
  </div>
  <div class="right-content">
    <h2 class="item-name">{{ $item->item_name }}</h2>
    <p class="item-brand">{{ $item->brand }}</p>
    <p class="item-price">&yen;<span class="item-value">{{ number_format($item->price) }}</span>(税込)</p>
    <section class="item-evaluation">
      <form action="/item/{{$item->id}}/like" method="post" class="item-likes">
        @csrf
        <button type="submit"  class="item-likes__icon {{ auth()->check() && $isLiked ? 'liked' : '' }}">
          {{ auth()->check() && $isLiked ? '★' : '☆' }}
        </button>
        <span class="item-likes__count">{{ $likes->count() }}</span>
      </form>
      <div class="item-comments">
        <span class="item-comments__icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </span>
        <span class="item-comments__count">{{ $comments->count() }}</span>
      </div>
    </section>
    <form action="/purchase/{{$item->id}}" method="GET">
      <button type="submit" class="button-buy">購入手続きへ</button>
    </form>
    <h3 class="item-detail">商品説明</h3>
    <textarea class="item-description" cols="30" rows="10" readonly>{{ $item->description }}</textarea>
    <h3 class="item-info">商品情報</h3>
    <section class="item-category">
      <span class="item-category__ttl">カテゴリー</span>
      <div class="item-category__list">
        @foreach($categories as $category)
        <span class="item-category__name">{{ $category->category_name }}</span>
        @endforeach
      </div>
    </section>
    <div class="item-condition">
      <span class="item-condition__ttl">商品の状態</span>
      <span class="item-condition__name">{{ $condition->condition_name }}</span>
    </div>
    <h3 class="item-comment__title">コメント({{ $comments->count() }})</h3>
    @foreach($comments as $comment)
    <div class="item-comment__profile">
      <img class="comment-icon" src="{{ asset('storage/image_user/' . $comment->user->user_image) }}" alt="プロフィール画像" >
      <span class="comment-user">{{ $comment->user->name }}</span>
    </div>
    <textarea class="comment-content" readonly>{{ $comment->comment }}</textarea>
    @endforeach
    <form action="/item/{{$item->id}}/comment" class="comment-form" method="post">
      @csrf
      <p class="comment-form__ttl">商品へのコメント</p>
      <textarea id="comment" name="comment" class="comment-form__input" cols="30" rows="10">{{ old('comment') }}</textarea>
      @error('comment')
      <p class="item-comment__error-message">{{ $message }}</p>
      @enderror
      <input type="submit" class="comment-form__button" value="コメントを送信する">
    </form>
  </div>
</div>
@endsection