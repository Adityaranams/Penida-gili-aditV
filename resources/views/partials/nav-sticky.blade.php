{{-- Desktop nav that follows the scroll.

     The nav drawn on the hero sits inside a header with `overflow-hidden`, which
     stops `position: sticky` working there, and it is included from thirteen
     pages. So rather than rework all of them, this is a second bar: hidden at
     the top of the page, sliding in once the hero has scrolled away, in the
     solid colours it needs over white content. resources/js/sticky-nav.js
     decides when; the hero nav is untouched. --}}
@props(['active' => null])

@php
    $links = [
        'home' => ['label' => 'Home', 'route' => route('home')],
        'boat' => ['label' => 'Boat', 'route' => route('boats.index')],
        'activity' => ['label' => 'Activity', 'route' => route('activities.index')],
        'hotel' => ['label' => 'Hotel', 'route' => route('hotels.index')],
        'artikel' => ['label' => 'Article', 'route' => route('articles.index')],
    ];
@endphp

<div data-sticky-nav class="sticky-nav fixed inset-x-0 top-0 z-40 hidden px-[24px] pt-[14px] lg:block">
    {{-- Liquid glass: a translucent pill that saturates and blurs whatever
         scrolls beneath it, lit by a hairline highlight along its top edge and
         floated off the page with a soft shadow. --}}
    <nav class="mx-auto flex max-w-[1472px] items-center justify-between rounded-full
                border border-white/60 bg-white/72 px-[28px] py-[10px]
                backdrop-blur-2xl backdrop-saturate-150
                shadow-[inset_0_1px_0_rgba(255,255,255,0.75),inset_0_-1px_0_rgba(255,255,255,0.25),0_10px_34px_rgba(15,34,54,0.14)]">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="Penida Gili">
            <img src="{{ asset('images/logo/logo-mark-dark.svg') }}" alt="" class="h-[38px] w-[77px]">
            <img src="{{ asset('images/logo/logo-word-dark.svg') }}" alt="Penida Gili" class="h-[38px] w-[173px]">
        </a>

        <ul class="flex items-center gap-[48px] text-[16px] leading-[30px]">
            @foreach ($links as $key => $link)
                <li>
                    <a href="{{ $link['route'] }}"
                       class="{{ $active === $key ? 'font-bold text-ink' : 'font-normal text-ink-muted hover:text-ink' }}
                              relative transition-colors duration-300
                              after:absolute after:-bottom-1 after:left-0 after:h-px after:w-full after:origin-left after:bg-current
                              after:transition-transform after:duration-300 after:ease-smooth
                              {{ $active === $key ? 'after:scale-x-100' : 'after:scale-x-0 hover:after:scale-x-100' }}">
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <a href="https://wa.me/{{ config('penida.booking.whatsapp') }}" target="_blank" rel="noopener"
           class="flex h-[44px] w-[158px] items-center justify-center rounded-field bg-brand text-[15px] font-medium leading-[24px] text-on-brand
                  transition-[transform,box-shadow] duration-300 ease-smooth hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand/30">
            Contact Us
        </a>
    </nav>
</div>
