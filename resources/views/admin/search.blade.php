{{-- Results for the console header's search box, grouped by module. Each row
     links to the screen that can act on the record, not to a read-only view. --}}
@extends('layouts.admin')

@section('title', 'Search')

@section('admin-active', 'search')

@section('content')
    <x-admin.form-page
        heading="Search"
        :subtitle="$term === '' ? 'Type in the box above to search boats, schedules, activities, hotels, articles, and bookings.' : $total.' '.\Illuminate\Support\Str::plural('result', $total).' for “'.$term.'”'">

        @if ($term !== '' && $groups === [])
            <div class="rounded-admin border border-[rgba(192,199,211,0.3)] bg-surface p-[40px] text-center shadow-sm">
                <p class="font-jakarta text-[18px] font-semibold leading-[26px] text-editorial-ink">Nothing matched “{{ $term }}”.</p>
                <p class="mt-[6px] font-jakarta text-[14px] leading-[20px] text-editorial-body">
                    Try a boat name, a port, a hotel, or a booking reference such as PG-ABC123.
                </p>
            </div>
        @endif

        <div class="flex max-w-[860px] flex-col gap-[24px]">
            @foreach ($groups as $group)
                <x-admin.form-section :title="$group['label']" icon="search-sm.svg">
                    <ul class="flex flex-col">
                        @foreach ($group['rows'] as $row)
                            <li class="border-b border-[rgba(192,199,211,0.3)] last:border-b-0">
                                <a href="{{ $row['url'] }}"
                                   class="flex items-center justify-between gap-[16px] px-[6px] py-[13px] transition-colors duration-300 hover:bg-[#f1f4f6]">
                                    <span class="min-w-0">
                                        <span class="block truncate font-jakarta text-[15px] font-semibold leading-[22px] text-editorial-ink">{{ $row['title'] }}</span>
                                        @if ($row['meta'])
                                            <span class="block truncate font-jakarta text-[13px] leading-[18px] text-editorial-body">{{ $row['meta'] }}</span>
                                        @endif
                                    </span>
                                    <span aria-hidden="true" class="shrink-0 font-jakarta text-[16px] text-editorial-meta">&rarr;</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-admin.form-section>
            @endforeach
        </div>
    </x-admin.form-page>
@endsection
