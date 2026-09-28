{{-- Figma node 1:5096 — Operator card (mobile, 350px wide): full-bleed photo, rating, name, 2-line blurb, meta row + round arrow button. --}}
@props([
    'name',
    'description',
    'rating',
    'image',
    'routes' => 2,
    'vessels' => 0,
    'href' => '#',
    'delay' => 0,
])

<a href="{{ $href }}" data-reveal style="--reveal-delay: {{ $delay }}ms"
   class="group flex w-full flex-col overflow-hidden rounded-[12px] bg-surface shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)]
          transition-[transform,box-shadow] duration-500 ease-smooth active:scale-[0.99]">
    <div class="h-[192px] w-full overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
    </div>

    <div class="p-[24px]">
        @if ($rating)
            <div class="flex items-center gap-[4px]">
                <img src="{{ asset('images/icons/mobile/list/star-blue.svg') }}" alt="" class="h-[11px] w-[12px]">
                <span class="text-[14px] font-bold leading-[20px] tracking-[0.7px] text-[#414751]">{{ $rating }}</span>
            </div>
        @endif

        <h2 class="mt-[8px] text-[24px] font-semibold leading-[32px] text-[#181c1e]">{{ $name }}</h2>

        <p class="mt-[8px] line-clamp-2 text-[16px] leading-[24px] text-[#414751]">{{ $description }}</p>

        <div class="mt-[16px] flex items-center justify-between border-t border-[#e5e9eb] pt-[17px]">
            <div class="flex items-center gap-[24px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">
                <span class="flex items-center gap-[8px]">
                    <img src="{{ asset('images/icons/mobile/list/routes.svg') }}" alt="" class="size-[10.5px]">
                    {{ $routes }} {{ \Illuminate\Support\Str::plural('Route', $routes) }}
                </span>
                <span class="flex items-center gap-[8px]">
                    <img src="{{ asset('images/icons/mobile/list/boat.svg') }}" alt="" class="h-[11.7px] w-[10.8px]">
                    {{ $vessels }} Pax
                </span>
            </div>

            <span class="flex size-[40px] items-center justify-center rounded-full bg-brand drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                <img src="{{ asset('images/icons/mobile/list/arrow-right.svg') }}" alt="" class="size-[16px]">
            </span>
        </div>
    </div>
</a>
