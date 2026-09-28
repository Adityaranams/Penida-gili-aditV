{{-- Figma node 1:6901 — admin Boat --}}
@extends('layouts.admin')

@section('title', 'Boat')

@section('admin-active', 'boat')

@section('content')
    <x-admin.bulk-delete :action="route('admin.boats.bulk-destroy')" noun="boats" />

    <x-admin.listing
        heading="Boat"
        subtitle="Overview of all vessels, their operator and capacity for Boat Booking."
        action="Add New Boat"
        :action-href="route('admin.boats.create')"
        :columns="['Boat Name', 'Type', 'Capacity', 'Status', 'Actions']"
        :center-columns="['Status', 'Actions']"
        :paginator="$boats"
        :selectable="true">

        <x-slot:filters>
            <x-admin.filters :action="route('admin.boats')" :filters="$filters" :statuses="\App\Enums\ListingStatus::options()" placeholder="Search by boat name or ID...">
                <x-admin.bulk-delete-button class="h-[42px]" />
            </x-admin.filters>
        </x-slot:filters>

        @forelse ($boats as $boat)
            <tr class="border-b border-[rgba(192,199,211,0.2)] last:border-b-0">
                <x-admin.bulk-checkbox :value="$boat->id" :label="$boat->name" />

                <td class="px-[16px] py-[18px]">
                    <span class="flex items-center gap-[12px]">
                        {{-- Active vessels get the blue boat tile, everything else the orange maintenance tile (Figma 1:6959 / 1:7067). --}}
                        @if ($boat->status === \App\Enums\ListingStatus::Active)
                            <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[8px] bg-editorial/10">
                                <img src="{{ asset('images/icons/admin/row-boat.svg') }}" alt="" class="h-[20px] w-[18.4px]">
                            </span>
                        @else
                            <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[8px] bg-[#ffedd5]">
                                <img src="{{ asset('images/icons/admin/row-boat-inactive.svg') }}" alt="" class="size-[18px]">
                            </span>
                        @endif
                        <span>
                            <span class="block text-[16px] font-semibold leading-[24px] text-editorial-ink">{{ $boat->name }}</span>
                            <span class="block text-[14px] leading-[20px] text-editorial-meta">ID: {{ $boat->code }}</span>
                        </span>
                    </span>
                </td>
                <td class="px-[16px] py-[18px] text-[16px] leading-[24px] text-editorial-body">{{ $boat->type }}</td>
                <td class="px-[16px] py-[18px] text-[16px] leading-[24px] text-editorial-body">{{ $boat->capacity }} Pax</td>
                <td class="px-[16px] py-[18px] text-center">
                    <x-admin.status :label="$boat->status->label()" :tone="$boat->status->tone()" />
                </td>
                <td class="px-[16px] py-[18px]">
                    <x-admin.row-actions align="center" :label="$boat->name" :edit-href="route('admin.boats.edit', $boat)" :delete-action="route('admin.boats.destroy', $boat)" />
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-[16px] py-[32px] text-center text-[15px] text-editorial-body">No vessels match this filter.</td></tr>
        @endforelse
    </x-admin.listing>
@endsection
