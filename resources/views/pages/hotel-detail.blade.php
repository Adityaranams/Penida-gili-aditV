{{-- Figma node 1:1694 — hotel Detail (desktop) / 1:3398 — Hotels details full (mobile) --}}
@extends('layouts.app')

@section('title', $hotel['name'])

@section('nav-active', 'hotel')

@section('hero')
    {{-- Mobile (< lg) gets its own hero + sections from the mobile Figma frame. --}}
    @include('partials.hotel.mobile-detail', ['hotel' => $hotel])

    <header class="relative hidden h-[184px] w-full overflow-hidden lg:block">
        <img src="{{ asset('images/activities/detail/hero-strip.png') }}" alt=""
             class="absolute inset-0 size-full object-cover">

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'hotel'])
        </div>
    </header>
@endsection

@section('content')
    <div class="hidden lg:block">
    {{-- Figma node 1:1698 — hero gallery, 1 large + 4 small --}}
    <section class="container-page pt-[54px]">
        <div data-reveal class="grid [&>*]:min-w-0 gap-[20px] overflow-hidden rounded-detail md:grid-cols-4 md:grid-rows-2">
            @php($frames = array_slice($hotel['gallery_photos'], 0, 5))
            @php($extra = count($hotel['gallery_photos']) - count($frames))

            @foreach ($frames as $photo)
                <button type="button" data-lightbox-group="hotel" data-lightbox="{{ $photo['url'] }}" data-lightbox-alt="{{ $photo['alt'] }}"
                        @class([
                            'group relative block cursor-zoom-in overflow-hidden rounded-detail',
                            'md:col-span-2 md:row-span-2' => $loop->first,
                            'h-[180px] md:h-auto' => ! $loop->first,
                        ])>
                    <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}"
                         class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">

                    {{-- Anything that does not fit is counted on the last frame. --}}
                    @if ($loop->last && $extra > 0)
                        <span class="absolute inset-0 flex items-center justify-center bg-black/45 text-[29.9px] font-semibold leading-[40px] text-white transition-colors duration-300 group-hover:bg-black/55">
                            +{{ $extra }}
                        </span>
                    @endif
                </button>
            @endforeach

            <x-gallery-extras :photos="array_slice($hotel['gallery_photos'], 5)" group="hotel" />
        </div>
    </section>

    {{-- Figma node 1:1708 — content split --}}
    <div class="container-page mt-[40px] grid [&>*]:min-w-0 gap-[32px] pb-[31px] lg:pb-[74px] lg:grid-cols-[minmax(0,1013fr)_minmax(0,491fr)] lg:gap-[30px]">
        <div>
            {{-- Figma node 1:1710 — overview header --}}
            <div data-reveal class="border-b border-editorial-line pb-[41px]">
                <p class="flex items-center gap-[10px]">
                    <span class="rounded-full bg-[#d5e2e9] px-[15px] py-[5px] text-[17.4px] font-semibold leading-[25px] tracking-[0.87px] text-[#58646a]">
                        {{ $hotel['category'] }}
                    </span>
                    <span class="flex items-center" role="img" aria-label="{{ $hotel['stars'] }} star hotel">
                        @for ($star = 1; $star <= $hotel['stars']; $star++)
                            <img src="{{ asset('images/icons/hotel/star.svg') }}" alt="" class="h-[17.7px] w-[18.7px]">
                        @endfor
                    </span>
                </p>

                <h1 class="mt-[10px] text-[25px] lg:text-[59.8px] font-bold leading-[34px] lg:leading-[74.7px] tracking-[-1.2px] text-editorial-ink">{{ $hotel['name'] }}</h1>

                <p class="mt-[10px] flex flex-wrap items-center text-[19.9px] leading-[29.9px] text-editorial-body">
                    <img src="{{ asset('images/icons/hotel/pin.svg') }}" alt="" class="h-[25px] w-[30px]">
                    {{ $hotel['address'] }}
                    <a href="#location" class="pl-[20px] text-brand transition-colors hover:underline">View on map</a>
                </p>

                <p class="mt-[20px] text-[22.4px] leading-[36.4px] text-editorial-body">{{ $hotel['description'] }}</p>
            </div>

            {{-- Figma node 1:1736 — premium amenities --}}
            <section class="border-b border-editorial-line py-[41px]">
                <h2 data-reveal class="text-[24px] lg:text-[44.8px] font-bold leading-[30px] lg:leading-[54.8px] tracking-[-0.45px] text-editorial-ink">Premium Amenities</h2>

                <ul class="mt-[30px] grid gap-x-[20px] gap-y-[30px] sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($hotel['amenities'] as $index => $amenity)
                        <li data-reveal style="--reveal-delay: {{ ($index % 3) * 90 }}ms" class="flex items-center gap-[15px]">
                            <span class="flex size-[49.8px] shrink-0 items-center justify-center rounded-full bg-editorial-rule">
                                <img src="{{ asset('images/icons/hotel/'.$amenity['icon']) }}" alt="" class="size-[25px] object-contain">
                            </span>
                            <span class="text-[19.9px] leading-[29.9px] text-editorial-ink">{{ $amenity['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Figma node 1:1776 — room options --}}
            <section class="border-b border-editorial-line py-[41px]">
                <h2 data-reveal class="text-[24px] lg:text-[44.8px] font-bold leading-[30px] lg:leading-[54.8px] tracking-[-0.45px] text-editorial-ink">Select Your Room</h2>

                <div class="mt-[30px] flex flex-col gap-[30px]">
                    @foreach ($hotel['rooms'] as $index => $room)
                        <article data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                                 class="group flex flex-col overflow-hidden rounded-detail border border-editorial-line bg-surface shadow-editorial
                                        transition-[transform,box-shadow] duration-500 ease-smooth hover:-translate-y-1 hover:shadow-card-hover sm:flex-row">
                            <div class="h-[220px] w-full shrink-0 overflow-hidden sm:h-auto sm:w-[337px]">
                                <img src="{{ \App\Support\ImagePath::url($room['image'] ?: 'room-deluxe.png', 'hotels/detail') }}" alt="{{ $room['name'] }}"
                                     class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
                            </div>

                            <div class="flex flex-1 flex-col justify-between p-[30px]">
                                <div class="flex flex-col gap-[10px] pb-[20px]">
                                    <h3 class="text-[29.9px] font-semibold leading-[39.9px] text-editorial-ink">{{ $room['name'] }}</h3>
                                    <p class="text-[19.9px] leading-[29.9px] text-editorial-body">{{ $room['description'] }}</p>

                                    <ul class="flex items-center gap-[20px] pt-[10px] text-[17.4px] leading-[25px] text-editorial-body">
                                        <li class="flex items-center gap-[5px]">
                                            <img src="{{ asset('images/icons/hotel/guests.svg') }}" alt="" class="h-[15px] w-[20.5px]">
                                            {{ $room['guests_label'] }}
                                        </li>
                                        <li class="flex items-center gap-[5px]">
                                            <img src="{{ asset('images/icons/hotel/bed.svg') }}" alt="" class="h-[13px] w-[18.7px]">
                                            {{ $room['bed'] }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="flex flex-wrap items-end justify-between gap-4 border-t border-editorial-line pt-[21px]">
                                    <p>
                                        <span class="block text-[17.4px] font-semibold leading-[25px] tracking-[0.87px] text-editorial-body">From</span>
                                        <span class="text-[29.9px] font-semibold leading-[39.9px] text-brand">{{ $room['price_label'] }}</span>
                                        <span class="text-[17.4px] leading-[25px] text-editorial-body">/ night</span>
                                    </p>

                                    <a href="{{ route('hotels.order', [$hotel['slug'], 'room' => $room['id']]) }}"
                                       @class([
                                           'rounded-full px-[30px] py-[15px] text-[17.4px] font-semibold leading-[25px] tracking-[0.87px] shadow-sm transition-transform duration-300 ease-smooth hover:-translate-y-0.5',
                                           'bg-brand text-white' => $loop->first,
                                           'border border-[rgba(192,199,211,0.5)] bg-editorial-rule text-editorial-ink' => ! $loop->first,
                                       ])>
                                        Select Room
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- Figma node 1:1832 — location --}}
            <section id="location" class="border-b border-editorial-line py-[41px]">
                <h2 data-reveal class="text-[24px] lg:text-[44.8px] font-bold leading-[30px] lg:leading-[54.8px] tracking-[-0.45px] text-editorial-ink">Location</h2>

                <div data-reveal class="mt-[30px] rounded-detail border border-editorial-line bg-[#f1f4f6] p-[21px]">
                    <div class="h-[319px] overflow-hidden rounded-[10px] bg-[#e0e3e5]">
                        <img src="{{ asset('images/hotels/detail/map.png') }}" alt="Map of {{ $hotel['name'] }}" class="size-full object-cover">
                    </div>

                    <p class="mt-[20px] flex items-start gap-[20px]">
                        <img src="{{ asset('images/icons/hotel/pin-map.svg') }}" alt="" class="h-[30px] w-[20px] shrink-0">
                        <span>
                            <span class="block text-[29.9px] font-semibold leading-[39.9px] text-editorial-ink">{{ $hotel['name'] }}</span>
                            <span class="block text-[19.9px] leading-[29.9px] text-editorial-body">{{ $hotel['full_address'] }}</span>
                        </span>
                    </p>
                </div>
            </section>

            {{-- Figma node 1:1846 — guest experiences --}}
            <section class="pt-[41px]">
                <h2 data-reveal class="text-[24px] lg:text-[44.8px] font-bold leading-[30px] lg:leading-[54.8px] tracking-[-0.45px] text-editorial-ink">Guest Experiences</h2>

                <div class="mt-[30px] grid [&>*]:min-w-0 gap-[30px] sm:grid-cols-2">
                    @foreach ($hotel['reviews'] as $index => $review)
                        <figure data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                                class="flex flex-col gap-[15px] rounded-detail border border-editorial-line bg-surface p-[31px] shadow-detail
                                       transition-[transform,box-shadow] duration-500 ease-smooth hover:-translate-y-1 hover:shadow-card-hover">
                            <div class="flex items-center gap-[10px]" role="img" aria-label="{{ $review['stars'] }} out of 5 stars">
                                @for ($star = 1; $star <= $review['stars']; $star++)
                                    <img src="{{ asset('images/icons/hotel/star-sm.svg') }}" alt="" class="h-[13.8px] w-[14.5px]">
                                @endfor
                            </div>

                            <blockquote class="text-[19.9px] leading-[29.9px] text-editorial-ink">{{ $review['quote'] }}</blockquote>

                            <figcaption class="flex items-center gap-[15px] pt-[5px]">
                                <span @class([
                                    'flex size-[49.8px] shrink-0 items-center justify-center rounded-full text-[19.9px] font-bold leading-[29.9px]',
                                    'bg-[#2178c3] text-[#fdfcff]' => $loop->first,
                                    'bg-[#d5e2e9] text-[#58646a]' => ! $loop->first,
                                ])>{{ $review['initials'] }}</span>
                                <span>
                                    <span class="block text-[17.4px] font-semibold leading-[25px] tracking-[0.87px] text-editorial-ink">{{ $review['name'] }}</span>
                                    <span class="block text-[15px] leading-[20px] text-editorial-body">{{ $review['stayed'] }}</span>
                                </span>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </section>
        </div>

        @include('partials.hotel.booking-sidebar', ['hotel' => $hotel])
    </div>
    </div>
@endsection
