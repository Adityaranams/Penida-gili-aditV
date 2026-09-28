{{-- Figma 1:9663 — article "Filter & Search Toolbar Card": search left; Author / Category / Status
     labelled pills plus reset on the right. --}}
@props(['action', 'filters' => [], 'authors' => [], 'categories' => [], 'statuses' => []])

@php
    $pill = 'flex cursor-pointer items-center gap-[8px] rounded-[12px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] px-[13px] py-[9px]'
        .' transition-[border-color,background-color,box-shadow] duration-200 ease-smooth hover:border-editorial/50 hover:bg-[#eef4f8]'
        .' focus-within:border-editorial focus-within:bg-surface focus-within:shadow-[0_0_0_3px_rgba(0,94,161,0.12)]';
    $select = 'cursor-pointer appearance-none bg-transparent pr-[29px] font-jakarta text-[14px] font-semibold leading-[20px] text-editorial-ink focus:outline-none';
    $chevron = 'pointer-events-none absolute right-0 top-1/2 size-[21px] -translate-y-1/2 transition-transform duration-200 ease-smooth';
@endphp

<form action="{{ $action }}" method="get">
    <div class="flex flex-wrap items-center justify-between gap-[16px]">
        <label class="relative block w-[770px] max-w-full">
            <span class="sr-only">Search</span>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search by title, keyword, or author..."
                   class="w-full rounded-[12px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] py-[10px] pl-[41px] pr-[17px] font-jakarta text-[14px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial">
            <img src="{{ asset('images/icons/admin/article/filter-search.svg') }}" alt="" class="pointer-events-none absolute left-[14px] top-1/2 size-[13.3px] -translate-y-1/2">
        </label>

        <div class="flex flex-wrap items-center gap-[9px]">
            {{-- Author --}}
            <label class="{{ $pill }}">
                <span class="font-jakarta text-[12px] font-medium leading-[16px] text-[#525c6f]">Author:</span>
                <span class="relative block">
                    <select name="author" onchange="this.form.requestSubmit()" class="{{ $select }}">
                        <option value="">All Authors</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author }}" @selected(($filters['author'] ?? '') === $author)>{{ $author }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/admin/article/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
                </span>
            </label>

            {{-- Category --}}
            <label class="{{ $pill }}">
                <span class="font-jakarta text-[12px] font-medium leading-[16px] text-[#525c6f]">Category:</span>
                <span class="relative block">
                    <select name="category" onchange="this.form.requestSubmit()" class="{{ $select }}">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/admin/article/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
                </span>
            </label>

            {{-- Status --}}
            <label class="{{ $pill }}">
                <span class="font-jakarta text-[12px] font-medium leading-[16px] text-[#525c6f]">Status:</span>
                <span class="relative block">
                    <select name="status" onchange="this.form.requestSubmit()" class="{{ $select }}">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <img src="{{ asset('images/icons/admin/article/filter-chevron.svg') }}" alt="" class="{{ $chevron }}">
                </span>
            </label>

            {{-- Appears only once rows are ticked; posts to the bulk form outside this one. --}}
            @if ($bulkDelete ?? false)
                <x-admin.bulk-delete-button class="h-[36px] rounded-[12px] px-[16px] text-[14px]" />
            @endif

            {{-- Reset --}}
            <a href="{{ $action }}" aria-label="Reset filters"
               class="flex size-[29px] items-center justify-center rounded-[12px] transition-[background-color,transform] duration-200 ease-smooth
                      hover:bg-[#f1f4f6] hover:-rotate-45 active:scale-95">
                <img src="{{ asset('images/icons/admin/article/filter-reset.svg') }}" alt="" class="h-[15.4px] w-[13.3px]">
            </a>
        </div>
    </div>

</form>
