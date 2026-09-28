{{-- Figma 1:9017 toolbar — Search Route, Boat, Date and a Filter button. Submits as GET so the URL is shareable.
     "Sanur to Nusa Penida" searches both ports; a single word matches either end. --}}
@props(['action', 'filters' => [], 'vessels' => []])

<form action="{{ $action }}" method="get" class="grid [&>*]:min-w-0 gap-[16px] md:grid-cols-[1fr_1fr_1fr_auto] md:items-end">
    <label class="block">
        <span class="block pb-[8px] text-[14px] text-editorial-body">Search Route</span>
        <span class="relative block">
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="e.g. Sanur to Nusa Penida"
                   class="w-full rounded-[8px] border border-[#c0c7d3] bg-surface py-[10px] pl-[41px] pr-[17px] text-[16px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial">
            <img src="{{ asset('images/icons/admin/table-search.svg') }}" alt=""
                 class="pointer-events-none absolute left-[12px] top-1/2 size-[18px] -translate-y-1/2">
        </span>
    </label>

    <label class="block">
        <span class="block pb-[8px] text-[14px] text-editorial-body">Boat</span>
        <span class="relative block">
            <select name="vessel"
                    class="w-full appearance-none rounded-[8px] border border-[#c0c7d3] bg-surface py-[11px] pl-[17px] pr-[44px] text-[16px] leading-[24px] text-editorial-ink focus:outline-2 focus:outline-editorial">
                <option value="">All Boat</option>
                @foreach ($vessels as $id => $name)
                    <option value="{{ $id }}" @selected((string) ($filters['vessel'] ?? '') === (string) $id)>{{ $name }}</option>
                @endforeach
            </select>
            <img src="{{ asset('images/icons/admin/chevron-down.svg') }}" alt=""
                 class="pointer-events-none absolute right-[12px] top-1/2 size-[20px] -translate-y-1/2">
        </span>
    </label>

    <label class="block">
        <span class="block pb-[8px] text-[14px] text-editorial-body">Date</span>
        <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
               class="w-full rounded-[8px] border border-[#c0c7d3] bg-surface px-[17px] py-[10px] text-[16px] text-editorial-ink focus:outline-2 focus:outline-editorial">
    </label>

    <span class="flex items-end gap-[12px]">
        <button type="submit"
                class="h-[46px] rounded-[8px] border border-[#c0c7d3] bg-surface px-[24px] text-[16px] text-editorial-ink transition-colors duration-300 hover:bg-[#f1f4f6]">
            Filter
        </button>

        {{-- Appears only once rows are ticked; posts to the bulk form outside this one. --}}
        @if ($bulkDelete ?? false)
            <x-admin.bulk-delete-button />
        @endif
    </span>
</form>
