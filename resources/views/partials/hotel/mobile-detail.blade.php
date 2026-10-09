{{-- Figma node 1:3398 — "Hotels details full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['hotel'])

@php
    // Mobile shows the first four amenities with its own icon set (keyed by the desktop icon name).
    $mAmenityIcon = [
        'wifi.svg'       => ['file' => 'amenity-wifi.svg',       'w' => 24, 'h' => 17],
        'pool.svg'       => ['file' => 'amenity-pool.svg',       'w' => 20, 'h' => 18],
        'spa.svg'        => ['file' => 'amenity-spa.svg',        'w' => 20, 'h' => 20],
        'restaurant.svg' => ['file' => 'amenity-restaurant.svg', 'w' => 15, 'h' => 20],
    ];
@endphp

<div class="bg-[#f7fafc] lg:hidden" data-room-picker data-nights="{{ $hotel['default_nights'] }}">
    {{-- Hero (1:3442) --}}
    <header class="relative h-[320px] w-full overflow-hidden">
        <x-photo-swiper :photos="$hotel['gallery_photos']" :counter="false" />
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

        <div class="absolute inset-x-[20px] bottom-[16px] flex items-end justify-between gap-[12px]">
            <div data-reveal>
                <h1 class="text-[28px] font-bold leading-[36px] text-white">{{ $hotel['name'] }}</h1>
                <p class="mt-[4px] flex items-center gap-[4px] text-[16px] leading-[24px] text-white/90">
                    <img src="{{ asset('images/icons/mobile/detail/pin-white.svg') }}" alt="" class="h-[13.3px] w-[10.7px]">
                    {{ $hotel['address'] }}
                </p>
            </div>

            <span data-reveal style="--reveal-delay: 90ms" class="flex shrink-0 items-center gap-[4px] rounded-[8px] bg-[#f7fafc] px-[12px] py-[4px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)]">
                <img src="{{ asset('images/icons/mobile/detail/star-rating.svg') }}" alt="" class="h-[14.3px] w-[15px]">
                <span class="text-[16px] font-bold leading-[24px] text-brand">{{ $hotel['rating'] }}</span>
            </span>
        </div>
    </header>

    <div class="flex flex-col gap-[32px] px-[20px] pb-[32px] pt-[16px]">
        {{-- Description (1:3460) --}}
        <div data-reveal class="rich-text text-[16px] leading-[24px] text-[#414751]">{!! $hotel['description'] !!}</div>

        {{-- Amenities (1:3462) --}}
        <section class="flex flex-col gap-[16px]">
            <h2 data-reveal class="text-[20px] font-bold leading-[30px] text-brand">Amenities</h2>

            <ul class="grid grid-cols-2 gap-[16px]">
                @foreach (array_slice($hotel['amenities'], 0, 4) as $index => $amenity)
                    @php $icon = $mAmenityIcon[$amenity['icon']] ?? null; @endphp
                    <li data-reveal style="--reveal-delay: {{ $index * 60 }}ms" class="flex min-h-[56px] items-center gap-[12px] rounded-[12px] bg-[#f1f4f6] p-[16px]">
                        <img src="{{ $icon ? asset('images/icons/mobile/detail/'.$icon['file']) : asset('images/icons/hotel/'.$amenity['icon']) }}" alt=""
                             class="shrink-0 object-contain" style="width: {{ $icon['w'] ?? 20 }}px; height: {{ $icon['h'] ?? 20 }}px">
                        <span class="text-[16px] leading-[24px] text-[#181c1e]">{{ $amenity['shortLabel'] ?? $amenity['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Select Room (1:3486) --}}
        <section class="flex flex-col gap-[16px]">
            <h2 data-reveal class="text-[20px] font-bold leading-[30px] text-brand">Select Room</h2>

            <div class="flex flex-col gap-[16px]">
                @foreach ($hotel['rooms'] as $index => $room)
                    <article data-room-card data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                             class="relative flex flex-col gap-[12px] overflow-hidden rounded-[12px] border border-[#c0c7d3] bg-white p-[17px]
                                    drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)] transition-colors duration-300">

                        <div class="flex items-start justify-between gap-[12px]">
                            <div>
                                <h3 class="text-[18px] leading-[27px] text-[#181c1e]">{{ $room['name'] }}</h3>
                                <p class="text-[14px] leading-[21px] text-[#414751]">{{ $room['bed'] }} • {{ $room['guests_label'] }}</p>
                            </div>
                            <p class="shrink-0 whitespace-nowrap pt-[2px]">
                                <span class="text-[16px] font-bold leading-[24px] text-brand">{{ $room['price_label'] }}</span>
                                <span class="text-[12px] leading-[18px] text-[#414751]">/night</span>
                            </p>
                        </div>

                        {{-- Picking a room stays on the page: the button turns solid, the
                             bar at the foot shows that room's total, and Book Now carries
                             it to the order screen. resources/js/room-picker.js. --}}
                        <button type="button" data-room-pick
                                data-room-id="{{ $room['id'] }}"
                                data-room-nightly="{{ $room['price_per_night'] }}"
                                class="flex min-h-[44px] items-center justify-center rounded-[8px] border border-[#2178c3] bg-white
                                       text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-brand
                                       transition-colors duration-300
                                       hover:bg-brand hover:text-white active:bg-brand active:text-white">
                            <span data-room-pick-label>Select Room</span>
                        </button>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Location (1:3516) --}}
        <section class="flex flex-col gap-[8px]">
            <h2 data-reveal class="text-[20px] font-bold leading-[30px] text-brand">Location</h2>

            <div data-reveal class="relative h-[200px] overflow-hidden rounded-[12px] shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
                <img src="{{ asset('images/hotels/detail/map.png') }}" alt="Map of {{ $hotel['name'] }}" class="size-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center bg-[rgba(0,94,161,0.1)]">
                    <img src="{{ asset('images/icons/mobile/detail/map-pin.svg') }}" alt="" class="h-[49px] w-[37px] drop-shadow-md">
                </div>
            </div>

            <p class="text-center text-[14px] leading-[21px] text-[#414751]">{{ $hotel['full_address'] }}</p>
        </section>

        <div class="h-[96px]" aria-hidden="true"></div>
    </div>

    {{-- Prominent booking action (1:3527). Pinned above the tab bar so the price
         and the button stay in reach the whole way down the page, instead of
         waiting at the very bottom. --}}
    <div class="fixed inset-x-0 bottom-[var(--tabbar-h,78px)] z-30 flex items-center justify-between gap-[12px]
                border-t border-[rgba(192,199,211,0.5)] bg-white/95 px-[20px] py-[12px]
                backdrop-blur-[8px] shadow-[0px_-4px_16px_rgba(0,0,0,0.08)]
                transition-[bottom] duration-300 ease-smooth lg:hidden">
        <div class="min-w-0">
            <p data-room-total-caption class="text-[12px] leading-[18px] text-[#414751]">Pick a room to see the total</p>
            <p data-room-total class="truncate text-[22px] font-bold leading-[33px] text-brand" hidden></p>
        </div>

        {{-- Inert until a room is picked, so Book Now can never reach the order
             screen without one. --}}
        <a data-room-book
           href="{{ route('hotels.order', $hotel['slug']) }}"
           data-room-book-base="{{ route('hotels.order', $hotel['slug']) }}"
           aria-disabled="true"
           class="pointer-events-none flex min-h-[48px] shrink-0 items-center justify-center rounded-[12px] bg-brand px-[32px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-white opacity-45 shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)] transition-[opacity,transform] duration-300 ease-smooth active:scale-[0.98]">
            Book Now
        </a>
    </div>
</div>
