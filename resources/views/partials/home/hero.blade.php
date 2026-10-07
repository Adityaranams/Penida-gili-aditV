{{-- Figma nodes 1:236 (hero) + 1:244 (search bar) --}}
@php
    $fields = [
        ['icon' => 'city-from.svg', 'label' => 'From',     'placeholder' => 'Origin Place',      'name' => 'from'],
        ['icon' => 'city-to.svg',   'label' => 'To',       'placeholder' => 'Destination Place', 'name' => 'to'],
        ['icon' => 'date.svg',      'label' => 'Check In', 'placeholder' => 'Add Your Date',     'name' => 'date'],
        ['icon' => 'people.svg',    'label' => 'Guests',   'placeholder' => 'Number',            'name' => 'guests'],
    ];
@endphp

<header class="relative min-h-[486px] w-full overflow-hidden pb-[48px] lg:h-[1156px] lg:min-h-0 lg:pb-0">
    <img src="{{ asset('images/home/hero-home.png') }}" alt=""
         class="absolute inset-0 size-full object-cover object-bottom">

    <div class="relative z-10">
        @include('partials.nav', ['active' => 'home'])

        <div class="container-page">
            {{-- Intro --}}
            <div class="pt-[34px] lg:pt-[80px] text-center lg:pt-[173px] lg:text-left">
                <span data-reveal class="inline-flex h-[49px] w-[156px] items-center justify-center rounded-[30px] bg-glass-pill text-[16px] font-medium uppercase leading-[30px] text-on-hero">
                    Boat Book
                </span>

                <h1 data-reveal style="--reveal-delay: 100ms" class="mx-auto mt-[24px] max-w-[888px] lg:mx-0 lg:mt-[49px] text-[35px] lg:text-[83px] leading-[55px] lg:leading-[123px] text-on-hero">
                    Explore Tropical Island Beauty Without Limits
                </h1>

                <p data-reveal style="--reveal-delay: 200ms" class="mx-auto mt-[24px] max-w-[795px] lg:mx-0 lg:mt-[63px] text-[16px] leading-[30px] text-on-hero-muted">
                    Book fast boat tickets and private charters to your dream destinations in minutes.
                    Safe, comfortable, and hassle-free journeys.
                </p>
            </div>

            {{-- Search tabs --}}
            <div data-search-tabs data-reveal style="--reveal-delay: 320ms" class="mt-[40px] lg:mt-[86px]">
            <div role="tablist" class="inline-flex gap-[4px] rounded-full border border-white/25 bg-white/10 p-[4px] font-jakarta backdrop-blur-2xl">
                @foreach (['boat' => ['Boat', 'M4 15h16l-2 4H6l-2-4Zm3-1V8h10v6M12 4v4'], 'where' => ['Where To?', 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm3.5-12.5-2 5-5 2 2-5 5-2Z']] as $key => [$label, $path])
                    <button type="button" role="tab" data-tab="{{ $key }}" aria-selected="{{ $key === 'boat' ? 'true' : 'false' }}"
                            class="flex items-center gap-[8px] rounded-full px-[18px] py-[8px] text-[15px] font-medium text-on-hero transition-colors duration-300
                                   aria-selected:bg-white aria-selected:text-[#1B1B1B] aria-selected:shadow-sm">
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $path }}"/></svg>
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Where To panel --}}
            <form action="{{ route('activities.explore') }}" method="get" data-panel="where"
                  class="mt-[16px] hidden flex-col gap-[16px] rounded-search p-[24px] font-jakarta
                         bg-white/10 backdrop-blur-2xl backdrop-saturate-150 border border-white/25
                         shadow-[inset_0_1px_0_rgba(255,255,255,0.4),inset_0_-1px_0_rgba(255,255,255,0.1),0_8px_32px_rgba(0,0,0,0.25)]
                         lg:h-[131px] lg:flex-row lg:items-center lg:gap-[24px] lg:px-[38px] lg:py-0">
                <div class="flex flex-1 items-center gap-[16px]">
                    <img src="{{ asset('images/icons/search/city-to.svg') }}" alt="" class="size-[40px] shrink-0 opacity-80">
                    <label class="block w-full">
                        <span class="block text-[24px] font-medium leading-none text-on-hero">Destination</span>
                        <input type="text" name="q" data-destination placeholder="Where do you want to go?"
                               class="mt-[12px] w-full bg-transparent text-[21px] leading-none text-on-hero placeholder:text-on-hero-soft focus:outline-none">
                    </label>
                </div>
                <div class="flex flex-wrap gap-[8px]">
                    @foreach (['Nusa Penida', 'Nusa Lembongan', 'Gili Trawangan', 'Bali'] as $place)
                        <button type="button" data-chip="{{ $place }}"
                                class="rounded-full border border-white/25 bg-white/10 px-[14px] py-[6px] text-[14px] text-on-hero transition-colors hover:bg-white/25">{{ $place }}</button>
                    @endforeach
                </div>
                <button type="submit"
                        class="flex h-[60px] w-full shrink-0 items-center justify-center gap-[12px] rounded-search-btn bg-brand text-[21px] tracking-[-1px] text-on-brand transition-[transform,box-shadow] duration-300 ease-smooth hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand/40 lg:w-[191px]">
                    <svg class="size-[26px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm3.5-12.5-2 5-5 2 2-5 5-2Z"/></svg>
                    Explore
                </button>
            </form>

            {{-- Search bar --}}
            <form action="{{ route('boats.schedules') }}" method="get" data-panel="boat"
                  class="mt-[16px] flex flex-col gap-[16px] rounded-search p-[24px] font-jakarta
                         bg-white/10 backdrop-blur-2xl backdrop-saturate-150
                         border border-white/25
                         shadow-[inset_0_1px_0_rgba(255,255,255,0.4),inset_0_-1px_0_rgba(255,255,255,0.1),0_8px_32px_rgba(0,0,0,0.25)]
                         lg:h-[131px] lg:flex-row lg:items-center lg:gap-[24px] lg:px-[38px] lg:py-0">
                @foreach ($fields as $index => $field)
                    <div class="group flex w-full items-center gap-[16px] {{ $index === 0 ? 'lg:w-auto lg:flex-none' : 'flex-1' }}">
                        <img src="{{ asset('images/icons/search/'.$field['icon']) }}" alt=""
                             class="size-[40px] shrink-0 opacity-80 transition-opacity duration-300 group-focus-within:opacity-100">
                        {{-- The guests cell is a plain <div>, not a <label>: a label adopts the
                             first labelable element inside it as its control, and here that is the
                             − button, so pointing at + put :hover on − as well. --}}
                        <{{ $field['name'] === 'guests' ? 'div' : 'label' }} class="block w-full">
                            <span class="block text-[24px] font-medium leading-none text-on-hero">{{ $field['label'] }}</span>
                            @if ($field['name'] === 'guests')
                                <span data-stepper class="mt-[12px] flex items-center gap-[12px] text-[21px] leading-none text-on-hero">
                                    <button type="button" data-step="-1" aria-label="Kurangi tamu"
                                            class="flex size-[28px] shrink-0 items-center justify-center rounded-full border border-white/40 leading-none transition-colors hover:bg-white/20"><svg class="size-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/></svg></button>
                                    <input type="number" name="guests" value="1" min="1" max="20" aria-label="Jumlah tamu"
                                           class="w-[32px] bg-transparent p-0 text-center font-medium tabular-nums focus:outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                    <button type="button" data-step="1" aria-label="Tambah tamu"
                                            class="flex size-[28px] shrink-0 items-center justify-center rounded-full border border-white/40 leading-none transition-colors hover:bg-white/20"><svg class="size-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg></button>
                                </span>
                            @else
                                <span class="mt-[12px] flex items-center">
                                    <input type="text" name="{{ $field['name'] }}" placeholder="{{ $field['placeholder'] }}"
                                           @if ($field['name'] === 'date') min="{{ now()->toDateString() }}" data-calendar @else data-port-picker="{{ $field['name'] }}" autocomplete="off" @endif
                                           class="w-full min-w-0 bg-transparent text-[21px] leading-none text-on-hero [color-scheme:dark] placeholder:text-on-hero-soft focus:outline-none [&::-webkit-calendar-picker-indicator]:!hidden [&::-webkit-list-button]:!hidden {{ $field['name'] === 'to' ? 'lg:w-[200px]' : 'lg:w-[150px]' }}">
                                </span>
                            @endif
                        </{{ $field['name'] === 'guests' ? 'div' : 'label' }}>
                        @unless ($field['name'] === 'guests')
                            <button type="button" data-open-picker aria-label="Buka pilihan {{ $field['label'] }}" class="shrink-0 rounded-full p-[4px] text-on-hero-soft transition-colors hover:bg-white/15 hover:text-on-hero">
                                <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                        @endunless
                    </div>

                    @if ($index === 0)
                        {{-- Swap icon: centered between From & To --}}
                        <div class="hidden shrink-0 items-center justify-center self-stretch lg:flex">
                            <img src="{{ asset('images/icons/search/transfer.svg') }}" alt="Swap" class="size-[29px]">
                        </div>
                    @elseif ($index < count($fields) - 1)
                        <span class="hidden h-[85px] w-px shrink-0 bg-on-hero/30 lg:block"></span>
                    @endif
                @endforeach


                <button type="submit"
                        class="group flex h-[60px] w-full shrink-0 lg:w-[191px] items-center justify-center gap-[12px] rounded-search-btn bg-brand text-[21px] tracking-[-1px] text-on-brand
                               transition-[transform,box-shadow] duration-300 ease-smooth hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand/40">
                    <img src="{{ asset('images/icons/search/search.svg') }}" alt=""
                         class="size-[29px] transition-transform duration-500 ease-smooth group-hover:rotate-12">
                    Search
                </button>
            </form>
            </div>
        </div>
    </div>
</header>

