{{-- Figma node 1:9280 — admin Hotel --}}
@extends('layouts.admin')

@section('title', 'Hotel')

@section('admin-active', 'hotel')

@section('content')
    <x-admin.listing
        heading="Hotel"
        subtitle="Partner properties, room inventory and nightly rates available to island travellers."
        action="Add New Hotel"
        :action-href="route('admin.hotels.create')"
        :columns="['Hotel / Resort', 'Location', 'Rating', 'Room Types', 'Starting Price / Night', 'Status', 'Bookings (Mo)', 'Actions']"
        :paginator="$hotels"
        entity="hotels"
        panel-title="Registered Partner Accommodations"
        :panel-badge="$activeCount.' of '.$totalCount.' Active Listed'">

        <x-slot:panelAction>
            <a href="{{ route('admin.hotels', $filters) }}" class="flex items-center gap-[4px] text-[12px] font-semibold leading-[16px] text-editorial hover:underline">
                <img src="{{ asset('images/icons/admin/hotel/refresh.svg') }}" alt="" class="size-[9.3px]">
                Refresh Rates
            </a>
        </x-slot:panelAction>

        <x-slot:toolbar>
            @include('partials.admin.hotel-filters', [
                'action' => route('admin.hotels'),
                'filters' => $filters,
                'destinations' => $destinations,
                'statuses' => \App\Enums\ListingStatus::options(),
            ])
        </x-slot:toolbar>

        @forelse ($hotels as $hotel)
            @php
                $stats = $monthlyBookings->get($hotel->id);
                $stays = (int) ($stats->stays ?? 0);
                $occupancy = $hotel->room_stock ? min(100, (int) round(((int) ($stats->units ?? 0)) / $hotel->room_stock * 100)) : 0;
                // "12 Rooms (4 Suites)": total units, then the premium type in brackets.
                $premium = $hotel->rooms->first(fn ($room) => preg_match('/suite|villa/i', $room->name));
                $roomsLabel = $hotel->room_stock
                    ? (int) $hotel->room_stock.' Rooms'.($premium && $hotel->rooms->count() > 1 ? ' ('.$premium->stock.' '.\Illuminate\Support\Str::plural(preg_match('/villa/i', $premium->name) ? 'Villa' : 'Suite', $premium->stock).')' : '')
                    : 'No rooms yet';
            @endphp
            <tr class="border-b border-[rgba(192,199,211,0.2)] last:border-b-0">
                {{-- Hotel / Resort: thumbnail, name, partner label (1:9375) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="flex items-center gap-[14px]">
                        <img src="{{ $hotel->image_url }}" alt="" class="size-[56px] shrink-0 rounded-[12px] object-cover shadow-[0_0_0_1px_rgba(192,199,211,0.4),0_1px_2px_rgba(0,0,0,0.05)]">
                        <span class="flex w-[164px] flex-col gap-[2px]">
                            <a href="{{ route('admin.hotels.edit', $hotel) }}" class="text-[14px] font-bold leading-[20px] text-editorial-ink hover:text-editorial">{{ $hotel->name }}</a>
                            <span class="flex items-center gap-[4px] text-[12px] leading-[16px] text-editorial-body">
                                <img src="{{ asset('images/icons/admin/hotel/row-partner.svg') }}" alt="" class="h-[10.5px] w-[11px] shrink-0">
                                {{ $hotel->partner_label ?? $hotel->category }}
                            </span>
                        </span>
                    </span>
                </td>

                {{-- Location with pin (1:9384) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="flex w-[136px] items-start gap-[4px] text-[12px] leading-[16px] text-editorial-body">
                        <img src="{{ asset('images/icons/admin/hotel/row-pin.svg') }}" alt="" class="mt-[1px] h-[13.7px] w-[9.3px] shrink-0">
                        {{ $hotel->address }}
                    </span>
                </td>

                {{-- Rating: star, score, (reviews) (1:9389) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="flex items-center gap-[4px] whitespace-nowrap">
                        <img src="{{ asset('images/icons/admin/hotel/row-star.svg') }}" alt="" class="h-[11.1px] w-[11.7px]">
                        <span class="text-[12px] font-bold leading-[16px] text-editorial-ink">{{ number_format($hotel->rating, 1) }}</span>
                        <span class="text-[11px] leading-[20px] text-editorial-meta">({{ $hotel->review_count }})</span>
                    </span>
                </td>

                {{-- Room types pill (1:9396) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="inline-block whitespace-nowrap rounded-[6px] bg-[#e5e9eb] px-[8px] py-[2px] text-[12px] leading-[16px] text-editorial-ink">{{ $roomsLabel }}</span>
                </td>

                {{-- Starting price / night (1:9399) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="block whitespace-nowrap text-[14px] font-bold leading-[20px] text-editorial-ink">{{ $hotel->price_from ? 'IDR '.number_format($hotel->price_from, 0, ',', '.') : '—' }}</span>
                    <span class="block text-[11px] leading-[20px] text-editorial-meta">Excl. taxes</span>
                </td>

                {{-- Status pill with dot (1:9403) --}}
                <td class="px-[12px] py-[20px]"><x-admin.status :label="$hotel->status->label()" :tone="$hotel->status->tone()" /></td>

                {{-- Bookings (Mo): count over "% full" (1:9407) --}}
                <td class="px-[12px] py-[20px] text-center">
                    <span class="block text-[14px] font-bold leading-[20px] text-editorial-ink">{{ $stays }}</span>
                    <span class="block text-[10px] leading-[20px] text-editorial">{{ $occupancy }}% full</span>
                </td>

                {{-- View · Edit · Rooms · Delete (1:9411) --}}
                <td class="px-[16px] py-[20px]">
                    <span class="flex items-center justify-end gap-[6px]">
                        <a href="{{ route('hotels.show', $hotel) }}" target="_blank" rel="noopener" aria-label="View {{ $hotel->name }}"
                           class="flex size-[26px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ asset('images/icons/admin/hotel/action-view.svg') }}" alt="" class="size-[13.5px]">
                        </a>
                        <a href="{{ route('admin.hotels.edit', $hotel) }}" aria-label="Edit {{ $hotel->name }}"
                           class="flex size-[27px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ asset('images/icons/admin/hotel/action-edit.svg') }}" alt="" class="size-[15px]">
                        </a>
                        <a href="{{ route('admin.hotels.edit', $hotel) }}#rooms" aria-label="Manage rooms of {{ $hotel->name }}"
                           class="flex size-[26px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                            <img src="{{ asset('images/icons/admin/hotel/action-rooms.svg') }}" alt="" class="size-[13.5px]">
                        </a>
                        <form action="{{ route('admin.hotels.destroy', $hotel) }}" method="post" onsubmit="return confirm('Delete {{ addslashes($hotel->name) }}? This cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" aria-label="Delete {{ $hotel->name }}"
                                    class="flex size-[26px] items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#fee2e2]">
                                <img src="{{ asset('images/icons/admin/hotel/action-delete.svg') }}" alt="" class="size-[13.5px]">
                            </button>
                        </form>
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-[16px] py-[32px] text-center text-[15px] text-editorial-body">No hotels match this filter.</td></tr>
        @endforelse
    </x-admin.listing>
@endsection
