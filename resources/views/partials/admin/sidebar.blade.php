{{-- Figma node 1:6896 — admin navbar component --}}
@php
    $menu = [
        'dashboard' => ['label' => 'Dashboard',      'icon' => 'nav-dashboard.svg', 'route' => route('admin.dashboard')],
        'boat'      => ['label' => 'Boat',           'icon' => 'nav-boat.svg',      'route' => route('admin.boats')],
        'schedule'  => ['label' => 'Schedule',       'icon' => 'nav-schedule.svg',  'route' => route('admin.schedules')],
        'activity'  => ['label' => 'Activity',       'icon' => 'nav-activity.svg',  'route' => route('admin.activities')],
        'hotel'     => ['label' => 'Hotel',          'icon' => 'nav-hotel.svg',     'route' => route('admin.hotels')],
        'article'   => ['label' => 'Article',        'icon' => 'nav-article.svg',   'route' => route('admin.articles')],
        'report'    => ['label' => 'Booking Report', 'icon' => 'nav-report.svg',    'route' => route('admin.report')],
        'setting'   => ['label' => 'Setting',        'icon' => 'nav-settings.svg',  'route' => route('admin.settings')],
    ];

    $current = trim($__env->yieldContent('admin-active')) ?: 'dashboard';
@endphp

<aside class="flex w-[259px] shrink-0 flex-col px-[10px] pb-[24px] pt-[27px]">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-[12px] px-[9px]">
        <img src="{{ asset('images/icons/admin/logo.svg') }}" alt="" class="h-[54px] w-[54px]">
        <span class="text-[24px] font-medium text-ink">Boat Booking</span>
    </a>

    <p class="mt-[56px] px-[19px] text-[18.7px] text-admin-label">MAIN MENU</p>

    <nav class="mt-[39px] flex flex-col gap-[11px]" aria-label="Admin">
        @foreach ($menu as $key => $item)
            <a href="{{ $item['route'] }}"
               @class([
                   'flex h-[56px] items-center gap-[14px] whitespace-nowrap rounded-admin-pill px-[20px] text-[18.5px] transition-colors duration-300',
                   'border border-admin-line bg-surface font-semibold text-editorial' => $current === $key,
                   'text-admin-nav hover:bg-surface' => $current !== $key,
               ])
               @if ($current === $key) aria-current="page" @endif>
                {{-- Icons ship with a hard-coded fill, so paint them via mask and let the colour follow the active state. --}}
                <span aria-hidden="true"
                      style="-webkit-mask: url('{{ asset('images/icons/admin/'.$item['icon']) }}') no-repeat center / contain; mask: url('{{ asset('images/icons/admin/'.$item['icon']) }}') no-repeat center / contain;"
                      @class([
                          'block size-[22px] shrink-0 transition-colors duration-300',
                          'bg-editorial' => $current === $key,
                          'bg-[#b1b1b1]' => $current !== $key,
                      ])></span>
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-auto border-t border-admin-line pt-[24px]">
        {{-- Figma node I1:6896;1:10690 — account card --}}
        <div class="rounded-[13px] border border-admin-line bg-gradient-to-b from-[#e8f0f2] to-surface px-[22px] py-[28px] text-center">
            <img src="{{ asset('images/icons/admin/crown.svg') }}" alt="" class="mx-auto size-[32px]">
            <p class="mt-[16px] text-[32px] text-admin-ink">Hi, {{ \Illuminate\Support\Str::before(auth()->user()?->name ?? 'Admin', ' ') }}!</p>
            <p class="mt-[8px] text-[16px] leading-[1.2] text-admin-muted">Full administrative access is granted.</p>

            <form action="{{ route('admin.logout') }}" method="post" class="mt-[20px]">
                @csrf
                <button type="submit"
                        class="flex h-[40px] w-full items-center justify-center gap-[8px] rounded-[10.7px] bg-editorial text-[16px] tracking-[-0.4px] text-[#fafafa]
                               transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                    <img src="{{ asset('images/icons/admin/logout.svg') }}" alt="" class="size-[16px] -scale-x-100">
                    Logout
                </button>
            </form>

            <p class="mt-[10px] text-[11px] tracking-[-0.4px] text-admin-muted">For help, contact system support.</p>
        </div>
    </div>
</aside>
