{{-- Swipeable hero gallery for the phone layouts.

     Scroll snapping does the swiping, so it works with no JavaScript at all —
     the photos simply become a horizontal strip the finger can drag. The
     counter and the dots are the only parts that need a script
     (resources/js/photo-swiper.js); without it they stay on the first photo
     while the swiping itself still works. --}}
@props(['photos', 'height' => 400, 'counter' => true])

<div data-photo-swiper class="absolute inset-0">
    <div data-photo-swiper-track
         class="flex h-full w-full snap-x snap-mandatory overflow-x-auto overscroll-x-contain scroll-smooth
                [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
        @foreach ($photos as $photo)
            <figure class="h-full w-full shrink-0 snap-center snap-always">
                <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] ?? '' }}"
                     class="size-full object-cover"
                     loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                     draggable="false">
            </figure>
        @endforeach
    </div>

    @if (count($photos) > 1)
        {{-- Dots sit above whatever the page overlays on the hero. --}}
        <div class="pointer-events-none absolute inset-x-0 bottom-[14px] z-10 flex items-center justify-center gap-[6px]">
            @foreach ($photos as $index => $photo)
                {{-- The two states must not both be present, or which one wins
                     would depend on stylesheet order rather than the index. --}}
                <span data-photo-swiper-dot="{{ $index }}"
                      @class([
                          'h-[6px] rounded-full shadow-[0_1px_2px_rgba(0,0,0,0.35)] transition-[width,background-color] duration-300 ease-smooth',
                          'w-[18px] bg-white' => $index === 0,
                          'w-[6px] bg-white/60' => $index !== 0,
                      ])></span>
            @endforeach
        </div>
    @endif

    @if ($counter)
        <span class="pointer-events-none absolute bottom-[58px] right-[24px] z-10 flex items-center gap-[4px] rounded-full bg-[rgba(24,28,30,0.6)] px-[12px] py-[4px] backdrop-blur-[2px]">
            <img src="{{ asset('images/icons/mobile/detail/camera.svg') }}" alt="" class="size-[13.3px]">
            <span class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-white">
                <span data-photo-swiper-current>1</span>/{{ count($photos) }}
            </span>
        </span>
    @endif
</div>
