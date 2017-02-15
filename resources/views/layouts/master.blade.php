<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
  </head>
  <body>
    @if (Auth::check())
    <?php
      $user_id = auth()->id();
    ?>
    @endif
    <header>
      @include('layouts.header')
    </header>
    @yield('content')
  </body>
</html>
