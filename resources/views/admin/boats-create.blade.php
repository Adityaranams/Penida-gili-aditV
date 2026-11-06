{{-- Figma node 1:7132 — admin Add New Boat (also serves Edit).
     Publishing Settings sits above the form, as on the hotel/activity editors. --}}
@extends('layouts.admin')

@php
    $editing = $vessel->exists;
    $backHref = route('admin.boats');
    $submitLabel = 'Save';
    $isDraft = $vessel->status === \App\Enums\ListingStatus::Draft;
@endphp

@section('title', $editing ? 'Edit '.$vessel->name : 'Add New Boat')

@section('admin-active', 'boat')

@section('content')
    <x-admin.form-page
        :heading="$editing ? 'Edit Boat' : 'Add New Boat'"
        :subtitle="$editing ? 'Update vessel details, status and facilities.' : 'Register a new boat into the active Boat booking.'"
        :back-href="$backHref">

        <form action="{{ $editing ? route('admin.boats.update', $vessel) : route('admin.boats.store') }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-[32px]">
            @csrf
            @if ($editing) @method('PUT') @endif

            {{-- Publishing Settings — same radio cards as the hotel/activity editors --}}
            <x-admin.form-section title="Publishing Settings" icon="nav-schedule.svg">
                <x-admin.radio-cards name="publish" :options="$publishModes" :selected="$isDraft ? 'draft' : 'publish'" />
            </x-admin.form-section>

            {{-- Figma node 1:7159 — Boat Details --}}
            <x-admin.form-section title="Boat Details" icon="form-vessel.svg">
                <div class="grid [&>*]:min-w-0 gap-[24px] md:grid-cols-2">
                    <div class="md:col-span-2">
                        <x-admin.field label="Boat Name" name="name" :value="$vessel->name" placeholder="Boat Name" :required="true" />
                    </div>

                    <x-admin.field label="Vessel Type" name="type" :value="$vessel->type" :options="$types" placeholder="Select Type" :required="true" />

                    <x-admin.field label="Passenger Capacity (Pax)" name="capacity" type="number" :value="$vessel->capacity ?? 100" :required="true" />

                    <x-admin.field label="Rating" name="rating" type="number" step="0.1" min="1" max="5" :value="$vessel->rating" placeholder="4.8"
                                   help="Shown on the catalogue card. Leave empty to hide the stars." />

                    <div class="md:col-span-2">
                        <x-admin.field label="Short Description" name="description" type="textarea" :value="$vessel->description"
                                       placeholder="One or two sentences about this boat, shown on its card."
                                       help="Belongs to this boat only — it is never taken from the operator." :limit="1000" />
                    </div>

                    {{-- Operational state; "Save as Draft" above overrides it while the boat is unpublished. --}}
                    <x-admin.field label="Initial Status" name="status" :value="$isDraft ? 'active' : $vessel->status?->value" :options="$operationalStatuses" />
                </div>
            </x-admin.form-section>

            {{-- Figma node 1:7206 — Boat Photos, split into the card image and the detail gallery --}}
            <x-admin.form-section title="Cover Photo" icon="form-camera.svg">
                <p class="mb-[16px] font-jakarta text-[15px] leading-[24px] text-editorial-body">
                    Shown on the boat card in the catalogue. One photo, landscape works best.
                </p>

                <x-admin.uploader name="cover" :multiple="false" hint="PNG or JPG, one photo (max 4MB).">
                    @if ($vessel->image)
                        <x-admin.gallery-preview :cover="$vessel->image" folder="boats" />
                    @endif
                </x-admin.uploader>
            </x-admin.form-section>

            <x-admin.form-section title="Boat Gallery" icon="form-camera.svg">
                <p class="mb-[16px] font-jakarta text-[15px] leading-[24px] text-editorial-body">
                    Extra photos shown inside the boat page. Up to 8; the cover above stays separate.
                </p>

                <x-admin.uploader name="photos" hint="PNG or JPG, up to 8 photos. Pick several at once, or add them one by one.">
                    @if ($vessel->gallery)
                        <p class="mt-[20px] font-jakarta text-[14px] leading-[22px] text-editorial-meta">
                            Already in the gallery &mdash; new photos are added to these. Tick one to remove it when you save.
                        </p>

                        <x-admin.gallery-preview :items="$vessel->gallery" folder="boats" remove="remove_photos" />
                    @endif
                </x-admin.uploader>
            </x-admin.form-section>

            {{-- Figma node 1:7222 — Boat Facilities --}}
            <x-admin.form-section title="Guest Testimonials" icon="form-check.svg">
                @include('partials.admin.boat-testimonials')
            </x-admin.form-section>

            <x-admin.form-section title="Boat Facilities" icon="form-check.svg">
                <div class="grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($facilities as $facility)
                        <label class="flex items-center gap-[12px]">
                            <input type="checkbox" name="facilities[]" value="{{ $facility['label'] }}"
                                   @checked($facility['checked'])
                                   class="size-[20px] rounded-[4px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] accent-[#005ea1]">
                            <span class="font-jakarta text-[16px] leading-[24px] text-editorial-ink">{{ $facility['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </x-admin.form-section>

            {{-- Free-text gear, unlike the fixed Facilities tick-list above: what a
                 boat carries and how the operator words it differs per vessel. --}}
            <x-admin.form-section title="Advanced Safety Equipment" icon="form-check.svg">
                <div class="flex flex-col gap-[8px]" data-keywords data-chip-class="bg-editorial/10 text-editorial">
                    <input type="hidden" name="safety_equipment"
                           value="{{ implode(', ', old('safety_equipment') ? array_map('trim', explode(',', old('safety_equipment'))) : ($vessel->safety_equipment ?? [])) }}">
                    <div class="flex flex-wrap items-center gap-[8px] rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] px-[12px] py-[8px] focus-within:border-editorial">
                        <span class="contents" data-keywords-list></span>
                        <input type="text" placeholder="+ Add equipment..." autocomplete="off"
                               class="min-w-[180px] flex-1 bg-transparent py-[5px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-none">
                    </div>
                    <span class="font-jakarta text-[13px] leading-[18px] text-editorial-body">
                        Listed on the boat page. For example: Life Rafts, EPIRB Beacon, Fire Suppression System, First Aid Kit.
                    </span>
                    @error('safety_equipment') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                </div>
            </x-admin.form-section>

            {{-- Figma node 1:7261 --}}
            <div class="flex items-center justify-end gap-[16px] border-t border-[rgba(192,199,211,0.3)] pt-[17px]">
                <a href="{{ $backHref }}"
                   class="rounded-[8px] border border-[#717782] px-[25px] py-[13px] font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink
                          transition-colors duration-300 hover:bg-[#f1f4f6]">
                    Cancel
                </a>

                <button type="submit"
                        class="flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[12px] font-jakarta text-[14px] font-bold leading-[20px] tracking-[0.7px] text-white shadow-sm
                               transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                    <img src="{{ asset('images/icons/admin/form-save.svg') }}" alt="" class="size-[18px]">
                    {{ $submitLabel }}
                </button>
            </div>
        </form>
    </x-admin.form-page>
@endsection
