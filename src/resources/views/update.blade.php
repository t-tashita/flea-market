@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/update.css')}}">
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
<div class="update-form">
  <div class="update-form__inner">
    <h1 class="update-form__heading">プロフィール設定</h2>
    <form class="update-form__form" action="/mypage/profile" method="post" enctype="multipart/form-data">
      @csrf
      <div class="update-form__group">
        <div class="update-form__profile">
          <img class="update-form__image" id="preview" src="{{ asset('storage/image_user/' . $user->user_image) }}" alt="プロフィール画像" >
          <input type="file" id="image" name="image" accept="image/*" hidden>
          <label for="image" class="custom-file-label">画像を選択する</label>
        </div>
        @error('image')
        <p class="update-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="update-form__group">
        <label class="update-form__label" for="name">ユーザー名</label>
        <input class="update-form__input" type="text" name="name" id="name" value="{{ old( 'name', $user->name) }}">
        @error('name')
        <p class="update-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="update-form__group">
        <label class="update-form__label" for="postal_code">郵便番号</label>
        <input class="update-form__input" type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $user->user_postal_code) }}">
        @error('postal_code')
        <p class="update-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="update-form__group">
        <label class="update-form__label" for="address">住所</label>
        <input class="update-form__input" type="text" name="address" id="address" value="{{ old('address', $user->user_address) }}">
        @error('address')
        <p class="update-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <div class="update-form__group">
        <label class="update-form__label" for="building">建物名</label>
        <input class="update-form__input" type="text" name="building" id="building" value="{{ old('building', $user->user_building) }}">
        @error('building')
        <p class="update-form__error-message">{{ $message }}</p>
        @enderror
      </div>
      <input class="update-form__btn btn" type="submit" value="更新する">
    </form>
  </div>
</div>
@endsection

@section('script')
<script>
  const imageInput = document.getElementById('image');
  const previewImage = document.getElementById('preview');

  imageInput.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();

      reader.onload = function (e) {
        previewImage.src = e.target.result;
      };

      reader.readAsDataURL(file);
    }
  });
</script>
@endsection