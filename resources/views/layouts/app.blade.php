<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Penida Gili') — Penida Gili</title>
    @hasSection('meta-description')
        <meta name="description" content="@yield('meta-description')">
    @endif
    @hasSection('meta-keywords')
        <meta name="keywords" content="@yield('meta-keywords')">
    @endif

    {{-- Set before paint so the reveal styles never hide content for no-JS visitors. --}}
    <script>document.documentElement.classList.add('js')</script>

    {{-- Figma type: Manrope (marketing) + Plus Jakarta Sans (search bar / quotes) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.nav-sticky', ['active' => trim($__env->yieldContent('nav-active')) ?: null])

    @include('partials.mobile-header')

    @yield('hero')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @hasSection('hide-whatsapp')
    @else
        @include('partials.whatsapp-float')
    @endif

    @include('partials.mobile-tabbar')

    @include('partials.confirm-modal')

    @include('partials.lightbox')
</body>
</html>
