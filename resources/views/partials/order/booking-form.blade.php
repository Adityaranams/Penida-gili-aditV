{{-- Figma node 1:1928 — left column: traveler details + party size --}}
@props(['order'])

<div class="flex flex-col gap-[43px]">
    @include('partials.order.errors')

    <div data-reveal class="font-jakarta">
        <h2 class="text-[24px] lg:text-[48px] font-bold leading-[30px] lg:leading-[60px] tracking-[-0.96px] text-editorial-ink">Complete Your Booking</h2>
        <p class="mt-[8px] text-[18px] leading-[28px] text-editorial-body">Almost there! Please review your details and confirm.</p>
    </div>

    {{-- Figma node 1:1934 — traveler details --}}
    <fieldset data-reveal style="--reveal-delay: 90ms"
              class="rounded-detail border border-[#e0e3e5] bg-surface p-[34px] shadow-detail">
        <legend class="sr-only">Traveler details</legend>

        <h3 class="flex items-center gap-[11px] text-[32px] font-semibold leading-[43px] text-editorial-ink">
            <img src="{{ asset('images/icons/order/traveler.svg') }}" alt="" class="size-[21.6px]">
            Traveler Details
        </h3>

        <div class="mt-[22px] grid [&>*]:min-w-0 gap-[21.6px] sm:grid-cols-2">
            <div class="flex flex-col gap-[5.4px]">
                <label for="full-name" class="text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-editorial-body">Full Name</label>
                <input id="full-name" name="full_name" type="text" minlength="3" title="Letters only, at least 3 letters" placeholder="Enter your full name" value="{{ old('full_name') }}" required
                       class="rounded-[11px] border border-[#c0c7d3] bg-[#f7fafc] px-[17.6px] pb-[15px] pt-[13.5px] text-[21.6px] text-editorial-ink placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
            </div>
            <div class="flex flex-col gap-[5.4px]">
                <label for="nationality" class="text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-editorial-body">Nationality</label>
                <div class="relative">
                    <select id="nationality" name="nationality" required
                            class="w-full appearance-none rounded-[11px] border border-[#c0c7d3] bg-[#f7fafc] px-[17.6px] py-[12px] text-[21.6px] leading-[32px] text-editorial-ink focus:border-brand focus:outline-none">
                        <option value="">Select nationality</option>
                        @foreach ($order['nationalities'] as $nationality)
                            <option value="{{ $nationality }}" @selected(old('nationality') === $nationality)>{{ $nationality }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/order/chevron-down.svg') }}" alt=""
                         class="pointer-events-none absolute right-[16px] top-1/2 h-[10px] w-[16px] -translate-y-1/2">
                </div>
            </div>

            <div class="flex flex-col gap-[5.4px] sm:col-span-2">
                <label for="phone" class="text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-editorial-body">Phone Number</label>
                <div class="flex">
                    <div class="relative">
                        <label for="dial-code" class="sr-only">Country dialling code</label>
                        <select id="dial-code" name="dial_code"
                                class="h-full appearance-none rounded-l-[11px] border border-[#c0c7d3] bg-[#ebeef0] py-[12px] pl-[17.6px] pr-[43px] text-[21.6px] leading-[32px] text-editorial-ink focus:outline-none">
                            @foreach ($order['countries'] as $country)
                                <option value="{{ $country['dial'] }}" @selected(old('dial_code', '+62') === $country['dial'])>{{ $country['flag'] }} {{ $country['dial'] }}</option>
                            @endforeach
                        </select>
                        <img src="{{ asset('images/icons/order/chevron-down.svg') }}" alt=""
                             class="pointer-events-none absolute right-[14px] top-1/2 h-[10px] w-[16px] -translate-y-1/2">
                    </div>
                    <input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9 \(\)\-]{6,20}" title="6-15 digits, without the country code" placeholder="812 3456 7890" value="{{ old('phone') }}" required
                           class="min-w-0 flex-1 rounded-r-[11px] border border-l-0 border-[#c0c7d3] bg-[#f7fafc] px-[17.6px] pb-[15px] pt-[13.5px] text-[21.6px] text-editorial-ink placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
                </div>
            </div>

            <div class="flex flex-col gap-[5.4px] sm:col-span-2">
                <label for="order-notes" class="text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-editorial-body">Order Notes (Optional)</label>
                <textarea id="order-notes" name="notes" rows="3" placeholder="Special requests, large luggage, etc."
                          class="rounded-[11px] border border-[#c0c7d3] bg-[#f7fafc] px-[17.6px] py-[12px] text-[21.6px] leading-[32px] text-editorial-ink placeholder:text-[#6b7280] focus:border-brand focus:outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>
    </fieldset>

    {{-- Figma node 1:1977 — number of people --}}
    <fieldset data-reveal style="--reveal-delay: 180ms"
              class="rounded-detail border border-[#e0e3e5] bg-surface p-[34px] shadow-detail">
        <legend class="sr-only">Number of people</legend>

        <h3 class="flex items-center gap-[11px] text-[32px] font-semibold leading-[43px] text-editorial-ink">
            <img src="{{ asset('images/icons/order/people.svg') }}" alt="" class="h-[21.6px] w-[30px]">
            Number of People
        </h3>

        <div class="mt-[22px] grid [&>*]:min-w-0 gap-[21.6px] sm:grid-cols-2">
            @foreach ($order['party'] as $group)
                <div class="flex flex-col gap-[5.4px]">
                    <label for="{{ $group['name'] }}" class="text-[18.9px] font-semibold uppercase leading-[27px] tracking-[0.95px] text-editorial-body">
                        {{ $group['label'] }}
                    </label>

                    {{-- Stepper: the buttons sit inside the field, as drawn in Figma. --}}
                    <div class="relative" data-stepper>
                        <button type="button" data-step="-1" aria-label="Decrease {{ $group['label'] }}"
                                class="absolute left-[16px] top-1/2 -translate-y-1/2 text-[24px] leading-none text-editorial-body transition-colors hover:text-brand">&minus;</button>

                        <input id="{{ $group['name'] }}" name="{{ $group['name'] }}" type="number" inputmode="numeric"
                               value="{{ old($group['name'], $group['value']) }}" min="{{ $group['min'] }}"
                               class="w-full rounded-[11px] border border-[#c0c7d3] bg-[#f7fafc] px-[40px] py-[12px] text-center text-[21.6px] leading-[32px] text-editorial-ink focus:border-brand focus:outline-none
                                      [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                        <button type="button" data-step="1" aria-label="Increase {{ $group['label'] }}"
                                class="absolute right-[16px] top-1/2 -translate-y-1/2 text-[24px] leading-none text-editorial-body transition-colors hover:text-brand">+</button>
                    </div>

                    <p class="pt-[5.4px] text-[16.2px] leading-[24px] text-editorial-body" @if ($group['name'] === 'adults') data-quote-adult-hint @endif>{{ $group['price'] }}</p>
                </div>
            @endforeach
        </div>
    </fieldset>
</div>
