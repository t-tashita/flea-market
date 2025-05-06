<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CoachTech FleaMarket</title>
  <link rel="stylesheet" href="https://unpkg.com/ress/dist/ress.min.css" />
  <link rel="stylesheet" href="{{ asset('css/common.css')}}">
  @yield('css')
</head>

<body>
  <div class="app">
    <header class="header">
      <a href="/" class="header__logo"><img class="header__logo-img" src="{{ asset('storage/logo.svg') }}" alt="CoachTech" /></a>
      @yield('navigation')
    </header>
    <div class="content">
      @yield('content')
    </div>
  </div>
</body>
@yield('script')
</html>