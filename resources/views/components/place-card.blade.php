{{-- Figma nodes 1:628 (Activity) / 1:951 (Hotel) — Destination Card, 642px tall.

     The two pages share this shell and differ only in how many meta items sit
     above the price, so `meta` is a list of ['icon' => …, 'label' => …]. --}}
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

<a href="{{ $href }}" data-reveal style="--reveal-delay: {{ $delay }}ms"
   class="group flex w-full max-w-[472px] flex-col lg:h-[642px] rounded-card border-2 border-line-card bg-surface p-[20px] shadow-card
          transition-[transform,box-shadow,border-color] duration-500 ease-smooth
          hover:-translate-y-2 hover:border-brand/20 hover:shadow-card-hover">
    <div class="h-[237px] w-full overflow-hidden rounded-field">
        <img src="{{ $image }}" alt="{{ $name }}"
             class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
    </div>

    @if ((float) $rating > 0)
        <div class="mt-[23px] flex items-center gap-[19px]">
            <x-rating-stars :rating="$rating" />
            <span class="text-[16px] leading-[30px] text-ink">{{ $rating }}</span>
        </div>
    @endif

    <h3 class="mt-[23px] text-[24px] uppercase leading-[30px] text-ink transition-colors duration-300 group-hover:text-brand">
        {{ $name }}
    </h3>

    <p class="mt-[14px] line-clamp-3 text-[16px] leading-[30px] text-ink-muted">{{ $description }}</p>

    <ul class="mt-auto flex flex-wrap items-start gap-[15px] text-[15.3px] leading-[23px] text-[#414751]">
        @foreach ($meta as $item)
            <li class="flex items-center gap-[7.7px]">
                <img src="{{ asset('images/icons/meta/'.$item['icon']) }}" alt="" class="size-[19px] object-contain">
                {{ $item['label'] }}
            </li>
        @endforeach
    </ul>

    <div class="mt-[16px] flex items-end justify-between">
        <div>
            <p class="text-[16px] leading-[30px] text-ink-muted">Start from</p>
            <p class="text-[24px] font-bold leading-[30px] text-ink">{{ $price }}</p>
        </div>

        <span class="block size-[44.25px] -rotate-90 transition-transform duration-500 ease-smooth group-hover:translate-y-[-6px]">
            <img src="{{ asset('images/icons/arrow.svg') }}" alt="" class="size-full">
        </span>
    </div>
</a>
