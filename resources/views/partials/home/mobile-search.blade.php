{{-- Mobile hero search: Boat / Where To? tabs with the same behaviour as the desktop hero
     (harbor picker, calendar popover, guest stepper — see search-tabs.js, port-picker.js, calendar.js). --}}
@php
    $mFields = [
        ['label' => 'From',     'placeholder' => 'Origin Place',      'name' => 'from', 'icon' => 'pin'],
        ['label' => 'To',       'placeholder' => 'Destination Place', 'name' => 'to',   'icon' => 'pin'],
        ['label' => 'Check In', 'placeholder' => 'Add Your Date',     'name' => 'date', 'icon' => 'calendar'],
    ];
    $panel = 'mt-[12px] w-full flex-col gap-[16px] rounded-[16px] border border-white/20 bg-white/10 p-[17px] shadow-[0px_25px_50px_-12px_rgba(0,0,0,0.25)] backdrop-blur-[6px]';
    $compass = 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm3.5-12.5-2 5-5 2 2-5 5-2Z';
@endphp

<div data-search-tabs data-reveal style="--reveal-delay: 320ms" class="mt-[40px] w-full">
    <div role="tablist" class="mx-auto flex w-fit gap-[4px] rounded-full border border-white/25 bg-white/10 p-[4px] backdrop-blur-[6px]">
        @foreach (['boat' => ['Boat', 'M4 15h16l-2 4H6l-2-4Zm3-1V8h10v6M12 4v4'], 'where' => ['Where To?', $compass]] as $key => [$label, $path])
            <button type="button" role="tab" data-tab="{{ $key }}" aria-selected="{{ $key === 'boat' ? 'true' : 'false' }}"
                    class="flex items-center gap-[6px] rounded-full px-[16px] py-[7px] text-[14px] font-medium text-white transition-colors duration-300
                           aria-selected:bg-white aria-selected:text-[#1B1B1B] aria-selected:shadow-sm">
                <svg class="size-[16px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Boat --}}
    <form action="{{ route('boats.schedules') }}" method="get" data-panel="boat" class="flex {{ $panel }}">
        @foreach ($mFields as $field)
            <label class="block">
                <span class="block text-center text-[14px] font-medium leading-[20px] text-white">{{ $field['label'] }}</span>
                <div class="mt-[4px] flex items-center rounded-[8px] border border-white/20 bg-white/10 p-[13px]">
                    <span class="relative mr-[8px] block h-[20px] w-[18px] shrink-0">
                        @if ($field['icon'] === 'pin')
                            <img src="{{ asset('images/icons/mobile/pin-outline.svg') }}" alt="" class="absolute inset-[15.82%_16.67%_13.97%_16.67%] size-auto h-[70%] w-[67%]">
                            <img src="{{ asset('images/icons/mobile/pin-dot.svg') }}" alt="" class="absolute inset-[34.81%_37.5%_42.4%_37.5%] size-auto h-[23%] w-[25%]">
                        @else
                            <img src="{{ asset('images/icons/mobile/calendar.svg') }}" alt="" class="absolute inset-[15.82%_12.5%] size-auto h-[68%] w-[75%]">
                        @endif
                    </span>
                    <input type="text" name="{{ $field['name'] }}" placeholder="{{ $field['placeholder'] }}" autocomplete="off"
                           @if ($field['name'] === 'date') min="{{ now()->toDateString() }}" data-calendar @else data-port-picker="{{ $field['name'] }}" @endif
                           class="w-full min-w-0 bg-transparent py-px text-[14px] leading-normal text-white placeholder:text-white/50 focus:outline-none">
                    <svg class="ml-[8px] size-[16px] shrink-0 text-white/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </label>
        @endforeach

        <div>
            <span class="block text-center text-[14px] font-medium leading-[20px] text-white">Guests</span>
            <div data-stepper class="mt-[4px] flex items-center justify-between rounded-[8px] border border-white/20 bg-white/10 px-[13px] py-[9px] text-white">
                <button type="button" data-step="-1" aria-label="Kurangi tamu" class="flex size-[30px] items-center justify-center rounded-full border border-white/40 active:bg-white/20">
                    <svg class="size-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg>
                </button>
                <input type="number" name="guests" value="1" min="1" max="20" aria-label="Guests"
                       class="w-[40px] bg-transparent p-0 text-center text-[16px] font-semibold tabular-nums focus:outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                <button type="button" data-step="1" aria-label="Tambah tamu" class="flex size-[30px] items-center justify-center rounded-full border border-white/40 active:bg-white/20">
                    <svg class="size-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                </button>
            </div>
        </div>

        <button type="submit"
                class="mt-[8px] flex w-full items-center justify-center gap-[8px] rounded-[8px] bg-[#2563eb] px-[24px] py-[12px] text-[16px] font-semibold leading-[24px] text-white
                       transition-[transform,box-shadow] duration-300 ease-smooth active:scale-[0.98]">
            <img src="{{ asset('images/icons/mobile/search.svg') }}" alt="" class="size-[20px]">
            Search
        </button>
    </form>

    {{-- Where To? --}}
    <form action="{{ route('activities.explore') }}" method="get" data-panel="where" class="hidden {{ $panel }}">
        <label class="block">
            <span class="block text-center text-[14px] font-medium leading-[20px] text-white">Destination</span>
            <div class="mt-[4px] flex items-center rounded-[8px] border border-white/20 bg-white/10 p-[13px]">
                <svg class="mr-[8px] size-[18px] shrink-0 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $compass }}"/></svg>
                <input type="text" name="q" data-destination placeholder="Where do you want to go?"
                       class="w-full min-w-0 bg-transparent py-px text-[14px] leading-normal text-white placeholder:text-white/50 focus:outline-none">
            </div>
        </label>
        <div class="flex flex-wrap justify-center gap-[8px]">
            @foreach (['Nusa Penida', 'Nusa Lembongan', 'Gili Trawangan', 'Bali'] as $place)
                <button type="button" data-chip="{{ $place }}"
                        class="rounded-full border border-white/25 bg-white/10 px-[12px] py-[6px] text-[13px] text-white active:bg-white/25">{{ $place }}</button>
            @endforeach
        </div>
        <button type="submit"
                class="mt-[8px] flex w-full items-center justify-center gap-[8px] rounded-[8px] bg-[#2563eb] px-[24px] py-[12px] text-[16px] font-semibold leading-[24px] text-white transition-transform duration-300 active:scale-[0.98]">
            <svg class="size-[20px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $compass }}"/></svg>
            Explore
        </button>
    </form>
</div>
