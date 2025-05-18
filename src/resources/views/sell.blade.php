@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css')}}">
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
<div class="sell-form">
  <div class="sell-form__inner">
    <h1 class="sell-form__heading">商品の出品</h1>
    <form action="/sell" method="post" enctype="multipart/form-data">
      @csrf
      <div class="sell-form__group">
        <label class="sell-form__label" for="image">商品画像</label>
        <div class="sell-form__image-inputs">
          <input type="file" id="image" name="image" accept="image/*" hidden>
          <label for="image" class="custom-file-label">画像を選択する</label>
        </div>
        @error('image')
        <div class="sell-form__error-message">{{ $message }}</div>
        @enderror
      </div>
      <h3 class="sell-form__subheading">商品の詳細</h3>
      <div class="sell-form__group">
        <label class="sell-form__label">カテゴリー</label>
        <div class="sell-form__category-inputs">
          <div class="sell-form__category-check">
            @foreach($categories as $category)
            <label class="sell-form__category-label">
              <input class="sell-form__category-input" id="category_{{ $category['id'] }}" name="item_category[]" type="checkbox" value="{{ $category['id'] }}" {{ in_array($category['id'], old('item_category', [])) ? 'checked' : '' }} hidden>
              <span class="sell-form__category-text">
                {{ $category['category_name'] }}
              </span>
            </label>
            @endforeach
          </div>
        </div>
        @error('item_category')
        <p class="sell-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label">商品の状態</label>
        <div class="sell-form__condition-inputs">
          <select class="sell-form__condition-select" name="condition_id" >
            <option class="placeholder-option" disabled selected hidden>選択してください</option>
            @foreach($conditions as $condition)
            <option value="{{ $condition->id }}" {{ old('condition_id')==$condition->id ? 'selected' : '' }}>{{$condition->condition_name }}</option>
            @endforeach
          </select>
        </div>
        @error('condition_id')
        <p class="sell-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <h3 class="sell-form__subheading">商品名と説明</h3>
      <div class="sell-form__group">
        <label class="sell-form__label" for="name">商品名</label>
        <input class="sell-form__input" type="text" id="name" name="item_name" value="{{ old('item_name') }}">
        @error('item_name')
        <p class="register-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="brand">ブランド名</label>
        <input class="sell-form__input" type="text" id="brand" name="brand" value="{{ old('brand') }}">
        @error('brand')
        <p class="sell-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="description">商品の説明</label>
        <textarea class="sell-form__textarea" id="description" name="description" cols="30" rows="10">{{ old('description') }}</textarea>
        @error('description')
        <p class="sell-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="sell-form__group">
        <label class="sell-form__label" for="price">販売価格</label>
        <input class="sell-form__input" type="text" name="price" id="price" value="{{ old('price') }}">
        @error('price')
        <p class="sell-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <input class="sell-form__btn" type="submit" value="出品する">
    </form>
  </div>
</div>
@endsection

@section('script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // カテゴリ選択のラベル切り替え
    document.querySelectorAll('.sell-form__category-label').forEach(label => {
      const input = label.querySelector('.sell-form__category-input');
      if (input.checked) {
        label.classList.add('checked');
      }
      input.addEventListener('change', () => {
        label.classList.toggle('checked', input.checked);
      });
    });
    // 背景画像として表示
    const imageInput = document.getElementById('image');
    const imageWrapper = document.querySelector('.sell-form__image-inputs');
    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
        imageWrapper.style.backgroundImage = `url(${e.target.result})`;
        imageWrapper.style.backgroundPosition = 'center';  // 画像を中央に配置
        imageWrapper.style.backgroundRepeat = 'no-repeat'; // 画像が繰り返し表示されないように
        };
        reader.readAsDataURL(file);
      }
    });
  });
</script>
@endsection