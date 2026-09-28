{{-- Figma node 1:1673 — right column booking card --}}
@props(['activity'])

<aside data-reveal style="--reveal-delay: 120ms" class="lg:sticky lg:top-8">
    <div class="rounded-detail border border-editorial-line bg-surface p-[32px] shadow-detail">
        <h2 class="text-[32px] font-semibold leading-[42.7px] text-editorial-ink">{{ $activity['name'] }}</h2>

        {{-- Price block: the figure carries the card, with the old price and the saving beside it. --}}
        <div class="mt-[20px] rounded-detail bg-[rgba(217,227,249,0.28)] px-[24px] py-[20px]">
            <p class="text-[15px] font-semibold uppercase leading-[20px] tracking-[1px] text-editorial-body">Starting from</p>

            <p class="mt-[4px] flex flex-wrap items-baseline gap-x-[10px] gap-y-[2px]">
                <span class="text-[40px] font-bold leading-[48px] tracking-[-0.5px] text-brand">{{ $activity['price_label'] }}</span>
                <span class="text-[17px] leading-[24px] text-editorial-body">/ person</span>
            </p>

            @if ($activity['discount_percent'] > 0)
                <p class="mt-[8px] flex flex-wrap items-center gap-[10px]">
                    <span class="text-[17px] leading-[24px] text-editorial-meta line-through">{{ $activity['price_was_label'] }}</span>
                    <span class="rounded-full bg-[#dcfce7] px-[10px] py-[3px] text-[13px] font-semibold leading-[18px] text-[#15803d]">
                        Save {{ $activity['discount_percent'] }}%
                    </span>
                </p>
            @endif
        </div>

        {{-- Only worth a box when there is actually a note to show. --}}
        @if (filled($activity['price_note']))
            <p class="mt-[16px] flex items-start gap-[10.7px] rounded-[10.7px] border border-editorial-line p-[16px] text-[16px] leading-[21.4px] text-editorial-body">
                <img src="{{ asset('images/icons/detail/info.svg') }}" alt="" class="size-[20px] shrink-0">
                {{ $activity['price_note'] }}
            </p>
        @endif

        <a href="{{ route('activities.order', $activity['slug']) }}"
           class="mt-[24px] flex items-center justify-center rounded-[10.7px] bg-brand py-[16px] text-[21.4px] leading-[32px] text-white shadow-sm
                  transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
            Book Now
        </a>

        <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
           class="mt-[12px] flex items-center justify-center gap-[10.7px] rounded-[10.7px] border-2 border-brand bg-surface py-[16px] text-[21.4px] leading-[32px] text-brand
                  transition-colors duration-300 hover:bg-brand/5">
            <img src="{{ asset('images/icons/detail/whatsapp.svg') }}" alt="" class="size-[26.7px]">
            Ask via WhatsApp
        </a>
    </div>
</aside>
