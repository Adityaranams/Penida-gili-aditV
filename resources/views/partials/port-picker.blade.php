{{-- Harbor picker modal for any input marked `data-port-picker="from|to"` (see resources/js/port-picker.js). --}}
@php
    $ports = \App\Models\Port::query()->orderBy('name')->get(['name', 'area'])
        ->map(fn ($port) => ['name' => $port->name, 'area' => $port->area ?: 'Other']);
    $areas = $ports->pluck('area')->unique()->sort()->values();
@endphp

<dialog data-port-dialog
        class="m-auto w-[calc(100%-32px)] max-w-[440px] overflow-hidden rounded-[22px] lg:max-w-[560px] lg:rounded-[28px] bg-surface p-0 font-jakarta text-ink shadow-2xl backdrop:bg-black/50 backdrop:backdrop-blur-sm">
    <div class="flex max-h-[min(520px,72vh)] lg:max-h-[min(640px,88vh)] flex-col">
        {{-- Header --}}
        <div class="relative shrink-0 bg-[#0F2236] px-[18px] pb-[14px] pt-[16px] lg:px-[22px] lg:pb-[20px] lg:pt-[22px] text-on-hero">
            <button type="button" data-port-close aria-label="Close"
                    class="absolute right-[12px] top-[12px] flex size-[30px] lg:right-[16px] lg:top-[16px] lg:size-[34px] items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20">
                <svg class="size-[14px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>

            <p data-port-eyebrow class="text-[10px] lg:text-[11px] font-semibold uppercase tracking-[0.15em] text-brand">Departure</p>
            <p data-port-title class="mt-[2px] pr-[36px] text-[17px] lg:mt-[4px] lg:pr-[40px] lg:text-[22px] font-bold leading-tight">Where are you sailing from?</p>

            <label class="mt-[12px] flex items-center gap-[8px] rounded-full bg-white/10 px-[14px] py-[8px] lg:mt-[16px] lg:gap-[10px] lg:px-[16px] lg:py-[10px] ring-1 ring-white/15 focus-within:ring-brand">
                <svg class="size-[16px] shrink-0 text-on-hero-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z"/></svg>
                <input type="search" data-port-search placeholder="Search harbor or island…"
                       class="w-full bg-transparent text-[13px] lg:text-[14px] text-on-hero placeholder:text-on-hero-muted focus:outline-none">
            </label>
        </div>

        {{-- Island filter --}}
        <div class="flex shrink-0 gap-[6px] overflow-x-auto border-b border-line px-[18px] py-[10px] lg:gap-[8px] lg:px-[22px] lg:py-[12px] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach ($areas->prepend('All') as $area)
                <button type="button" data-port-area="{{ $area }}" @if ($area === 'All') data-active @endif
                        class="inline-flex h-[30px] shrink-0 items-center rounded-full border border-line px-[12px] text-[12px] font-medium leading-none lg:h-auto lg:px-[14px] lg:py-[6px] lg:leading-normal text-ink-muted transition-colors hover:text-ink
                               data-[active]:border-brand lg:data-[active]:border-transparent data-[active]:bg-brand data-[active]:text-on-brand">
                    {{ $area }}
                </button>
            @endforeach
        </div>

        {{-- Harbors --}}
        <div class="grid min-h-0 grid-cols-1 content-start gap-[6px] overflow-y-auto p-[12px] lg:gap-[10px] lg:p-[22px] sm:grid-cols-2">
            @foreach ($ports as $port)
                <button type="button" data-port-option="{{ $port['name'] }}" data-area="{{ $port['area'] }}"
                        class="group flex items-center gap-[10px] rounded-[12px] px-[10px] py-[8px] lg:gap-[12px] lg:rounded-[16px] lg:bg-surface-muted lg:p-[12px] text-left ring-1 ring-transparent transition
                               hover:bg-brand/5 hover:ring-brand/30 data-[selected]:bg-brand/10 data-[selected]:ring-brand">
                    <span class="flex size-[32px] shrink-0 items-center justify-center rounded-full bg-brand/10 text-brand lg:size-[40px] lg:bg-surface lg:shadow-sm">
                        <svg class="size-[15px] lg:size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22V8m0 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM5 12H2a10 10 0 0 0 20 0h-3"/></svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-[13px] lg:text-[14px] font-semibold">{{ $port['name'] }}</span>
                        <span class="block text-[11px] text-ink-muted">{{ $port['area'] }}</span>
                    </span>
                    <svg class="size-[18px] shrink-0 text-brand opacity-0 transition-opacity group-data-[selected]:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                </button>
            @endforeach

            <p data-port-empty hidden class="col-span-full py-[24px] text-center text-[13px] text-ink-muted">No harbor matches your search.</p>
        </div>
    </div>
</dialog>
