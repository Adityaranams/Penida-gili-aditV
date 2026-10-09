{{-- Figma node 1:4528 — "boat order full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['order'])

<form action="{{ $order['action'] }}" method="post" data-quote="{{ json_encode($order['quote']) }}" data-confirm="booking" data-email="{{ config('penida.booking.email') }}" data-confirm-product="{{ $order['operator'] }}" class="bg-[#f7fafc] lg:hidden">
    @csrf
        @include('partials.order.hidden-fields', ['order' => $order])

    {{-- Hero / summary image (1:4573) --}}
    <header class="relative h-[220px] w-full overflow-hidden bg-[#ebeef0]">
        <img src="{{ $order['image'] }}" alt="{{ $order['operator'] }}" class="absolute inset-0 size-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-[rgba(0,0,0,0.75)] via-[rgba(0,0,0,0.25)] to-transparent"></div>

        <div class="absolute inset-x-[20px] bottom-[18px]">
            <h1 data-reveal class="text-[24px] font-semibold leading-[32px] text-white">{{ $order['operator'] }}</h1>
            <p data-reveal style="--reveal-delay: 90ms" class="text-[16px] leading-[24px] text-white/90">{{ $order['service'] }}</p>
        </div>
    </header>

    <div class="flex flex-col gap-[32px] px-[20px] pb-[188px] pt-[32px]">
        {{-- Route Details (1:4582) --}}
        <section data-reveal class="flex flex-col gap-[16px] rounded-[12px] bg-white/95 p-[24px] shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)] backdrop-blur-[5px]">
            <div class="flex items-start justify-between border-b border-[#c0c7d3] pb-[17px]">
                <div>
                    <h2 class="text-[18px] font-bold leading-[28px] text-[#181c1e]">Route Details</h2>
                    <p class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">{{ $order['date'] }}</p>
                </div>
                <span class="rounded-full bg-[#d2e4ff] px-[12px] py-[4px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#00497e]">{{ $order['tripType'] }}</span>
            </div>

            <ol class="relative flex flex-col gap-[16px] pl-[32px]">
                <span class="absolute bottom-[8px] left-[11px] top-[17px] w-[2px] bg-[#c0c7d3]" aria-hidden="true"></span>

                <li class="relative">
                    <img src="{{ asset('images/icons/mobile/order/origin.svg') }}" alt="" class="absolute -left-[28px] top-[3px] size-[16.7px]">
                    <p class="text-[14px] font-bold leading-[20px] tracking-[0.7px] text-[#181c1e]">{{ $order['departure'] }}</p>
                    <p class="text-[16px] leading-[24px] text-[#414751]">{{ $order['from'] }}</p>
                </li>
                <li class="relative">
                    <img src="{{ asset('images/icons/mobile/order/destination.svg') }}" alt="" class="absolute -left-[26px] top-[10px] h-[16.7px] w-[13.3px]">
                    <p class="text-[14px] font-bold leading-[20px] tracking-[0.7px] text-[#181c1e]">{{ $order['arrival'] }}</p>
                    <p class="text-[16px] leading-[24px] text-[#414751]">{{ $order['to'] }}</p>
                </li>
            </ol>
        </section>

        {{-- Passengers (1:4605) --}}
        <fieldset data-reveal style="--reveal-delay: 90ms" class="flex flex-col gap-[16px] rounded-[12px] bg-white/95 p-[24px] shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)] backdrop-blur-[5px]">
            <legend class="sr-only">Passengers</legend>
            <h2 class="text-[18px] font-bold leading-[28px] text-[#181c1e]">Passengers</h2>

            <div class="flex flex-col gap-[16px]">
                @foreach ($order['party'] as $group)
                    <div class="flex items-center justify-between">
                        <label for="m-{{ $group['name'] }}" class="min-w-0 pr-[8px]">
                            <span class="block text-[14px] font-bold leading-[20px] tracking-[0.7px] text-[#181c1e]">{{ $group['label'] }}</span>
                            @isset($group['hint'])
                                <span class="block text-[12px] leading-[18px] text-[#414751]">{{ $group['hint'] }}</span>
                            @endisset
                            <span class="block text-[12px] font-semibold leading-[18px] text-brand"
                                  @if ($group['name'] === 'adults') data-quote-adult-hint @endif>{{ $group['price'] }}</span>
                        </label>

                        <div class="flex items-center rounded-[8px] bg-[#f1f4f6] p-[4px]" data-stepper>
                            <button type="button" data-step="-1" aria-label="Decrease {{ $group['label'] }}"
                                    class="flex size-[44px] items-center justify-center rounded-[8px] bg-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)] transition-transform active:scale-95">
                                <img src="{{ asset('images/icons/mobile/order/minus.svg') }}" alt="" class="h-[2px] w-[14px]">
                            </button>

                            <input id="m-{{ $group['name'] }}" name="{{ $group['name'] }}" type="number" inputmode="numeric"
                                   value="{{ old($group['name'], $group['value']) }}" min="{{ $group['min'] }}"
                                   class="w-[44px] bg-transparent text-center text-[16px] font-bold leading-[20px] tracking-[0.7px] text-[#181c1e] focus:outline-none
                                          [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                            <button type="button" data-step="1" aria-label="Increase {{ $group['label'] }}"
                                    class="flex size-[44px] items-center justify-center rounded-[8px] bg-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)] transition-transform active:scale-95">
                                <img src="{{ asset('images/icons/mobile/order/plus.svg') }}" alt="" class="size-[14px]">
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </fieldset>

        {{-- Traveler Details (1:4643) --}}
        <fieldset data-reveal style="--reveal-delay: 180ms" class="flex flex-col gap-[16px] rounded-[12px] bg-white/95 p-[24px] shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)] backdrop-blur-[5px]">
            <legend class="sr-only">Traveler details</legend>
            <h2 class="text-[18px] font-bold leading-[28px] text-[#181c1e]">Traveler Details</h2>

            <div class="flex flex-col gap-[16px]">
                @include('partials.order.errors')

                <div class="flex flex-col gap-[4px]">
                    <label for="m-full-name" class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">Full Name</label>
                    <input id="m-full-name" name="full_name" type="text" minlength="3" title="Letters only, at least 3 letters" placeholder="e.g. John Doe" value="{{ old('full_name') }}" required
                           class="rounded-[8px] border border-[#c0c7d3] bg-white px-[17px] pb-[15px] pt-[14px] text-[16px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
                </div>
                <div class="flex flex-col gap-[4px]">
                    <label for="m-nationality" class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">Nationality</label>
                    <div class="relative">
                        <select id="m-nationality" name="nationality" required
                                class="w-full appearance-none rounded-[8px] border border-[#c0c7d3] bg-white py-[13px] pl-[17px] pr-[44px] text-[16px] leading-[24px] text-[#181c1e] focus:border-brand focus:outline-none">
                            <option value="">Select Nationality</option>
                            @foreach ($order['nationalities'] as $nationality)
                                <option value="{{ $nationality }}" @selected(old('nationality') === $nationality)>{{ $nationality }}</option>
                            @endforeach
                        </select>
                        <img src="{{ asset('images/icons/mobile/order/chevron-down.svg') }}" alt="" class="pointer-events-none absolute right-[9px] top-1/2 size-[24px] -translate-y-1/2">
                    </div>
                </div>

                <div class="flex flex-col gap-[4px]">
                    <label for="m-phone" class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">Phone Number</label>
                    <div class="flex">
                        <label for="m-dial-code" class="sr-only">Country dialling code</label>
                        <span class="relative block">
                                <select id="m-dial-code" name="dial_code"
                                class="h-full appearance-none rounded-l-[8px] border border-r-0 border-[#c0c7d3] bg-[#f1f4f6] py-[13px] pl-[17px] pr-[16px] text-[16px] leading-[24px] text-[#414751] focus:outline-none pr-[28px]">
                            @foreach ($order['countries'] as $country)
                                <option value="{{ $country['dial'] }}" @selected(old('dial_code', '+62') === $country['dial'])>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                            @endforeach
                        </select>
                        <img src="{{ asset('images/icons/mobile/order/chevron-down.svg') }}" alt="" class="pointer-events-none absolute right-[6px] top-1/2 size-[18px] -translate-y-1/2">
                        </span>
                        <input id="m-phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9 \(\)\-]{6,20}" title="6-15 digits, without the country code" placeholder="812 3456 7890" value="{{ old('phone') }}" required
                               class="min-w-0 flex-1 rounded-r-[8px] border border-[#c0c7d3] bg-white px-[17px] pb-[15px] pt-[14px] text-[16px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
                    </div>
                </div>

                <div class="flex flex-col gap-[4px] pb-[6px]">
                    <label for="m-order-notes" class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#414751]">
                        Order Notes <span class="font-normal text-[#525c6f]">(Optional)</span>
                    </label>
                    <textarea id="m-order-notes" name="notes" rows="2" placeholder="Any special requests?"
                              class="rounded-[8px] border border-[#c0c7d3] bg-white px-[17px] py-[13px] text-[16px] leading-[24px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">{{ old('notes') }}</textarea>
                </div>
            </div>
        </fieldset>
    </div>

        {{-- Price breakdown, as on the desktop summary card --}}
        <section data-reveal style="--reveal-delay: 240ms" class="flex flex-col gap-[12px] rounded-[12px] bg-white/95 p-[24px] shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)] backdrop-blur-[5px]">
            <h2 class="text-[18px] font-bold leading-[28px] text-[#181c1e]">Price Details</h2>

            <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-[16px] gap-y-[6px]">
                @foreach ($order['lines'] as $line)
                    <span data-quote-row="{{ $line['kind'] }}-label" class="text-[15px] leading-[24px] text-[#414751]"
                          @if ($line['hidden'] ?? false) hidden @endif>{{ $line['label'] }}</span>
                    <span data-quote-row="{{ $line['kind'] }}-amount" class="whitespace-nowrap text-[15px] leading-[24px] text-[#181c1e]"
                          @if ($line['hidden'] ?? false) hidden @endif>{{ $line['amount'] }}</span>
                @endforeach

                <span class="col-span-2 mt-[6px] border-t border-[#e0e3e5]" aria-hidden="true"></span>
                <span class="self-center text-[16px] font-semibold leading-[24px] text-brand">Total</span>
                <span data-quote-total class="whitespace-nowrap text-[20px] font-bold leading-[28px] text-brand">{{ $order['total'] }}</span>
            </div>

            <a href="{{ $order['emailHref'] }}" target="_blank" rel="noopener" data-email-book
               class="mt-[4px] flex min-h-[48px] w-full items-center justify-center gap-[10px] rounded-[12px] border border-[#c0c7d3] bg-[#f7fafc] text-[15px] font-semibold text-[#181c1e] transition-colors active:bg-[#ebeef0]">
                <img src="{{ asset('images/icons/order/cart-outline.svg') }}" alt="" class="h-[16px] w-[18px]">
                Book With Email
            </a>
        </section>
    </div>

    {{-- Bottom action bar (1:4677). Pinned to the bottom of the screen so the
         total and the booking button stay reachable down the whole form. --}}
    <div class="fixed inset-x-0 bottom-[var(--tabbar-h,64px)] z-30 flex flex-col gap-[12px] border-t border-[#e5e7eb] bg-white/95 px-[20px] pb-[16px] pt-[14px] backdrop-blur-[8px] shadow-[0px_-4px_16px_rgba(0,0,0,0.08)] transition-[bottom] duration-300 ease-smooth lg:hidden">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[13px] font-semibold leading-[18px] tracking-[0.7px] text-[#414751]">Total Price</p>
                <p data-quote-total class="text-[22px] font-bold leading-[30px] text-brand">{{ $order['total'] }}</p>
            </div>
            <a href="{{ route('boats.index') }}" class="min-h-[44px] content-center text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#6b7588] underline">Change Selection</a>
        </div>

        <button type="submit"
                class="min-h-[52px] w-full rounded-[12px] bg-brand text-center text-[16px] font-bold leading-[24px] tracking-[0.5px] text-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]
                       transition-transform duration-300 ease-smooth active:scale-[0.98]">
            <span data-quote-total="button">Book Now &ndash; {{ $order['total'] }}</span>
        </button>
    </div>
</form>
