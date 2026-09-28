{{-- Figma 1:9996 — activity toolbar: search left; Category, Status and Sort pills plus a reset button on the right.
     Selects submit on change, the search on Enter; GET keeps the URL shareable. --}}
@props(['action', 'filters' => [], 'categories' => [], 'statuses' => [], 'sorts' => []])

@php
    $pill = 'appearance-none rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] py-[7px] pl-[13px] pr-[36px] text-[14px] leading-[20px] text-editorial-ink focus:outline-2 focus:outline-editorial';
    $chevron = 'pointer-events-none absolute right-[8px] top-1/2 size-[21px] -translate-y-1/2';
@endphp

<form action="{{ $action }}" method="get" class="flex flex-wrap items-center justify-between gap-[16px]">
    <label class="relative block w-[803px] max-w-full">
        <span class="sr-only">Search</span>
        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by title, location or vendor..."
               class="w-full rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] py-[10px] pl-[41px] pr-[13px] text-[14px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial">
        <img src="{{ asset('images/icons/admin/table-search.svg') }}" alt="" class="pointer-events-none absolute left-[12px] top-1/2 size-[18px] -translate-y-1/2">
    </label>

    <div class="flex flex-wrap items-center gap-[11px]">
        {{-- Category --}}
        <label class="relative block">
            <span class="sr-only">Filter by category</span>
            <img src="{{ asset('images/icons/admin/activity/filter-category.svg') }}" alt="" class="pointer-events-none absolute left-[13px] top-1/2 h-[13.3px] w-[12.7px] -translate-y-1/2">
            <select name="category" onchange="this.form.requestSubmit()" class="{{ $pill }} pl-[34px]">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <img src="{{ asset('images/icons/admin/activity/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
        </label>

        {{-- Status --}}
        <label class="relative block">
            <span class="sr-only">Filter by status</span>
            <img src="{{ asset('images/icons/admin/activity/filter-status.svg') }}" alt="" class="pointer-events-none absolute left-[13px] top-1/2 h-[8px] w-[14.7px] -translate-y-1/2">
            <select name="status" onchange="this.form.requestSubmit()" class="{{ $pill }} pl-[36px]">
                <option value="">Status: All</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>Status: {{ $label }}</option>
                @endforeach
            </select>
            <img src="{{ asset('images/icons/admin/activity/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
        </label>

        {{-- Sort --}}
        <label class="relative block">
            <span class="sr-only">Sort</span>
            <img src="{{ asset('images/icons/admin/activity/filter-sort.svg') }}" alt="" class="pointer-events-none absolute left-[13px] top-1/2 h-[8px] w-[12px] -translate-y-1/2">
            <select name="sort" onchange="this.form.requestSubmit()" class="{{ $pill }} pl-[33px]">
                @foreach ($sorts as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['sort'] ?? array_key_first($sorts)) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <img src="{{ asset('images/icons/admin/activity/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
        </label>

        {{-- Appears only once rows are ticked; posts to the bulk form outside this one. --}}
        @if ($bulkDelete ?? false)
            <x-admin.bulk-delete-button class="h-[36px] px-[16px] text-[14px]" />
        @endif

        {{-- Reset --}}
        <a href="{{ $action }}" aria-label="Reset filters"
           class="flex size-[29px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
            <img src="{{ asset('images/icons/admin/activity/filter-reset.svg') }}" alt="" class="size-[13.3px]">
        </a>
    </div>
</form>
