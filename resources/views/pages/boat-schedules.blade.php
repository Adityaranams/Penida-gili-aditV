{{-- Fast boat schedules — results of the home hero search --}}
@extends('layouts.app')

@section('title', 'Fast Boat Schedules')

@section('nav-active', 'boat')

@php
    use App\Support\Money;

    $query = fn (array $overrides = []) => route('boats.schedules', array_filter([
        'from' => $search['from'], 'to' => $search['to'], 'date' => $search['date']->toDateString(),
        'guests' => $search['guests'], 'time' => $search['time'] === 'all' ? null : $search['time'],
        'sort' => $search['sort'] === 'earliest' ? null : $search['sort'],
        ...$overrides,
    ]));

    $periods = [
        'all'       => ['All sailings', null],
        'morning'   => ['Morning · before 12:00', 'M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10 1.4 1.4M3 12h2m14 0h2M5.6 18.4 7 17M17 7l1.4-1.4M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z'],
        'afternoon' => ['Afternoon · 12:00 – 17:00', 'M12 4v2M4.9 7.9l1.4 1.4M2 15h2m16 0h2m-4.3-5.7 1.4-1.4M7 15a5 5 0 0 1 10 0M3 19h18'],
        'evening'   => ['Evening · after 17:00', 'M20 14.5A8 8 0 0 1 9.5 4a8 8 0 1 0 10.5 10.5Z'],
    ];

    $icon = fn (string $path, string $class = 'size-[16px]') => '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="'.$path.'"/></svg>';
@endphp

