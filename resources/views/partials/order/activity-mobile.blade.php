{{-- Figma node 1:4274 — "activity order full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['order'])

<form action="{{ $order['action'] }}" method="post" data-quote="{{ json_encode($order['quote']) }}" data-confirm="booking" data-email="{{ config('penida.booking.email') }}" data-confirm-product="{{ $order['rows'][0]['value'] ?? '' }}" class="bg-[#f7fafc] lg:hidden">
    @csrf
        @include('partials.order.hidden-fields', ['order' => $order])

    {{-- Hero (1:4318) --}}
    <header class="relative flex h-[250px] items-center justify-center overflow-hidden">
        <img src="{{ $order['image'] }}" alt="{{ $order['title'] }}" class="absolute inset-0 size-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[rgba(0,0,0,0.75)] via-[rgba(0,0,0,0.3)] to-[rgba(0,0,0,0.15)]"></div>
        <div class="absolute inset-x-[20px] bottom-[18px]">
            <h1 data-reveal class="text-[24px] font-semibold leading-[32px] text-white">{{ $order['title'] }}</h1>
            <p data-reveal style="--reveal-delay: 90ms" class="text-[16px] leading-[24px] text-white/90">Order Summary</p>
        </div>
    </header>

    <div class="flex flex-col gap-[32px] px-[20px] pb-[160px] pt-[32px]">
        <div data-reveal>
            <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Complete Your Booking</h2>
            <p class="mt-[8px] text-[16px] leading-[24px] text-[#414751]">Almost there! Please review your details and confirm.</p>
        </div>

        <div class="flex flex-col gap-[24px]">
            @include('partials.order.booking-form-mobile', ['order' => $order])

            {{-- Summary (1:4414) --}}
            <aside data-reveal style="--reveal-delay: 180ms" class="flex flex-col gap-[16px] pt-[16px]">
                <div class="rounded-[16px] bg-white px-[24px] pb-[48px] pt-[24px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
                    <h2 class="border-b border-[rgba(192,199,211,0.3)] pb-[17px] text-[22px] font-semibold leading-[30px] text-[#181c1e]">{{ $order['summaryTitle'] }}</h2>

                    <dl class="flex flex-col gap-[16px] py-[24px]">
                        @foreach ($order['rows'] as $row)
                            <div class="flex items-start justify-between gap-[24px]">
                                <dt class="shrink-0 text-[16px] leading-[24px] text-[#414751]">{{ $row['label'] }}</dt>
                                <dd class="text-right text-[16px] font-medium leading-[24px] text-[#181c1e]">{{ $row['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="flex items-end justify-between border-t border-[rgba(192,199,211,0.3)] pt-[17px]">
                        <span class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Total</span>
                        <span data-quote-total class="text-[28px] font-bold leading-[36px] text-brand">{{ $order['total'] }}</span>
                    </div>
                </div>

                {{-- Terms notice (1:4443) --}}
                <p class="flex items-start gap-[12px] rounded-[12px] bg-[#ebeef0] p-[16px] text-[14px] leading-[20px] text-[#414751]">
                    <img src="{{ asset('images/icons/mobile/order/info.svg') }}" alt="" class="size-[20px] shrink-0">
                    By completing this booking, you agree to our terms of service and cancellation policy.
                </p>
            </aside>
        </div>
    </div>

    {{-- Bottom action bar (1:4447) --}}
    <div class="fixed inset-x-0 bottom-[var(--tabbar-h,64px)] z-30 flex items-center justify-between gap-[16px] border-t border-[rgba(192,199,211,0.2)] bg-white/95 px-[20px] pb-[16px] pt-[14px] backdrop-blur-[8px] shadow-[0px_-4px_16px_rgba(0,0,0,0.08)] transition-[bottom] duration-300 ease-smooth lg:hidden">
        <div>
            <p class="text-[12px] leading-[16px] text-[#414751]">Total</p>
            <p data-quote-total class="text-[20px] font-bold leading-[28px] text-brand">{{ $order['total'] }}</p>
        </div>

        <button type="submit"
                class="flex min-h-[52px] max-w-[200px] flex-1 items-center justify-center gap-[8px] rounded-[12px] bg-brand px-[24px] text-[15px] font-bold leading-[20px] tracking-[0.5px] text-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]
                       transition-transform duration-300 ease-smooth active:scale-[0.98]">
            Book Now
            <img src="{{ asset('images/icons/mobile/order/check-circle.svg') }}" alt="" class="size-[15px]">
        </button>
    </div>
</form>
