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

{{-- A floating liquid-glass bar: translucent, blurred and saturated over
     whatever scrolls beneath, lit along its top edge. The captions stay put at
     every scroll position; resources/js/tabbar-shrink.js only reports the space
     it occupies so the things pinned above it can sit clear. --}}
<nav data-tabbar class="tabbar fixed inset-x-[14px] bottom-[max(14px,env(safe-area-inset-bottom))] z-50 rounded-[26px]
                        border border-white/60 bg-white/72 backdrop-blur-2xl backdrop-saturate-150
                        shadow-[inset_0_1px_0_rgba(255,255,255,0.75),inset_0_-1px_0_rgba(255,255,255,0.25),0_10px_30px_rgba(15,34,54,0.18)]
                        lg:hidden" aria-label="Primary">
    <ul class="tabbar__row flex items-center justify-around px-[6px]">
        @foreach ($tabs as $key => $tab)
            <li>
                <a href="{{ $tab['route'] }}"
                   @class([
                       'tabbar__tab relative flex min-w-[60px] flex-col items-center gap-[4px] rounded-[18px] px-[10px] py-[7px]',
                       'tabbar__tab--on' => $current === $key,
                   ])
                   @if ($current === $key) aria-current="page" @endif>
                    @if ($current === $key)
                        <span class="tabbar__pill" aria-hidden="true"></span>
                    @endif

                    {{-- The SVGs ship with a hard-coded fill, so paint them via mask so the colour follows the active state. --}}
                    <span aria-hidden="true"
                          style="width: {{ $tab['w'] }}px; height: {{ $tab['h'] }}px; -webkit-mask: url('{{ asset('images/icons/tabbar/'.$tab['icon']) }}') no-repeat center / contain; mask: url('{{ asset('images/icons/tabbar/'.$tab['icon']) }}') no-repeat center / contain;"
                          @class([
                              'tabbar__icon relative block transition-colors duration-300',
                              'bg-brand' => $current === $key,
                              'bg-[#64748b]' => $current !== $key,
                          ])></span>
                    <span @class([
                        'tabbar__label relative text-[10px] font-semibold uppercase leading-[15px] tracking-[0.5px]',
                        'text-brand' => $current === $key,
                        'text-[#64748b]' => $current !== $key,
                    ])>{{ $tab['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
