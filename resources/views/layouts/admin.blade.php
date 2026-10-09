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
                    <label class="relative block w-[427px] max-w-full">
                        <span class="sr-only">Search</span>
                        <input type="search" placeholder="Search"
                               class="h-[58.7px] w-full rounded-admin-pill bg-admin-field pl-[24px] pr-[56px] text-[21.3px] text-admin-ink placeholder:text-admin-nav focus:outline-2 focus:outline-editorial">
                        <img src="{{ asset('images/icons/admin/search.svg') }}" alt=""
                             class="pointer-events-none absolute right-[24px] top-1/2 size-[20px] -translate-y-1/2">
                    </label>

                    <button type="button" aria-label="Notifications"
                            class="relative flex size-[58.7px] items-center justify-center rounded-full bg-admin-field transition-colors duration-300 hover:bg-editorial-rule">
                        <img src="{{ asset('images/icons/admin/bell.svg') }}" alt="" class="size-[22px]">
                        <span class="absolute right-[18px] top-[17px] size-[8px] rounded-full bg-[#e11d48]"></span>
                    </button>
                </div>
            </header>

            <main>
                <x-admin.flash />
                @yield('content')
            </main>
        </div>
    </div>

    @include('partials.admin.delete-modal')
</body>
</html>
