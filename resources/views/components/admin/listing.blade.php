{{-- The console listing shell shared by Boat, Schedule, Activity, Hotel, Article
     and Booking Report (Figma 1:6901, 1:9017, 1:9970, 1:9280, 1:9637, 1:10402).

     Every one of those frames is the same three pieces — a titled header with a
     primary action, a filter toolbar, then a card holding the table and its
     pagination footer — so they live here once and each page supplies only its
     own columns and rows. --}}
@props([
    'heading',
    'subtitle',
    'action' => null,
    'actionHref' => '#',
    'columns' => [],
    'centerColumns' => [],
    'summary' => '',
    'paginator' => null,
    'panelTitle' => null,
    'panelBadge' => null,
    'selectable' => false,
])

<div class="flex flex-wrap items-start justify-between gap-4 pt-[24px]">
    <div data-reveal>
        <h1 class="text-[36px] font-bold tracking-[-0.96px] text-admin-ink">{{ $heading }}</h1>
        <p class="mt-[4px] text-[16px] text-admin-muted">{{ $subtitle }}</p>
    </div>

    @if ($action)
        <a href="{{ $actionHref }}" data-reveal
           class="flex h-[44px] items-center gap-[8px] rounded-[8px] bg-editorial px-[20px] text-[16px] font-semibold text-white
                  transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
            <span aria-hidden="true">+</span>
            {{ $action }}
        </a>
    @endif
</div>

@isset($toolbar)
    <div data-reveal class="mt-[24px] rounded-admin border border-[rgba(192,199,211,0.2)] bg-[rgba(241,244,246,0.3)] p-[24px]">
        {{ $toolbar }}
    </div>
@endisset

<section data-reveal class="mt-[24px] overflow-hidden rounded-admin border border-[rgba(192,199,211,0.2)] bg-surface shadow-admin">
    @isset($filters)
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[rgba(192,199,211,0.2)] bg-[rgba(241,244,246,0.3)] px-[24px] pb-[25px] pt-[24px]">
            {{ $filters }}
        </div>
    @endisset

    @if ($panelTitle)
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[rgba(192,199,211,0.2)] px-[24px] py-[20px]">
            <h2 class="flex items-center gap-[10px] text-[18px] font-bold leading-[28px] text-editorial-ink">
                {{ $panelTitle }}
                @if ($panelBadge)
                    <span class="rounded-full bg-[#d2e4ff] px-[10px] py-[2px] text-[12px] font-normal text-[#00497e]">{{ $panelBadge }}</span>
                @endif
            </h2>
            @isset($panelAction)
                {{ $panelAction }}
            @endisset
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px] border-collapse text-left">
            <thead class="bg-[#f1f4f6]">
                <tr>
                    @if ($selectable)
                        {{-- Select-all for the rows on this page (resources/js/bulk-select.js). --}}
                        <th scope="col" class="w-[52px] pl-[20px] pr-[4px]">
                            <input type="checkbox" data-bulk-all aria-label="Select all rows on this page"
                                   class="size-[18px] cursor-pointer rounded-[4px] accent-editorial">
                        </th>
                    @endif

                    @foreach ($columns as $column)
                        <th scope="col" @class([
                                'px-[16px] py-[16px] text-[14px] font-semibold uppercase leading-[20px] tracking-[0.5px] text-editorial-body',
                                'text-center' => in_array($column, $centerColumns, true),
                            ])>
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-[rgba(192,199,211,0.2)] bg-[rgba(241,244,246,0.3)] px-[24px] py-[16px]">
        @if ($paginator)
            <x-admin.pagination :paginator="$paginator" />
        @else
            <p class="text-[14px] leading-[20px] text-editorial-body">{{ $summary }}</p>
        @endif
    </div>
</section>
