{{-- Figma node 1:2027 — right column: booking summary --}}
@props(['order'])

<aside data-reveal style="--reveal-delay: 120ms" class="flex flex-col gap-[21.6px] lg:sticky lg:top-8">
    <div class="overflow-hidden rounded-detail border border-brand/20 bg-surface shadow-editorial">
        {{-- Header --}}
        <div class="bg-brand p-[32px]">
            <div class="flex items-start justify-between gap-4">
                <h2 class="max-w-[262px] text-[28.3px] font-bold leading-[37.8px] text-white">Single Trip Booking Summary</h2>

                <span class="flex shrink-0 items-center gap-[5.4px] rounded-full border border-white/10 bg-[#f7fafc]/20 py-[7px] pl-[17.6px] pr-[27.7px] backdrop-blur-[3px]">
                    <img src="{{ asset('images/icons/order/one-way.svg') }}" alt="" class="h-[18px] w-[16px]">
                    <span class="text-[18.9px] font-semibold leading-[27px] tracking-[0.95px] text-white">{{ $order['tripType'] }}</span>
                </span>
            </div>

            <p class="mt-[11px] text-[18.9px] leading-[27px] text-[#9fcaff]">Your journey details and information</p>
        </div>

        {{-- Journey --}}
        <div class="flex flex-col gap-[21.6px] bg-[#f7fafc] p-[32px]">
            <div class="flex items-center gap-[16px] border-b border-[#e0e3e5] pb-[23px]">
                <span class="flex size-[64.9px] shrink-0 items-center justify-center rounded-[11px] bg-[#2178c3]">
                    <img src="{{ asset('images/icons/order/boat.svg') }}" alt="" class="h-[19.7px] w-[18px]">
                </span>
                <span>
                    <span class="block text-[21.6px] font-bold leading-[32px] text-editorial-ink">{{ $order['operator'] }}</span>
                    <span class="block text-[18.9px] leading-[27px] text-editorial-body">{{ $order['service'] }}</span>
                </span>
            </div>

            <div class="flex items-center gap-[16px] py-[11px]">
                <img src="{{ asset('images/icons/order/pin.svg') }}" alt="" class="h-[27px] w-[21.6px] shrink-0">
                <p class="flex flex-wrap items-center gap-x-[10px] text-[21.6px] font-semibold leading-[32px] text-editorial-ink">
                    <span>{{ $order['from'] }}</span>
                    <img src="{{ asset('images/icons/order/arrow-right.svg') }}" alt="to" class="h-[5px] w-[13px]">
                    <span>{{ $order['to'] }}</span>
                </p>
            </div>

            {{-- Pricing breakdown --}}
            <div class="mt-[11px] rounded-[11px] bg-[#f1f4f6] p-[21.6px]">
                {{-- Two-column grid: labels may wrap, amounts sit in one left-aligned column sized to the widest figure. --}}
                <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-[24px] gap-y-[4px]">
                    {{-- Rewritten by resources/js/order-quote.js as the party steppers change. --}}
                    @foreach ($order['lines'] as $line)
                        <span data-quote-row="{{ $line['kind'] }}-label" class="text-[18.9px] leading-[27px] text-editorial-body"
                              @if ($line['hidden'] ?? false) hidden @endif>{{ $line['label'] }}</span>
                        <span data-quote-row="{{ $line['kind'] }}-amount" class="whitespace-nowrap text-[18.9px] leading-[27px] text-editorial-ink"
                              @if ($line['hidden'] ?? false) hidden @endif>{{ $line['amount'] }}</span>
                    @endforeach

                    <span class="col-span-2 mt-[7px] border-t border-editorial-line" aria-hidden="true"></span>
                    <span class="self-center text-[21.6px] leading-[32px] text-brand">Total</span>
                    <span data-quote-total class="whitespace-nowrap text-[24.3px] leading-[38px] text-brand">{{ $order['total'] }}</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-[11px] bg-[#f7fafc] px-[32px] pb-[32px]">
            <button type="submit"
                    class="flex w-full items-center justify-center gap-[11px] rounded-[11px] bg-brand py-[16px] text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-white shadow-lg
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                <img src="{{ asset('images/icons/order/cart.svg') }}" alt="" class="size-[20px]">
                <span data-quote-total="button">Book Now &ndash; {{ $order['total'] }}</span>
            </button>

            <a href="{{ $order['emailHref'] }}" target="_blank" rel="noopener" data-email-book
               class="mt-[11px] flex w-full items-center justify-center gap-[11px] rounded-[11px] border border-[#c0c7d3] bg-[#f7fafc] py-[17.6px] font-jakarta text-[19.3px] font-semibold leading-[27.6px] tracking-[0.97px] text-editorial-ink
                      transition-colors duration-300 hover:bg-[#ebeef0]">
                <img src="{{ asset('images/icons/order/cart-outline.svg') }}" alt="" class="h-[16px] w-[18px]">
                Book With Email
            </a>
        </div>

        {{-- Trust badge --}}
        <div class="px-[32px] pb-[32px]">
            <p class="flex items-start gap-[16px] rounded-[11px] border border-[rgba(192,199,211,0.5)] bg-editorial-rule p-[23px] text-[17.6px] leading-[22px]">
                <img src="{{ asset('images/icons/order/thumbs-up.svg') }}" alt="" class="h-[27px] w-[28.4px] shrink-0">
                <span class="text-editorial-body">
                    <strong class="font-bold text-editorial-ink">94% of travelers</strong> recommend this boat service
                </span>
            </p>
        </div>
    </div>

    <a href="{{ route('boats.index') }}"
       class="flex items-center justify-center gap-[11px] rounded-detail border border-[#c0c7d3] bg-surface py-[23px] text-[18.9px] font-semibold leading-[27px] tracking-[0.95px] text-brand shadow-detail
              transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
        <img src="{{ asset('images/icons/order/search.svg') }}" alt="" class="size-[20px]">
        Choose Another Product
    </a>
</aside>