@section('hero')
    <header class="relative w-full overflow-hidden pb-[40px] lg:h-[360px] lg:pb-0">
        <img src="{{ asset('images/boats/hero-boat.png') }}" alt="" class="absolute inset-0 size-full object-cover object-bottom">
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/50"></div>

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'boat'])

            <div class="container-page mt-[24px] font-jakarta text-on-hero lg:mt-[40px]">
                <nav class="flex items-center gap-[6px] text-[13px] text-on-hero-muted" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-on-hero">Home</a> <span>›</span>
                    <a href="{{ route('boats.index') }}" class="hover:text-on-hero">Boat</a> <span>›</span>
                    <span class="text-on-hero">Schedules</span>
                </nav>

                <span class="mt-[14px] inline-flex rounded-full bg-glass-pill px-[12px] py-[5px] text-[11px] font-semibold uppercase tracking-wider">
                    + Fast boat schedules
                </span>

                <h1 class="mt-[14px] flex flex-wrap items-center gap-x-[14px] text-[30px] font-bold leading-tight lg:text-[48px]">
                    {{ $search['from'] ?: 'All ports' }}
                    <span class="text-brand">→</span>
                    {{ $search['to'] ?: 'All destinations' }}
                </h1>

                <div class="mt-[18px] flex flex-wrap gap-[10px] text-[13px]">
                    @foreach ([
                        ['M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z', $search['date']->format('l, j F Y')],
                        ['M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', $search['guests'].' '.str('guest')->plural($search['guests'])],
                        ['M4 15h16l-2 4H6l-2-4Zm3-1V8h10v6M12 4v4', $periodCounts['all'].' '.str('sailing')->plural($periodCounts['all'])],
                    ] as [$path, $label])
                        <span class="inline-flex items-center gap-[6px] rounded-full border border-white/25 bg-white/10 px-[12px] py-[6px] backdrop-blur">
                            {!! $icon($path, 'size-[14px]') !!} {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <section class="container-page grid gap-[28px] py-[40px] font-jakarta lg:grid-cols-[300px_1fr] lg:py-[56px]">
        {{-- Sidebar --}}
        <aside class="flex flex-col gap-[24px]">
            {{-- Change search --}}
            <form action="{{ route('boats.schedules') }}" method="get"
                  class="rounded-[18px] border border-line bg-surface p-[18px] shadow-[0_8px_30px_rgba(0,0,0,0.06)]">
                <p class="flex items-center gap-[6px] text-[11px] font-semibold uppercase tracking-wider text-ink-muted">
                    {!! $icon('m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z', 'size-[14px]') !!} Change search
                </p>

                <div class="mt-[12px] inline-flex gap-[4px] rounded-full bg-surface-muted p-[4px] text-[13px] font-medium">
                    <span class="flex items-center gap-[6px] rounded-full bg-surface px-[14px] py-[6px] shadow-sm">{!! $icon('M4 15h16l-2 4H6l-2-4Zm3-1V8h10v6M12 4v4', 'size-[14px]') !!} Boat</span>
                    <a href="{{ route('activities.index') }}" class="flex items-center gap-[6px] rounded-full px-[14px] py-[6px] text-ink-muted hover:text-ink">{!! $icon('M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm3.5-12.5-2 5-5 2 2-5 5-2Z', 'size-[14px]') !!} Where To?</a>
                </div>
                <div class="mt-[14px] flex flex-col gap-[8px]">
                    @foreach ([
                        ['from', 'From', 'Origin port', 'M12 2v20M5 9l7-7 7 7'],
                        ['to', 'To', 'Destination port', 'M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z'],
                    ] as [$name, $label, $placeholder, $path])
                        <label class="flex items-center gap-[12px] rounded-[12px] bg-surface-muted px-[12px] py-[10px]">
                            <span class="flex size-[34px] shrink-0 items-center justify-center rounded-[10px] bg-brand/10 text-brand">{!! $icon($path) !!}</span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-[10px] font-semibold uppercase tracking-wider text-ink-muted">{{ $label }}</span>
                                <input type="text" name="{{ $name }}" value="{{ $search[$name] }}" placeholder="{{ $placeholder }}" data-port-picker="{{ $name }}" autocomplete="off"
                                       class="w-full bg-transparent text-[14px] text-ink placeholder:text-ink-muted/60 focus:outline-none [&::-webkit-calendar-picker-indicator]:!hidden">
                            </span>
                        </label>
                    @endforeach

                    <label class="flex items-center gap-[12px] rounded-[12px] bg-surface-muted px-[12px] py-[10px]">
                        <span class="flex size-[34px] shrink-0 items-center justify-center rounded-[10px] bg-brand/10 text-brand">{!! $icon('M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z') !!}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-ink-muted">Date</span>
                            <input type="date" name="date" value="{{ $search['date']->toDateString() }}" min="{{ now()->toDateString() }}" data-calendar
                                   class="w-full bg-transparent text-[14px] text-ink focus:outline-none">
                        </span>
                    </label>

                    <div class="flex items-center gap-[12px] rounded-[12px] bg-surface-muted px-[12px] py-[10px]">
                        <span class="flex size-[34px] shrink-0 items-center justify-center rounded-[10px] bg-brand/10 text-brand">{!! $icon('M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z') !!}</span>
                        <span class="flex-1">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-ink-muted">Guests</span>
                            <span data-stepper class="mt-[2px] flex items-center gap-[10px] text-[14px] text-ink">
                                <button type="button" data-step="-1" aria-label="Kurangi tamu" class="flex size-[24px] items-center justify-center rounded-full border border-black/15 hover:bg-black/5">{!! $icon('M5 12h14', 'size-[12px]') !!}</button>
                                <input type="number" name="guests" value="{{ $search['guests'] }}" min="1" max="20"
                                       class="w-[28px] bg-transparent p-0 text-center font-semibold tabular-nums focus:outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" data-step="1" aria-label="Tambah tamu" class="flex size-[24px] items-center justify-center rounded-full border border-black/15 hover:bg-black/5">{!! $icon('M12 5v14M5 12h14', 'size-[12px]') !!}</button>
                            </span>
                        </span>
                    </div>
                </div>

                <button type="submit" class="mt-[14px] flex h-[44px] w-full items-center justify-center gap-[8px] rounded-[12px] bg-brand text-[14px] font-semibold text-on-brand transition hover:brightness-110">
                    {!! $icon('m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z') !!} Search
                </button>
            </form>

            {{-- Departure time --}}
            <div>
                <p class="flex items-center gap-[6px] text-[14px] font-semibold text-ink">
                    {!! $icon('M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z') !!} Departure time
                </p>
                <div class="mt-[12px] flex flex-col gap-[6px]">
                    @foreach ($periods as $key => [$label, $path])
                        @php $isActive = $search['time'] === $key; @endphp
                        <a href="{{ $query(['time' => $key === 'all' ? null : $key]) }}"
                           class="flex items-center justify-between rounded-[10px] px-[12px] py-[9px] text-[13px] transition
                                  {{ $isActive ? 'bg-brand text-on-brand' : 'border border-line bg-surface text-ink-soft hover:border-brand/40' }}">
                            <span class="flex items-center gap-[8px]">{!! $icon($path ?? 'M4 6h16M4 12h16M4 18h16', 'size-[14px]') !!} {{ $label }}</span>
                            <span class="min-w-[22px] rounded-full px-[6px] text-center text-[11px] font-semibold {{ $isActive ? 'bg-white/25' : 'bg-surface-muted' }}">{{ $periodCounts[$key] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Private boat --}}
            <div class="rounded-[18px] bg-[#0F2236] p-[20px] text-on-hero">
                {!! $icon('M12 22V8m0 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5 12H2a10 10 0 0 0 20 0h-3', 'size-[26px] text-brand') !!}
                <p class="mt-[12px] text-[16px] font-semibold">Need a private boat?</p>
                <p class="mt-[6px] text-[12px] leading-[18px] text-on-hero-muted">Charter a speed boat for your group, any time of day. Our team replies within minutes.</p>
                <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
                   class="mt-[14px] inline-flex rounded-full bg-surface px-[14px] py-[7px] text-[12px] font-semibold text-ink hover:bg-white/90">Chat on WhatsApp →</a>
            </div>
        </aside>

        @include('partials.port-picker')

        {{-- Results --}}
        <div class="min-w-0">
            {{-- Date strip --}}
            <div class="flex gap-[10px] overflow-x-auto pb-[4px] sm:justify-center">
                @foreach ($days as $day)
                    @php $isSelected = $day['date']->isSameDay($search['date']); @endphp
                    <a @unless ($day['is_past']) href="{{ $query(['date' => $day['date']->toDateString()]) }}" @endunless @if ($day['is_past']) aria-disabled="true" @endif
                       class="flex w-[64px] shrink-0 flex-col items-center rounded-[12px] border py-[8px] transition
                              {{ $isSelected ? 'border-brand bg-brand text-on-brand shadow-lg shadow-brand/30' : 'border-line bg-surface text-ink hover:border-brand/40' }} {{ $day['is_past'] ? 'pointer-events-none opacity-40' : '' }}">
                        <span class="text-[10px] font-semibold uppercase {{ $isSelected ? 'text-on-brand/80' : 'text-ink-muted' }}">{{ $day['date']->format('D') }}</span>
                        <span class="text-[20px] font-bold leading-tight">{{ $day['date']->format('j') }}</span>
                        <span class="text-[9px] {{ $isSelected ? 'text-on-brand/80' : 'text-ink-muted' }}">{{ $day['from_price'] ? Money::compact($day['from_price'], '') : '—' }}</span>
                    </a>
                @endforeach
            </div>

            <div class="mt-[22px] flex flex-wrap items-center justify-between gap-[12px]">
                <p class="text-[13px] text-ink-muted">
                    <strong class="text-ink">{{ $schedules->count() }}</strong> {{ str('sailing')->plural($schedules->count()) }} found
                    @if ($fromPrice) · from <strong class="text-brand">{{ Money::idr($fromPrice) }}</strong> @endif
                </p>
                <div class="inline-flex gap-[4px] rounded-full bg-surface-muted p-[3px] text-[12px] font-medium">
                    @foreach (['earliest' => 'Earliest', 'cheapest' => 'Cheapest'] as $key => $label)
                        <a href="{{ $query(['sort' => $key === 'earliest' ? null : $key]) }}"
                           class="rounded-full px-[14px] py-[5px] {{ $search['sort'] === $key ? 'bg-[#0F2236] text-on-hero' : 'text-ink-muted hover:text-ink' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>

            <div class="mt-[18px] flex flex-col gap-[18px]">
                @forelse ($schedules as $schedule)
                    @include('partials.boat.schedule-card')
                @empty
                    <div class="rounded-[18px] border border-dashed border-line bg-surface p-[40px] text-center">
                        <p class="text-[16px] font-semibold text-ink">No sailings on {{ $search['date']->format('l, j F') }}</p>
                        <p class="mt-[6px] text-[13px] text-ink-muted">Try another date above, a different departure time, or change your ports.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
