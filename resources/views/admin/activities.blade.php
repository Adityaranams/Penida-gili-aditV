{{-- Figma node 1:9970 — admin Activity --}}
@extends('layouts.admin')

@section('title', 'Activity')

@section('admin-active', 'activity')

@section('content')
    <x-admin.bulk-delete :action="route('admin.activities.bulk-destroy')" noun="activities" />

    <x-admin.listing
        heading="Activity"
        subtitle="Manage Balinese cultural tours, day passes, watersports, and photography packages."
        action="Add New Activity"
        :action-href="route('admin.activities.create')"
        :columns="['Activity Details', 'Category', 'Location', 'Price / Pax', 'Status', 'Total Sold', 'Actions']"
        :center-columns="['Category', 'Location', 'Price / Pax', 'Status', 'Total Sold', 'Actions']"
        :paginator="$activities"
        entity="activities"
        :selectable="true">

        <x-slot:toolbar>
            @include('partials.admin.activity-filters', [
                'action' => route('admin.activities'),
                'filters' => $filters,
                'categories' => $categories,
                'statuses' => \App\Enums\ListingStatus::options(),
                'sorts' => $sorts,
                'bulkDelete' => true,
            ])
        </x-slot:toolbar>

        @forelse ($activities as $activity)
            <tr class="border-b border-[rgba(192,199,211,0.2)] last:border-b-0">
                <x-admin.bulk-checkbox :value="$activity->id" :label="$activity->name" />

                {{-- Activity details: thumbnail, name, rating • hours • badge/reviews (1:10054) --}}
                <td class="px-[16px] py-[16px]">
                    <span class="flex items-center gap-[14px]">
                        <img src="{{ $activity->image_url }}" alt="" class="size-[56px] shrink-0 rounded-[8px] object-cover">
                        <span class="min-w-0">
                            <a href="{{ route('admin.activities.edit', $activity) }}" class="block truncate text-[14px] font-semibold leading-[20px] text-editorial-ink hover:text-editorial">{{ $activity->name }}</a>
                            <span class="mt-[4px] flex flex-wrap items-center gap-[8px] text-[12px] leading-[16px] text-editorial-body">
                                <span class="flex items-center gap-[2px]">
                                    <img src="{{ asset('images/icons/admin/activity/star.svg') }}" alt="" class="h-[11px] w-[11.7px]">
                                    {{ $activity->rating }}
                                </span>
                                @if ($activity->hours_label)
                                    <span class="text-editorial-meta">&bull;</span>
                                    <span>{{ $activity->hours_label }}</span>
                                @endif
                                <span class="text-editorial-meta">&bull;</span>
                                @if ($activity->badge)
                                    <span class="rounded-[4px] bg-[#d2e4ff] px-[8px] py-[2px] font-semibold text-[#005ea1]">{{ $activity->badge }}</span>
                                @else
                                    <span>{{ $activity->review_count }} ulasan</span>
                                @endif
                            </span>
                        </span>
                    </span>
                </td>

                {{-- Category pill with glyph (1:10075) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="inline-flex max-w-[140px] items-center gap-[6px] rounded-[6px] bg-[#ebeef0] px-[10px] py-[4px] text-[12px] font-medium leading-[16px] text-editorial-body">
                        <img src="{{ asset('images/icons/admin/activity/'.$activity->category_icon) }}" alt="" class="size-[12px] shrink-0 object-contain">
                        {{ $activity->category }}
                    </span>
                </td>

                {{-- Location with pin (1:10079) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="inline-flex max-w-[140px] items-center gap-[6px] text-[12px] leading-[16px] text-editorial-body">
                        <img src="{{ asset('images/icons/admin/activity/pin.svg') }}" alt="" class="h-[11.7px] w-[9.3px] shrink-0">
                        {{ $activity->place_label ?: $activity->location }}
                    </span>
                </td>

                {{-- Price: brand-blue figure, then "/ pax" or the struck-through old price (1:10084 / 1:10140) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="block text-[14px] font-semibold leading-[20px] text-editorial">{{ $activity->price_label }}</span>
                    @if ($activity->price_was_label)
                        <span class="block text-[12px] leading-[16px] text-editorial-meta line-through">{{ $activity->price_was_label }}</span>
                    @else
                        <span class="block text-[12px] leading-[16px] text-editorial-body">/ pax</span>
                    @endif
                </td>

                <td class="px-[16px] py-[16px] text-center"><x-admin.status :label="$activity->status->label()" :tone="$activity->status->tone()" /></td>

                {{-- Total sold: figure over "pax" (1:10093) --}}
                <td class="px-[16px] py-[16px] text-center">
                    <span class="block text-[14px] leading-[20px] text-editorial-ink">{{ number_format($activity->sold_pax ?? 0) }}</span>
                    <span class="block text-[12px] leading-[16px] text-editorial-body">pax</span>
                </td>

                {{-- Edit · Delete (1:10096) --}}
                <td class="px-[16px] py-[16px]">
                    <span class="flex items-center justify-center gap-[6px]">
                        <a href="{{ route('admin.activities.edit', $activity) }}" aria-label="Edit {{ $activity->name }}"
                           class="flex size-[28px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ asset('images/icons/admin/activity/action-edit.svg') }}" alt="" class="size-[13.5px]">
                        </a>
                        <form action="{{ route('admin.activities.destroy', $activity) }}" method="post" onsubmit="return confirm('Delete {{ addslashes($activity->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Delete {{ $activity->name }}"
                                    class="flex size-[28px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#fee2e2]">
                                <img src="{{ asset('images/icons/admin/activity/action-delete-red.svg') }}" alt="" class="h-[13.5px] w-[12px]">
                            </button>
                        </form>
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-[16px] py-[32px] text-center text-[15px] text-editorial-body">No activities match this filter.</td></tr>
        @endforelse
    </x-admin.listing>
@endsection
