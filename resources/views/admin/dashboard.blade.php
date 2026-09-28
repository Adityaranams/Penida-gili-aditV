{{-- Figma node 1:6642 — admin Dashboard --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('admin-active', 'dashboard')

@section('content')
    <div class="pt-[24px]">
        <h1 data-reveal class="text-[36px] font-bold tracking-[-0.96px] text-admin-ink">Dashboard</h1>
        <p data-reveal class="mt-[4px] text-[16px] text-admin-muted">Today&rsquo;s operational summary for Boat Booking.</p>
    </div>

    {{-- Figma node 1:6658 — KPI row --}}
    <div class="mt-[28px] grid [&>*]:min-w-0 gap-[16px] md:grid-cols-2 xl:grid-cols-3">
        @foreach ($kpis as $index => $kpi)
            <article data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                     class="flex flex-col gap-[4px] rounded-admin border border-[rgba(224,227,229,0.5)] bg-surface p-[25px] shadow-admin
                            transition-[transform,box-shadow] duration-500 ease-smooth hover:-translate-y-1 hover:shadow-card-hover">
                <div class="flex items-start justify-between">
                    <span class="flex size-[48px] items-center justify-center rounded-full bg-[#d5e2e9]">
                        <img src="{{ asset('images/icons/admin/'.$kpi['icon']) }}" alt="" class="size-[20px] object-contain">
                    </span>

                    <span @class([
                        'flex items-center gap-[4px] rounded-full px-[8px] py-[4px] text-[14px] font-semibold leading-[20px]',
                        'bg-[#f0fdf4] text-[#16a34a]' => $kpi['badgeTone'] === 'up',
                        'bg-editorial-rule text-editorial-body' => $kpi['badgeTone'] === 'neutral',
                    ])>
                        @if ($kpi['badgeTone'] === 'up')
                            <img src="{{ asset('images/icons/admin/trend-up.svg') }}" alt="" class="h-[6px] w-[10px]">
                        @endif
                        {{ $kpi['badge'] }}
                    </span>
                </div>

                <p class="pt-[12px] text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-editorial-body">{{ $kpi['label'] }}</p>
                <p class="text-[36px] font-bold leading-[60px] tracking-[-0.96px] text-editorial-ink">{{ $kpi['value'] }}</p>
            </article>
        @endforeach
    </div>

    {{-- Figma node 1:6697 — boat status --}}
    <section data-reveal class="mt-[26px] rounded-admin border border-[rgba(224,227,229,0.5)] bg-surface p-[25px] shadow-admin">
        <div class="flex items-center justify-between pb-[24px]">
            <h2 class="text-[24px] font-semibold leading-[32px] text-editorial-ink">Boat Status</h2>
            <a href="{{ route('admin.boats') }}" class="text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial transition-colors hover:underline">View All</a>
        </div>

        <ul class="flex flex-col gap-[16px]">
            @foreach ($vessels as $vessel)
                <li class="flex flex-col gap-[4px] rounded-[8px] border border-editorial-line p-[17px]">
                    <div class="flex items-start justify-between gap-4">
                        <h3 class="text-[16px] font-semibold leading-[24px] text-editorial-ink">{{ $vessel['name'] }}</h3>

                        <span @class([
                            'flex shrink-0 items-center gap-[4px] rounded-full px-[8px] py-[4px] text-[12px] font-semibold leading-[16px]',
                            'bg-[#dcfce7] text-[#15803d]' => $vessel['state'] === 'transit',
                            'bg-[#dbeafe] text-[#1d4ed8]' => $vessel['state'] === 'docked',
                        ])>
                            <span @class([
                                'size-[6px] rounded-full',
                                'bg-[#16a34a]' => $vessel['state'] === 'transit',
                                'bg-[#2563eb]' => $vessel['state'] === 'docked',
                            ])></span>
                            {{ $vessel['status'] }}
                        </span>
                    </div>

                    <ul class="flex items-center gap-[16px] pb-[8px] pt-[4px] text-[14px] leading-[20px] text-editorial-body">
                        @foreach ($vessel['meta'] as $meta)
                            <li class="flex items-center gap-[4px]">
                                <img src="{{ asset('images/icons/admin/'.$meta['icon']) }}" alt="" class="size-[13px] object-contain">
                                {{ $meta['label'] }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="h-[6px] w-full rounded-full bg-[#e5e9eb]">
                        <div @class([
                                 'h-[6px] rounded-full',
                                 'bg-editorial' => $vessel['state'] === 'transit',
                                 'bg-[#546066]' => $vessel['state'] === 'docked',
                             ])
                             style="width: {{ $vessel['progress'] }}%"></div>
                    </div>

                    <p class="text-right text-[12px] leading-[16px] text-editorial-body">{{ $vessel['eta'] }}</p>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Figma node 1:6763 — recent transactions --}}
    <section id="transactions" data-reveal class="mt-[29px] scroll-mt-[24px] overflow-hidden rounded-admin border border-[rgba(224,227,229,0.5)] bg-surface shadow-admin">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-editorial-line px-[24px] pb-[25px] pt-[24px]">
            <h2 class="text-[24px] font-semibold leading-[32px] text-editorial-ink">Recent Transactions</h2>

            {{-- Search + product filter; both submit the same GET form so they combine. --}}
            <form method="get" action="{{ route('admin.dashboard') }}" data-keep-anchor="#transactions" class="flex flex-wrap items-start gap-[8px]">
                <label class="relative block">
                    <span class="sr-only">Search</span>
                    <input type="search" name="q" value="{{ $transactionsFilters['q'] ?? '' }}" placeholder="Search..."
                           class="rounded-[6px] border border-editorial-line bg-[#f1f4f6] py-[8px] pl-[37px] pr-[17px] text-[14px] text-editorial-ink placeholder:text-[#6b7280] focus:outline-2 focus:outline-editorial">
                    <img src="{{ asset('images/icons/admin/search-sm.svg') }}" alt=""
                         class="pointer-events-none absolute left-[12px] top-1/2 size-[15px] -translate-y-1/2">
                </label>

                <label class="relative block">
                    <span class="sr-only">Filter by product</span>
                    <select name="type" onchange="this.form.requestSubmit()"
                            class="appearance-none rounded-[6px] border border-editorial-line bg-surface px-[34px] py-[8px] text-center text-[14px] leading-[20px] text-editorial-ink focus:outline-2 focus:outline-editorial">
                        <option value="">All</option>
                        @foreach (['boat' => 'Boat', 'activity' => 'Activity', 'hotel' => 'Hotel'] as $value => $label)
                            <option value="{{ $value }}" @selected(($transactionsFilters['type'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/admin/filter.svg') }}" alt=""
                         class="pointer-events-none absolute left-[13px] top-1/2 h-[9px] w-[13.5px] -translate-y-1/2">
                </label>
            </form>
        </div>

        @include('partials.admin.transactions-table', [
            'rows' => $transactions,
            'summary' => $transactionsSummary,
            'paginator' => $transactionsPaginator,
            'anchor' => '#transactions',
        ])
    </section>
@endsection
