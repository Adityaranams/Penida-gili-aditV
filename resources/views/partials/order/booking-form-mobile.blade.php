{{-- Figma nodes 1:4332 + 1:4380 — mobile "Traveler Details" and "Number of People" cards (activity / hotel order).
     Field names match the desktop form so either layout submits the same payload; ids are prefixed to stay unique. --}}
@props(['order'])

<div class="flex flex-col gap-[16px]">
    {{-- Traveler Details (1:4332) --}}
    <fieldset data-reveal class="flex flex-col gap-[24px] rounded-[16px] bg-white p-[24px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
        <legend class="sr-only">Traveler details</legend>

        <h3 class="flex items-center gap-[12px] text-[20px] font-semibold leading-[28px] text-[#181c1e]">
            <span class="flex size-[40px] items-center justify-center rounded-full bg-[#d5e2e9]">
                <img src="{{ asset('images/icons/mobile/order/traveler.svg') }}" alt="" class="size-[16px]">
            </span>
            Traveler Details
        </h3>

        <div class="flex flex-col gap-[16px]">
            @include('partials.order.errors')

                <div class="flex flex-col gap-[4px]">
                <label for="m-full-name" class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#414751]">Full Name</label>
                <input id="m-full-name" name="full_name" type="text" minlength="3" title="Letters only, at least 3 letters" placeholder="Enter your full name" value="{{ old('full_name') }}" required
                       class="rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] pb-[15px] pt-[14px] text-[16px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
            </div>
            <div class="flex flex-col gap-[4px]">
                <label for="m-nationality" class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#414751]">Nationality</label>
                <div class="relative">
                    <select id="m-nationality" name="nationality" required
                            class="w-full appearance-none rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] py-[13px] pl-[17px] pr-[36px] text-[16px] leading-[24px] text-[#181c1e] focus:border-brand focus:outline-none">
                        <option value="">Select nationality</option>
                        @foreach ($order['nationalities'] as $nationality)
                            <option value="{{ $nationality }}" @selected(old('nationality') === $nationality)>{{ $nationality }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/mobile/order/chevron-sm.svg') }}" alt="" class="pointer-events-none absolute right-[12px] top-1/2 h-[7.4px] w-[12px] -translate-y-1/2">
                </div>
            </div>

            <div class="flex flex-col gap-[4px]">
                <label for="m-phone" class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#414751]">Phone Number</label>
                <div class="flex">
                    <label for="m-dial-code" class="sr-only">Country dialling code</label>
                    <span class="relative block">
                        <select id="m-dial-code" name="dial_code"
                                class="h-full appearance-none rounded-l-[8px] border border-r-0 border-[#c0c7d3] bg-[#ebeef0] py-[13px] pl-[13px] pr-[28px] text-[16px] leading-[24px] text-[#181c1e] focus:outline-none">
                            @foreach ($order['countries'] as $country)
                                <option value="{{ $country['dial'] }}" @selected(old('dial_code', '+62') === $country['dial'])>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                            @endforeach
                        </select>
                        <img src="{{ asset('images/icons/mobile/order/chevron-down.svg') }}" alt="" class="pointer-events-none absolute right-[6px] top-1/2 size-[18px] -translate-y-1/2">
                    </span>
                    <input id="m-phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9 \(\)\-]{6,20}" title="6-15 digits, without the country code" placeholder="812 3456 7890" value="{{ old('phone') }}" required
                           class="min-w-0 flex-1 rounded-r-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] pb-[15px] pt-[14px] text-[16px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
                </div>
            </div>

            <div class="flex flex-col gap-[4px] pb-[6px]">
                <label for="m-order-notes" class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#414751]">Order Notes (Optional)</label>
                <textarea id="m-order-notes" name="notes" rows="2" placeholder="Special requests, large luggage, etc."
                          class="rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] py-[13px] text-[16px] leading-[24px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>
    </fieldset>

    {{-- Number of People (1:4380) --}}
    <fieldset data-reveal style="--reveal-delay: 90ms" class="flex flex-col gap-[24px] rounded-[16px] bg-white p-[24px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
        <legend class="sr-only">Number of people</legend>

        <h3 class="flex items-center gap-[12px] text-[20px] font-semibold leading-[28px] text-[#181c1e]">
            <span class="flex size-[40px] items-center justify-center rounded-full bg-[#d5e2e9]">
                <img src="{{ asset('images/icons/mobile/order/people.svg') }}" alt="" class="h-[16px] w-[22px]">
            </span>
            Number of People
        </h3>

        <div class="flex flex-col gap-[24px]">
            @foreach ($order['party'] as $group)
                <div class="flex flex-col gap-[8px]">
                    <label for="m-{{ $group['name'] }}" class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#414751]">{{ $group['label'] }}</label>

                    <div class="flex items-center justify-between overflow-hidden rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] p-px" data-stepper>
                        <button type="button" data-step="-1" aria-label="Decrease {{ $group['label'] }}"
                                class="px-[16px] py-[12px] text-[16px] leading-[24px] text-[#414751] transition-colors hover:text-brand">-</button>

                        <input id="m-{{ $group['name'] }}" name="{{ $group['name'] }}" type="number" inputmode="numeric"
                               value="{{ old($group['name'], $group['value']) }}" min="{{ $group['min'] }}"
                               class="w-[48px] bg-transparent text-center text-[16px] leading-[24px] text-[#181c1e] focus:outline-none
                                      [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                        <button type="button" data-step="1" aria-label="Increase {{ $group['label'] }}"
                                class="px-[16px] py-[12px] text-[16px] leading-[24px] text-[#414751] transition-colors hover:text-brand">+</button>
                    </div>

                    <p class="text-[12px] leading-[16px] text-[#414751]" @if ($group['name'] === 'adults') data-quote-adult-hint @endif>{{ $group['price'] }}</p>
                </div>
            @endforeach
        </div>
    </fieldset>
</div>
