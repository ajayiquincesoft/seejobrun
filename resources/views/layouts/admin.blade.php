<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>See Job Run | Admin Dashboard</title>
    <link rel="icon" href="{{ asset('Icon.png') }}" type="image/icon type">
    <link rel="stylesheet" href="{{ asset('admin_assets/vendor/nucleo/css/nucleo.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('admin_assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/argon.css?v=1.2.1') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/style.css?v=1.2.1') }}" type="text/css">
  </head>

  <body class="bg-default">
    <div class="main-content">
      <div class="header py-7 py-lg-8 pt-lg-9">
      </div>

      @yield('content')
    </div>

    <script src="{{ asset('admin_assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/js-cookie/js.cookie.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('admin_assets/vendor/jquery-scroll-lock/dist/jquery-scrollLock.min.js') }}"></script>

  </body>