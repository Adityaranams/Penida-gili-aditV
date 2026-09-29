{{-- Figma node 1:3606 — "hotel order full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['order'])

@php [$adults] = $order['party']; @endphp

<form action="{{ $order['action'] }}" method="post" data-quote="{{ json_encode($order['quote']) }}" data-confirm="booking" data-email="{{ config('penida.booking.email') }}" data-confirm-product="{{ $order['property'] }}" class="bg-[#f7fafc] lg:hidden">
    @csrf
        @include('partials.order.hidden-fields', ['order' => $order])

    <div class="flex flex-col gap-[32px] px-[20px] pb-[32px] pt-[16px]">
        {{-- Image (1:3650) --}}
        <figure data-reveal class="h-[192px] w-full overflow-hidden rounded-[12px] shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]">
            <img src="{{ asset('images/hotels/detail/'.$order['thumb']) }}" alt="{{ $order['property'] }}" class="size-full object-cover">
        </figure>

        {{-- Order summary card (1:3652) --}}
        <section data-reveal style="--reveal-delay: 90ms" class="flex flex-col gap-[8px] rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-white p-[17px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
            <h1 class="text-[20px] font-bold leading-[30px] text-brand">{{ $order['property'] }}</h1>
            <p class="flex items-center gap-[8px] text-[16px] leading-[24px] text-[#414751]">
                <img src="{{ asset('images/icons/mobile/order/pin.svg') }}" alt="" class="h-[15px] w-[12px]">
                {{ $order['location'] }}
            </p>

            <dl class="flex flex-col gap-[8px] rounded-[8px] bg-[#f1f4f6] px-[8px] pb-[8px] pt-[16px]">
                @foreach ($order['details'] as $detail)
                    <div @class(['flex items-center justify-between gap-[16px] text-[16px] leading-[24px]', 'border-b border-[rgba(192,199,211,0.5)] pb-[9px]' => ! $loop->last])>
                        <dt class="text-[#414751]">{{ $detail['label'] === 'Room Type' ? 'Room' : $detail['label'] }}</dt>
                        <dd class="text-right text-[#181c1e]">{{ $detail['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Complete Your Booking (1:3676) --}}
        <section class="flex flex-col gap-[16px]">
            <h2 data-reveal class="text-[20px] font-bold leading-[30px] text-brand">Complete Your Booking</h2>

            <fieldset data-reveal class="flex flex-col gap-[8px] rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-white p-[17px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
                <legend class="sr-only">Traveler details</legend>
                <h3 class="pb-[8px] text-[16px] leading-[24px] text-brand">Traveler Details</h3>

                @include('partials.order.errors')

                <div class="flex flex-col gap-[4px]">
                    <label for="m-full-name" class="text-[12px] leading-[18px] text-[#414751]">Full Name</label>
                    <input id="m-full-name" name="full_name" type="text" minlength="3" title="Letters only, at least 3 letters" placeholder="John Doe" value="{{ old('full_name') }}" required
                           class="rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] pb-[15px] pt-[14px] text-[16px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">
                </div>
                <div class="flex flex-col gap-[4px]">
                    <label for="m-nationality" class="text-[12px] leading-[18px] text-[#414751]">Nationality</label>
                    <div class="relative">
                        <select id="m-nationality" name="nationality" required
                                class="w-full appearance-none rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] py-[13px] pl-[17px] pr-[44px] text-[16px] leading-[24px] text-[#181c1e] focus:border-brand focus:outline-none">
                            <option value="">Select nationality</option>
                            @foreach ($order['nationalities'] as $nationality)
                                <option value="{{ $nationality }}" @selected(old('nationality') === $nationality)>{{ $nationality }}</option>
                            @endforeach
                        </select>
                        <img src="{{ asset('images/icons/mobile/order/chevron-down.svg') }}" alt="" class="pointer-events-none absolute right-[9px] top-1/2 size-[24px] -translate-y-1/2">
                    </div>
                </div>

                <div class="flex flex-col gap-[4px]">
                    <label for="m-phone" class="text-[12px] leading-[18px] text-[#414751]">Phone Number</label>
                    <div class="flex">
                        <label for="m-dial-code" class="sr-only">Country dialling code</label>
                        <span class="relative block">
                            <select id="m-dial-code" name="dial_code"
                                    class="h-full appearance-none rounded-l-[8px] border border-r-0 border-[#c0c7d3] bg-[#f1f4f6] py-[13px] pl-[14px] pr-[28px] text-[16px] leading-[24px] text-[#414751] focus:outline-none">
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
                    <label for="m-order-notes" class="text-[12px] leading-[18px] text-[#414751]">Special Notes (Optional)</label>
                    <textarea id="m-order-notes" name="notes" rows="2" placeholder="Any special requests?"
                              class="rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] py-[13px] text-[16px] leading-[24px] text-[#181c1e] placeholder:text-[#6b7280] focus:border-brand focus:outline-none">{{ old('notes') }}</textarea>
                </div>
            </fieldset>

            {{-- Number of People (1:3711) --}}
            <div data-reveal style="--reveal-delay: 90ms" class="flex items-center justify-between gap-[12px] rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-white p-[17px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
                <label for="m-{{ $adults['name'] }}">
                    <span class="block text-[16px] leading-[24px] text-brand">Number of People</span>
                    <span class="block text-[12px] leading-[18px] text-[#414751]" data-quote-adult-hint>{{ $adults['price'] }}</span>
                </label>

                <div class="flex items-center gap-[16px] rounded-full bg-[#ebeef0] px-[8px] py-[4px]" data-stepper>
                    <button type="button" data-step="-1" aria-label="Decrease adults"
                            class="flex size-[32px] items-center justify-center rounded-full bg-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                        <img src="{{ asset('images/icons/mobile/order/minus.svg') }}" alt="" class="h-[2px] w-[11.7px]">
                    </button>

                    <input id="m-{{ $adults['name'] }}" name="{{ $adults['name'] }}" type="number" inputmode="numeric"
                           value="{{ $adults['value'] }}" min="{{ $adults['min'] }}"
                           class="w-[24px] bg-transparent text-center text-[16px] leading-[24px] text-[#181c1e] focus:outline-none
                                  [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                    <button type="button" data-step="1" aria-label="Increase adults"
                            class="flex size-[32px] items-center justify-center rounded-full bg-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                        <img src="{{ asset('images/icons/mobile/order/plus.svg') }}" alt="" class="size-[11.7px]">
                    </button>
                </div>
            </div>

        </section>
    </div>

    {{-- Bottom action bar (1:3726) --}}
    <div class="flex flex-col gap-[8px] border-t border-[rgba(192,199,211,0.5)] bg-white px-[20px] pb-[20px] pt-[21px] drop-shadow-[0px_-4px_10px_rgba(0,0,0,0.05)]">
        <div class="flex items-center justify-between">
            <span class="text-[16px] leading-[24px] text-[#414751]">Total Price</span>
            <span data-quote-total class="text-[20px] font-bold leading-[30px] text-brand">{{ $order['total'] }}</span>
        </div>

        <button type="submit"
                class="w-full rounded-[12px] bg-brand py-[16px] text-center text-[16px] leading-[24px] text-white transition-transform duration-300 ease-smooth active:scale-[0.98]">
            Proceed to Payment
        </button>
    </div>
</form>
