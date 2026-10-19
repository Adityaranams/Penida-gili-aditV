<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Penida Gili</title>

    <script>document.documentElement.classList.add('js')</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-admin-canvas">
    <div class="flex min-h-screen">
        @include('partials.admin.sidebar')

        {{-- Figma node 1:6643 — the rounded white panel the console content sits on. --}}
        <div class="m-[27px] ml-0 flex-1 rounded-[24px] bg-surface px-[24px] pb-[40px] lg:px-[40px]">
            {{-- Figma node 1:6644 — header --}}
            <header class="flex flex-wrap items-start justify-between gap-4 pt-[55px]">
                <div>
                    <p class="text-[26.7px] leading-[1.5] text-admin-ink">Hello, {{ auth()->user()?->name ?? 'Admin' }}</p>
                    <p class="text-[16px] leading-[1.5] text-admin-muted">{{ now()->format('l, j F Y') }}</p>
                </div>

                <div class="flex items-center gap-[20px]">
                    {{-- Screens that are only ever reached from a menu say
                         @section('hide-search'); searching from them would
                         throw away what the person came there to do. --}}
                    @unless (trim($__env->yieldContent('hide-search')))
                        <form action="{{ route('admin.search') }}" method="get" role="search" class="relative block w-[427px] max-w-full">
                            <label for="console-search" class="sr-only">Search</label>
                            <input id="console-search" type="search" name="q" value="{{ request()->routeIs('admin.search') ? request('q') : '' }}"
                                   placeholder="Search boats, hotels, bookings…"
                                   class="h-[58.7px] w-full rounded-admin-pill bg-admin-field pl-[24px] pr-[56px] text-[18px] text-admin-ink placeholder:text-admin-nav focus:outline-2 focus:outline-editorial">
                            <button type="submit" aria-label="Search"
                                    class="absolute right-[12px] top-1/2 flex size-[36px] -translate-y-1/2 items-center justify-center rounded-full transition-colors duration-300 hover:bg-editorial-rule">
                                <img src="{{ asset('images/icons/admin/search.svg') }}" alt="" class="size-[20px]">
                            </button>
                        </form>
                    @endunless

                    @include('partials.admin.header-menus')
                </div>
            </header>

            <main>
                <x-admin.flash />
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.admin.delete-modal')
    @include('partials.admin.profile-modal')
</body>
</html>
