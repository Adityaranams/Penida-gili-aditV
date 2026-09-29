{{-- Figma node 1:10402 — admin Booking Report --}}
@extends('layouts.admin')

@section('title', 'Booking Report')

@section('admin-active', 'report')

@section('content')
    <x-admin.listing
        heading="Booking Report"
        subtitle="Comprehensive overview of fastboat reservations and performance."
        action="Download Report"
        :action-href="route('admin.report.export', request()->query())"
        :columns="['Passenger', 'Route', 'Date & Time', 'Amount', 'Status', 'Action']"
        :center-columns="['Route', 'Date & Time', 'Amount', 'Status', 'Action']"
        :paginator="$bookings"
        entity="bookings">

        {{-- Filters & Search (1:10428): same Search Route / Boat / Date / Filter bar as the Schedule console --}}
        <x-slot:toolbar>
            @include('partials.admin.route-filters', ['action' => route('admin.report'), 'filters' => $filters, 'vessels' => $vessels])
        </x-slot:toolbar>

        @forelse ($rows as $row)
            <tr class="border-b border-[rgba(192,199,211,0.2)] last:border-b-0">
                {{-- Passenger: initials avatar, name, email (1:10480) --}}
                <td class="px-[16px] py-[16px]">
                    <span class="flex items-center gap-[12px]">
                        <span class="flex size-[32px] shrink-0 items-center justify-center rounded-full bg-[#d5e2e9] text-[14px] font-bold leading-[20px] text-[#58646a]">{{ $row['initials'] }}</span>
                        <span class="min-w-0">
                            <span class="block truncate text-[16px] font-semibold leading-[24px] text-editorial-ink">{{ $row['name'] }}</span>
                            <span class="block truncate text-[12px] leading-[16px] text-editorial-body">{{ $row['email'] }}</span>
                        </span>
                    </span>
                </td>

                {{-- Route: from → to over the vessel (1:10488) --}}
                <td class="px-[16px] py-[18px] text-center">
                    <span class="inline-flex items-center gap-[8px] whitespace-nowrap text-[14px] leading-[20px] text-editorial-ink">
                        {{ $row['from'] }}
                        <img src="{{ asset('images/icons/admin/report/route-arrow.svg') }}" alt="to" class="size-[9.3px]">
                        {{ $row['to'] }}
                    </span>
                    <span class="block text-[12px] leading-[16px] text-editorial-body">{{ $row['vessel'] }}</span>
                </td>

                {{-- Date & time (1:10495) --}}
                <td class="px-[16px] py-[18px] text-center">
                    <span class="block whitespace-nowrap text-[14px] leading-[20px] text-editorial-ink">{{ $row['date'] }}</span>
                    <span class="block text-[12px] leading-[16px] text-editorial-body">{{ $row['time'] }}</span>
                </td>

                <td class="whitespace-nowrap px-[16px] py-[24px] text-center text-[16px] font-semibold leading-[24px] text-editorial-ink">{{ $row['amount'] }}</td>

                {{-- Status pill with tinted border (1:10503) --}}
                <td class="px-[16px] py-[24px] text-center">
                    <span @class([
                        'inline-block whitespace-nowrap rounded-full border px-[11px] py-[3px] text-[12px] leading-[16px]',
                        'border-[#bbf7d0] bg-[#dcfce7] text-[#166534]' => $row['status'] === 'Confirmed',
                        'border-[#fde68a] bg-[#fef9c3] text-[#a16207]' => $row['status'] === 'Pending',
                        'border-[#fecaca] bg-[#fee2e2] text-[#b91c1c]' => $row['status'] === 'Cancelled',
                    ])>{{ $row['status'] }}</span>
                </td>

                {{-- Action: kebab menu with the status changes (1:10506) --}}
                <td class="px-[16px] py-[16px]">
                    <details class="relative mx-auto w-fit" data-row-menu>
                        <summary aria-label="Actions for {{ $row['reference'] }}"
                                 class="flex size-[28px] cursor-pointer list-none items-center justify-center rounded-[8px] transition-colors duration-300 hover:bg-[#f1f4f6] [&::-webkit-details-marker]:hidden">
                            <img src="{{ asset('images/icons/admin/report/row-menu.svg') }}" alt="" class="h-[16px] w-[4px]">
                        </summary>
                        <div class="absolute right-0 z-10 mt-[4px] w-[180px] rounded-[8px] border border-[rgba(192,199,211,0.4)] bg-surface p-[6px] shadow-[0_8px_20px_rgba(0,0,0,0.08)]">
                            <span class="block px-[10px] py-[4px] text-[11px] font-semibold uppercase tracking-[0.5px] text-editorial-meta">{{ $row['reference'] }}</span>
                            @foreach ($statuses as $status)
                                @continue($status->value === $row['status_value'])
                                <form action="{{ route('admin.report.update', $row['reference']) }}" method="post">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $status->value }}">
                                    <button type="submit" class="block w-full rounded-[6px] px-[10px] py-[7px] text-left text-[13px] text-editorial-ink transition-colors hover:bg-[#f1f4f6]">
                                        Mark as {{ $status->label() }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </details>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-[16px] py-[32px] text-center text-[15px] text-editorial-body">No bookings match this filter.</td></tr>
        @endforelse
    </x-admin.listing>
@endsection
