{{-- One sailing on the fast boat schedules page. Expects $schedule, $search and the page's $icon helper. --}}
@php
    $vessel = $schedule->vessel;
    $hours = intdiv($schedule->duration_minutes, 60);
    $minutes = $schedule->duration_minutes % 60;

    $facilityIcons = [
        'air' => 'M8 16a4 4 0 0 1-4-4 4 4 0 0 1 4-4h9a3 3 0 1 0-3-3M3 12h13a3 3 0 1 1-3 3',
        'insurance' => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z',
        'jacket' => 'M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Zm0-6a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
        'bag' => 'M6 7h12l1 14H5L6 7Zm3 0V5a3 3 0 0 1 6 0v2',
        'sound' => 'M11 5 6 9H2v6h4l5 4V5Zm4.5 3.5a5 5 0 0 1 0 7',
    ];
@endphp

<article class="grid overflow-hidden rounded-[24px] bg-surface shadow-[0_10px_40px_rgba(15,34,54,0.08)] ring-1 ring-black/[0.04] md:grid-cols-[220px_1fr] xl:grid-cols-[220px_1fr_210px]">
    <a @if ($vessel) href="{{ route('boats.vessel', $vessel) }}" aria-label="View {{ $vessel->name }} details" @endif
       class="group/photo relative block h-[200px] overflow-hidden md:h-auto">
        <img src="{{ $vessel?->image_url ?? $schedule->operator->image_url }}" alt="{{ $vessel?->name ?? $schedule->operator->name }}" class="absolute inset-0 size-full object-cover transition-transform duration-500 ease-out group-hover/photo:scale-105">
        <span class="absolute left-[12px] top-[12px] inline-flex items-center gap-[6px] rounded-full bg-surface px-[10px] py-[4px] text-[10px] font-bold uppercase tracking-wider text-brand shadow-sm">
            <span class="size-[7px] rounded-full border-2 border-brand"></span> Departure
        </span>
        @if ($vessel?->rating)
            <span class="absolute bottom-[12px] left-[12px] inline-flex items-center gap-[4px] rounded-full bg-surface px-[8px] py-[3px] text-[11px] font-semibold text-ink shadow-sm">
                <span class="text-[#FFB800]">★</span> {{ $vessel->rating }}
            </span>
        @endif
    </a>

    <div class="px-[20px] py-[18px]">
        @if ($vessel?->rating)
            @include('components.rating-stars', ['rating' => $vessel->rating, 'size' => 13, 'gap' => 2])
        @endif
        <h2 class="mt-[6px] text-[18px] font-bold uppercase leading-tight text-ink">
            @if ($vessel)
                <a href="{{ route('boats.vessel', $vessel) }}" class="transition-colors hover:text-brand">{{ $vessel->name }}</a>
            @else
                {{ $schedule->operator->name }}
            @endif
        </h2>
        <p class="mt-[2px] text-[12px] text-ink-muted">
            {{ collect([$schedule->operator->name, $vessel?->type, $vessel?->capacity ? $vessel->capacity.' seats' : null])->filter()->implode(' · ') }}
        </p>

        <div class="mt-[16px] flex items-center gap-[14px]">
            <div>
                <p class="text-[26px] font-bold leading-none tracking-tight text-ink">{{ substr($schedule->departure_time, 0, 5) }}</p>
                <p class="mt-[6px] text-[11px] font-bold uppercase tracking-wide text-[#E5484D]">{{ $schedule->fromPort->name }}</p>
            </div>
            <div class="flex flex-1 flex-col items-center gap-[4px] text-[10px] text-ink-muted">
                <span>{{ $hours ? $hours.'h ' : '' }}{{ $minutes ? $minutes.'m' : '' }}</span>
                <span class="flex w-full items-center gap-[6px]">
                    <span class="h-[2px] flex-1 rounded-full bg-brand/70"></span>
                    {!! $icon('M4 15h16l-2 4H6l-2-4Zm3-1V8h10v6M12 4v4', 'size-[14px] text-brand') !!}
                    <span class="h-[2px] flex-1 rounded-full bg-line"></span>
                </span>
            </div>
            <div class="text-right">
                <p class="text-[26px] font-bold leading-none tracking-tight text-ink">{{ substr($schedule->arrival_time, 0, 5) }}</p>
                <p class="mt-[6px] text-[11px] font-bold uppercase tracking-wide text-[#E5484D]">{{ $schedule->toPort->name }}</p>
            </div>
        </div>

        <div class="mt-[16px] grid overflow-hidden rounded-[14px] text-center text-[11px] ring-1 ring-black/[0.05] {{ $schedule->price_foreign ? 'grid-cols-2' : 'grid-cols-1' }}">
            <div class="bg-brand/[0.07] px-[10px] py-[10px]">
                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-brand">Local</p>
                <div class="mt-[6px] grid grid-cols-2">
                    <span><span class="block text-ink-muted">Adult</span><strong class="text-[14px] text-ink">{{ number_format($schedule->price_adult, 0, ',', '.') }}</strong></span>
                    <span><span class="block text-ink-muted">Child</span><strong class="text-[14px] text-ink">{{ number_format($schedule->price_child, 0, ',', '.') }}</strong></span>
                </div>
            </div>
            @if ($schedule->price_foreign)
                <div class="bg-[#FFF6E0] px-[10px] py-[10px]">
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-[#E08A00]">Foreign</p>
                    <div class="mt-[6px] grid grid-cols-2">
                        <span><span class="block text-ink-muted">Adult</span><strong class="text-[14px] text-ink">{{ number_format($schedule->price_foreign, 0, ',', '.') }}</strong></span>
                        <span><span class="block text-ink-muted">Child</span><strong class="text-[14px] text-ink">{{ $schedule->price_foreign_child ? number_format($schedule->price_foreign_child, 0, ',', '.') : '—' }}</strong></span>
                    </div>
                </div>
            @endif
        </div>

        @if ($vessel?->facilities)
            <div class="mt-[14px] flex flex-wrap gap-[8px]">
                @foreach ($vessel->facilities as $facility)
                    @php
                        $name = is_array($facility) ? ($facility['name'] ?? '') : $facility;
                        $path = collect($facilityIcons)->first(fn ($p, $key) => str_contains(strtolower($name), $key)) ?? 'm5 12 5 5L20 7';
                    @endphp
                    <span class="inline-flex items-center gap-[6px] rounded-full border border-line px-[10px] py-[4px] text-[11px] text-ink-soft">
                        {!! $icon($path, 'size-[13px] text-ink-muted') !!} {{ $name }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    <div class="flex flex-col items-stretch justify-center gap-[6px] border-t border-line bg-[#F7F9FB] p-[20px] md:col-span-2 xl:col-span-1 xl:border-l xl:border-t-0">
        <p class="text-[12px] text-ink-muted xl:text-center">Total · {{ $search['guests'] }} {{ str('adult')->plural($search['guests']) }}</p>
        <p class="text-[22px] font-bold tracking-tight text-ink xl:text-center">{{ \App\Support\Money::idr($schedule->price_adult * $search['guests']) }}</p>
        <a href="{{ route('boats.order', [$schedule->operator, 'schedule' => $schedule->id, 'date' => $search['date']->toDateString(), 'adults' => $search['guests']]) }}"
           class="group mt-[10px] flex h-[48px] items-center justify-center gap-[8px] rounded-[14px] bg-brand text-[15px] font-semibold text-on-brand shadow-lg shadow-brand/25 transition hover:-translate-y-0.5 hover:brightness-110">
            Select {!! $icon('M5 12h14m-6-6 6 6-6 6', 'size-[16px] transition-transform group-hover:translate-x-0.5') !!}
        </a>
    </div>
</article>
