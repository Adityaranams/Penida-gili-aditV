{{-- Figma node 1:413 — catalogue card for one boat. --}}
@props([
    'name',
    'description',
    'rating',
    'image',
    'routes' => 0,
    'capacity' => null,
    'href' => '#',
    'delay' => 0,
])

<a href="{{ $href }}" data-reveal style="--reveal-delay: {{ $delay }}ms"
   class="group flex w-full max-w-[472px] flex-col lg:h-[569px] rounded-card border-2 border-line-card bg-surface p-[20px] shadow-card
          transition-[transform,box-shadow,border-color] duration-500 ease-smooth
          hover:-translate-y-2 hover:border-brand/20 hover:shadow-card-hover">
    <div class="h-[237px] w-full overflow-hidden rounded-field">
        <img src="{{ $image }}" alt="{{ $name }}"
             class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
    </div>

    @if ($rating)
        <div class="mt-[23px] flex items-center gap-[19px]">
            <x-rating-stars :rating="$rating" />
            <span class="text-[16px] leading-[30px] text-ink">{{ $rating }}</span>
        </div>
    @endif

    <h3 class="mt-[23px] text-[24px] leading-[30px] text-ink transition-colors duration-300 group-hover:text-brand">{{ $name }}</h3>

    <p class="mt-[12px] line-clamp-2 text-[16px] leading-[30px] text-ink-muted">{{ $description }}</p>

    <div class="mt-auto flex items-center justify-between">
        <div class="flex items-center gap-[54px]">
            <span class="flex items-center gap-[12px] text-[16px] leading-[30px] text-ink-muted">
                <img src="{{ asset('images/icons/icon-routes.svg') }}" alt="" class="size-[18px]">
                {{ $routes }} {{ \Illuminate\Support\Str::plural('Route', $routes) }}
            </span>

            <span class="flex items-center gap-[12px] text-[16px] leading-[30px] text-ink-muted">
                <img src="{{ asset('images/icons/icon-boat.svg') }}" alt="" class="h-[18px] w-[16.6px]">
                {{ $capacity }} Pax
            </span>
        </div>

        <span class="block size-[44.25px] -rotate-90 transition-transform duration-500 ease-smooth group-hover:translate-y-[-6px]">
            <img src="{{ asset('images/icons/arrow.svg') }}" alt="" class="size-full">
        </span>
    </div>
</a>
