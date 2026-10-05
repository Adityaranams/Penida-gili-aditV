{{-- Figma node 1:7269 — admin Add New Schedule (also serves Edit).
     Left: Schedule Details + Pricing Configuration. Right: Publishing Settings, High Season Alert, Schedule Summary. --}}
@extends('layouts.admin')

@php
    $editing = $schedule->exists;
    $backHref = route('admin.schedules');
    $selectedDays = old('days', $schedule->days ?? $days);
    $currentRoute = old('route', $schedule->from_port_id && $schedule->to_port_id ? $schedule->from_port_id.'-'.$schedule->to_port_id : null);
    $isDraft = old('publish', $schedule->status === \App\Enums\ListingStatus::Draft ? 'draft' : 'publish') === 'draft';
    $time = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('H:i') : null;
@endphp

@section('title', $editing ? 'Edit Schedule' : 'Add New Schedule')

@section('admin-active', 'schedule')

@section('content')
    <x-admin.form-page
        :heading="$editing ? 'Edit Schedule' : 'Add New Schedule'"
        subtitle="Configure route timing, vessel assignment, and pricing for a new fastboat dispatch."
        :back-href="$backHref">

        <form action="{{ $editing ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}" method="post"
              class="grid [&>*]:min-w-0 gap-[24px] lg:grid-cols-[minmax(0,963fr)_minmax(0,464fr)]" data-schedule-form data-vessel-capacities="{{ json_encode($vesselCapacities) }}">
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="flex flex-col gap-[32px]">
                {{-- Schedule Details (1:7282) --}}
                <div class="overflow-hidden rounded-admin border border-[rgba(192,199,211,0.3)] bg-surface shadow-sm">
                    <div class="h-[4px] bg-editorial"></div>

                    <div class="p-[32px]">
                        <h2 class="flex items-center gap-[12px]">
                            <span class="flex size-[40px] items-center justify-center rounded-[10px] bg-editorial/10">
                                <img src="{{ asset('images/icons/admin/nav-schedule.svg') }}" alt="" class="size-[18px]">
                            </span>
                            <span class="font-jakarta text-[24px] font-semibold leading-[32px] text-editorial-ink">Schedule Details</span>
                        </h2>

                        <div class="mt-[24px] flex flex-col gap-[24px]">
                            <x-admin.field label="Route Segment" name="route" :value="$currentRoute" :options="$routes" placeholder="Select an established route..." :required="true" />

                            <x-admin.field label="Assigned Vessel" name="vessel_id" :value="$schedule->vessel_id" :options="$vesselOptions" placeholder="Select available vessel..." :required="true" />

                            <div class="grid [&>*]:min-w-0 gap-[24px] sm:grid-cols-2">
                                <x-admin.field label="Departure Time" name="departure_time" type="time" :value="$time($schedule->departure_time)" :required="true" />
                                <x-admin.field label="Est. Arrival Time" name="arrival_time" type="time" :value="$time($schedule->arrival_time)" :required="true" />
                            </div>

                            <fieldset class="pt-[16px]">
                                <legend class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">
                                    Operating Days (Frequency)
                                </legend>

                                <div class="mt-[12px] flex flex-wrap gap-[12px]">
                                    @foreach ($days as $day)
                                        <label class="flex items-center gap-[8px] rounded-[8px] bg-[#f1f4f6] px-[16px] py-[10px]">
                                            <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays, true))
                                                   class="size-[18px] rounded-[4px] accent-[#005ea1]">
                                            <span class="font-jakarta text-[15px] text-editorial-ink">{{ $day }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('days.*') <p class="mt-[8px] font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</p> @enderror
                            </fieldset>
                        </div>
                    </div>
                </div>

                {{-- Pricing Configuration (1:7392) --}}
                <div class="rounded-admin border border-[rgba(192,199,211,0.3)] bg-surface p-[32px] shadow-sm">
                    <h2 class="flex items-center gap-[12px]">
                        <span class="flex size-[40px] items-center justify-center rounded-[10px] bg-editorial/10">
                            <img src="{{ asset('images/icons/admin/kpi-revenue.svg') }}" alt="" class="size-[20px]">
                        </span>
                        <span class="font-jakarta text-[24px] font-semibold leading-[32px] text-editorial-ink">Pricing Configuration</span>
                    </h2>

                    <p class="mt-[24px] flex items-start gap-[12px] rounded-[8px] bg-[#f1f4f6] p-[16px] font-jakarta text-[14px] leading-[20px] text-editorial-body">
                        <img src="{{ asset('images/icons/order/info.svg') }}" alt="" class="mt-[1px] size-[20px] shrink-0">
                        Set base fares for this specific schedule. These prices will override default route pricing if defined here.
                    </p>

                    <div class="mt-[24px] grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2">
                        <x-admin.field label="Base Price (Local Pax)" name="price_adult" type="number" :value="$schedule->price_adult" placeholder="0" prefix="IDR" :required="true" />
                        <x-admin.field label="Base Price (Foreign Pax)" name="price_foreign" type="number" :value="$schedule->price_foreign" placeholder="0" prefix="IDR" />
                        <x-admin.field label="Child Price" name="price_child" type="number" :value="$schedule->price_child" placeholder="0" prefix="IDR" :required="true" />
                        <x-admin.field label="Child Price (Foreign Pax)" name="price_foreign_child" type="number" :value="$schedule->price_foreign_child" placeholder="0" prefix="IDR" />
                    </div>
                </div>
            </div>

            {{-- Side column (1:7447) --}}
            <aside class="flex flex-col gap-[16px] lg:sticky lg:top-8 lg:self-start">
                <x-admin.panel title="Publishing Settings" icon="nav-schedule.svg">
                    <x-admin.radio-cards name="publish" :options="$publishModes" :selected="$isDraft ? 'draft' : 'publish'" />
                </x-admin.panel>

                {{-- High Season Alert (1:7448) --}}
                <div class="relative overflow-hidden rounded-[12px] bg-[#005ea1] p-[24px] text-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]">
                    <span class="pointer-events-none absolute -right-[40px] -top-[40px] size-[160px] rounded-full bg-white/10 blur-[20px]" aria-hidden="true"></span>
                    <span class="pointer-events-none absolute -bottom-[32px] -left-[32px] size-[128px] rounded-full bg-[rgba(213,226,233,0.2)] blur-[12px]" aria-hidden="true"></span>

                    <div class="relative flex flex-col gap-[8px]">
                        <img src="{{ asset('images/icons/admin/season-sun.svg') }}" alt="" class="size-[20px]">
                        <h2 class="pt-[8px] font-jakarta text-[20px] leading-[30px]">High Season Alert</h2>
                        <p class="font-jakarta text-[14px] leading-[20px] text-white/90">
                            Current demand for Nusa Penida routes is 45% higher than average. Consider increasing frequency or deploying higher capacity vessels.
                        </p>
                        <a href="{{ route('admin.report') }}" class="flex items-center gap-[4px] pt-[10px] font-jakarta text-[14px] font-bold leading-[20px]">
                            View Analytics
                            <img src="{{ asset('images/icons/admin/link-arrow.svg') }}" alt="" class="size-[11px]">
                        </a>
                    </div>
                </div>

                {{-- Schedule Summary (1:7462) --}}
                <div class="rounded-[12px] border border-[rgba(192,199,211,0.2)] bg-surface p-[25px] drop-shadow-[0px_4px_10px_rgba(0,0,0,0.05)]">
                    <h2 class="border-b border-[rgba(192,199,211,0.3)] pb-[17px] font-jakarta text-[18px] leading-[27px] text-editorial-ink">Schedule Summary</h2>

                    <dl class="flex flex-col gap-[16px] pt-[16px] font-jakarta">
                        <div class="flex items-center justify-between">
                            <dt class="text-[14px] leading-[20px] text-editorial-body">Status</dt>
                            <dd data-summary-status
                                @class(['rounded-full px-[10px] py-[4px] text-[12px] leading-[16px]', 'bg-[#ebeef0] text-editorial-ink' => $isDraft, 'bg-[#dcfce7] text-[#166534]' => ! $isDraft])>
                                {{ $isDraft ? 'Draft' : 'Active' }}
                            </dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-[14px] leading-[20px] text-editorial-body">Total Capacity</dt>
                            <dd data-summary-capacity class="text-[16px] leading-[24px] text-editorial-ink">{{ $schedule->vessel ? $schedule->vessel->capacity.' pax' : '-- pax' }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-[14px] leading-[20px] text-editorial-body">Est. Duration</dt>
                            <dd data-summary-duration class="text-[16px] leading-[24px] text-editorial-ink">{{ $editing ? $schedule->duration_minutes.' mins' : '-- mins' }}</dd>
                        </div>
                    </dl>

                    <div class="flex flex-col gap-[12px] pt-[32px]">
                        <button type="submit"
                                class="w-full rounded-[8px] bg-editorial px-[16px] py-[14px] font-jakarta text-[16px] leading-[24px] text-white
                                       transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                            Publish Schedule
                        </button>
                        <a href="{{ $backHref }}"
                           class="block rounded-[8px] border border-[#c0c7d3] bg-[#f7fafc] px-[17px] py-[15px] text-center font-jakarta text-[16px] leading-[24px] text-editorial-ink
                                  transition-colors duration-300 hover:bg-[#f1f4f6]">
                            Cancel
                        </a>
                    </div>
                </div>
            </aside>
        </form>
    </x-admin.form-page>
@endsection
