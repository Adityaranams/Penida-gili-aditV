{{-- Figma node 1:5284 — fixed bottom tab bar, mobile only. --}}
@php
    // Icon boxes match the Figma Nav (10:5813) per tab: the glyphs are not all square.
    $tabs = [
        'home'     => ['label' => 'Home',    'icon' => 'home.svg',     'w' => 16, 'h' => 18, 'route' => route('home')],
        'boat'     => ['label' => 'Boat',    'icon' => 'boat.svg',     'w' => 18.446, 'h' => 20, 'route' => route('boats.index')],
        'activity' => ['label' => 'Activity','icon' => 'activity.svg', 'w' => 20, 'h' => 22, 'route' => route('activities.index')],
        'hotel'    => ['label' => 'Hotels',  'icon' => 'hotels.svg',   'w' => 22, 'h' => 15, 'route' => route('hotels.index')],
        'artikel'  => ['label' => 'Article', 'icon' => 'article.svg',  'w' => 18, 'h' => 18, 'route' => route('articles.index')],
    ];

    // The page's own nav highlight drives the tab bar too, so the two never disagree.
    $current = trim($__env->yieldContent('nav-active')) ?: null;
@endphp

{{-- Scrolling down shrinks the bar (see resources/js/tabbar-shrink.js); scrolling back up restores it. --}}
<nav data-tabbar class="tabbar fixed inset-x-0 bottom-0 z-50 border-t border-[#e5e7eb] bg-surface pb-[env(safe-area-inset-bottom)] lg:hidden" aria-label="Primary">
    <ul class="tabbar__row flex items-center justify-around">
        @foreach ($tabs as $key => $tab)
            <li>
                <a href="{{ $tab['route'] }}"
                   @class(['tabbar__tab flex flex-col items-center gap-[4px]'])
                   @if ($current === $key) aria-current="page" @endif>
                    {{-- The SVGs ship with a hard-coded fill, so paint them via mask so the colour follows the active state. --}}
                    <span aria-hidden="true"
                          style="width: {{ $tab['w'] }}px; height: {{ $tab['h'] }}px; -webkit-mask: url('{{ asset('images/icons/tabbar/'.$tab['icon']) }}') no-repeat center / contain; mask: url('{{ asset('images/icons/tabbar/'.$tab['icon']) }}') no-repeat center / contain;"
                          @class([
                              'tabbar__icon block transition-colors duration-300',
                              'bg-brand' => $current === $key,
                              'bg-[#64748b]' => $current !== $key,
                          ])></span>
                    <span @class([
                        'tabbar__label text-[10px] font-semibold uppercase leading-[15px] tracking-[0.5px]',
                        'text-brand' => $current === $key,
                        'text-[#64748b]' => $current !== $key,
                    ])>{{ $tab['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
