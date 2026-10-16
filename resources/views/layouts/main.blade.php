<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors"/>
    <meta name="generator" content="Astro v5.13.2" />
    <title>CPM - Support</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/offcanvas-navbar/"/>
    <link rel="stylesheet" href="{{ asset('dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/offcanvas-navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/custom.css') }}">
  </head>
  <body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark" aria-label="Main navigation">
      @include('layouts.menu-list')
    </nav>
    
    
    <main class="container">
        @yield('content')
    </main>

    <script src="{{ asset('dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('dist/js/offcanvas-navbar.js') }}"></script>
  </body>
</html>
