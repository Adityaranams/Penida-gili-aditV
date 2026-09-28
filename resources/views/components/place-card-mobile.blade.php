{{-- Figma node 1:3859 — Activity card (mobile, 350px wide). Hotels reuse the same shell with different `meta`. --}}
@props([
    'name',
    'description',
    'rating',
    'image',
    'price',
    'meta' => [],
    'href' => '#',
    'delay' => 0,
])

@php
    // Meta icons are keyed by the desktop icon name so the same data feeds both layouts.
    $metaIcon = [
        'location.svg' => ['file' => 'meta-location.svg', 'w' => 10.7, 'h' => 13.3],
        'clock.svg'    => ['file' => 'meta-clock.svg',    'w' => 13.3, 'h' => 13.3],
        'category.svg' => ['file' => 'meta-category.svg', 'w' => 13.3, 'h' => 12],
    ];
    $fullStars = (int) floor((float) $rating);
    $hasHalf   = ((float) $rating - $fullStars) >= 0.5;
@endphp

<a href="{{ $href }}" data-reveal style="--reveal-delay: {{ $delay }}ms"
   class="group flex w-full flex-col overflow-hidden rounded-[12px] bg-surface shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)]
          transition-[transform,box-shadow] duration-500 ease-smooth active:scale-[0.99]">
    <div class="h-[192px] w-full overflow-hidden">
        <img src="{{ $image }}" alt="{{ $name }}" class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
    </div>

    <div class="p-[24px]">
        <div class="flex items-center gap-[4px]" role="img" aria-label="{{ $rating }} out of 5 stars">
            @for ($star = 1; $star <= 5; $star++)
                <img src="{{ asset('images/icons/mobile/list/'.($star <= $fullStars ? 'star-gold.svg' : 'star-gold-half.svg')) }}" alt="" class="h-[12.7px] w-[13.3px]">
            @endfor
            <span class="pl-[4px] text-[16px] leading-[24px] text-[#414751]">{{ $rating }}</span>
        </div>

        <h2 class="mt-[8px] text-[20px] font-semibold uppercase leading-[25px] text-[#181c1e]">{{ $name }}</h2>

        <p class="mt-[12px] line-clamp-3 text-[14px] leading-[21px] text-[#414751]">{{ $description }}</p>

        <ul class="mt-[16px] flex flex-wrap gap-x-[16px] gap-y-[8px] text-[13px] leading-[19.5px] text-[#414751]">
            @foreach ($meta as $item)
                @php $icon = $metaIcon[$item['icon']] ?? ['file' => 'meta-category.svg', 'w' => 13.3, 'h' => 13.3]; @endphp
                <li class="flex items-center gap-[8px]">
                    <img src="{{ asset('images/icons/mobile/list/'.$icon['file']) }}" alt="" style="width: {{ $icon['w'] }}px; height: {{ $icon['h'] }}px">
                    {{ $item['label'] }}
                </li>
            @endforeach
        </ul>

        <div class="mt-[24px] flex items-end justify-between border-t border-[rgba(192,199,211,0.3)] pt-[17px]">
            <div class="flex flex-col gap-[4px]">
                <span class="text-[12px] leading-[18px] text-[#414751]">Start from</span>
                <span class="text-[18px] font-bold leading-[27px] text-[#181c1e]">{{ $price }}</span>
            </div>

            <span class="flex size-[40px] items-center justify-center rounded-full bg-brand shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]">
                <img src="{{ asset('images/icons/mobile/list/arrow-right-sm.svg') }}" alt="" class="size-[13.3px]">
            </span>
        </div>
    </div>
</a>
