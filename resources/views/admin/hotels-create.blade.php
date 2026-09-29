{{-- Figma node 1:7501 — admin Add New Hotel (also serves Edit). Figma is drawn at 1.5×; sizes here are ÷1.5. --}}
@extends('layouts.admin')

@php
    $editing = $hotel->exists;
    $backHref = route('admin.hotels');
    $roomRows = array_values($rooms);
    $money = fn ($v) => $v ? number_format((int) $v, 0, ',', '.') : '';
    $photoCount = ($hotel->image ? 1 : 0) + count($hotel->gallery ?? []);
    $label = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-body';
    $input = 'w-full rounded-[8px] border border-[#c0c7d3] bg-surface px-[13px] py-[9px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial';
    $icon = fn ($file) => asset('images/icons/admin/hotel/'.$file);
    $chip = 'flex w-fit items-center gap-[8px] rounded-[8px] bg-[#f1f4f6] p-[10px] font-jakarta text-[12px] font-medium leading-[16px] text-editorial-ink';
@endphp

@section('title', $editing ? 'Edit '.$hotel->name : 'Add New Hotel')

@section('admin-active', 'hotel')

@section('content')
    <x-admin.form-page
        :heading="$editing ? 'Edit Hotel' : 'Add New Hotel'"
        subtitle="Register island partner accommodations, room categories, and instant fastboat transfer bundles"
        :back-href="$backHref">

        {{-- Header actions (1:7512 / 1:7516) --}}
        <x-slot:actions>
            <button type="submit" form="hotel-form" name="submit_as" value="draft"
                    class="flex items-center gap-[8px] rounded-[8px] border border-[#c0c7d3] bg-surface px-[21px] py-[11px] font-jakarta text-[14px] font-semibold text-editorial-ink
                           transition-colors duration-300 hover:bg-[#f1f4f6]">
                <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-editorial" />
                Save Draft
            </button>
            <button type="submit" form="hotel-form" name="submit_as" value="publish"
                    class="flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[10px] font-jakarta text-[14px] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)]
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                <x-admin.icon name="nav-hotel.svg" class="size-[12px] bg-white" />
                Publish Hotel Listing
            </button>
        </x-slot:actions>

        <form id="hotel-form" action="{{ $editing ? route('admin.hotels.update', $hotel) : route('admin.hotels.store') }}" method="post" enctype="multipart/form-data"
              class="grid [&>*]:min-w-0 gap-[24px] lg:grid-cols-[minmax(0,642fr)_minmax(0,309fr)]" data-hotel-form>
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="flex flex-col gap-[24px]">
                {{-- 1. Property Overview (1:7523) --}}
                <section class="rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-surface p-[33px] shadow-[0_4px_10px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center gap-[12px] border-b border-[#ebeef0] pb-[17px]">
                        <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[8px] bg-[rgba(210,228,255,0.5)]">
                            <img src="{{ $icon('editor-overview.svg') }}" alt="" class="h-[18px] w-[20px]">
                        </span>
                        <div>
                            <h2 class="font-jakarta text-[18px] font-semibold leading-[28px] text-editorial-ink">Property Overview</h2>
                            <p class="font-jakarta text-[12px] leading-[16px] text-editorial-body">Core identity, hospitality classification, and descriptive narrative</p>
                        </div>
                    </div>

                    <div class="mt-[24px] flex flex-col gap-[20px]">
                        <div class="flex flex-col gap-[8px]">
                            <label for="hotel-name" class="{{ $label }}">Property Name <span class="text-[#ba1a1a]">*</span></label>
                            <span class="relative block">
                                <img src="{{ $icon('field-hotel.svg') }}" alt="" class="pointer-events-none absolute left-[14px] top-1/2 h-[11px] w-[11px] -translate-y-1/2">
                                <input id="hotel-name" name="name" value="{{ old('name', $hotel->name) }}" required placeholder="The Nusa Penida Resort &amp; Spa"
                                       class="{{ $input }} py-[11px] pl-[45px] pr-[17px]">
                            </span>
                            @error('name') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2">
                            <div class="flex flex-col gap-[8px]">
                                <label for="hotel-category" class="{{ $label }}">Accommodation Type <span class="text-[#ba1a1a]">*</span></label>
                                <span class="relative block">
                                    <select id="hotel-category" name="category" class="{{ $input }} appearance-none py-[11px] pl-[15px] pr-[40px]">
                                        @foreach ($categories as $category)
                                            <option value="{{ $category }}" @selected(old('category', $hotel->category) === $category)>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                    <img src="{{ $icon('field-chevron.svg') }}" alt="" class="pointer-events-none absolute right-[9px] top-1/2 size-[21px] -translate-y-1/2">
                                </span>
                            </div>

                            <fieldset class="flex flex-col gap-[8px]">
                                <legend class="{{ $label }} mb-[8px]">Star Rating <span class="text-[#ba1a1a]">*</span></legend>
                                <div class="flex gap-[8px]">
                                    @foreach ([3, 4, 5] as $star)
                                        <label class="flex-1 cursor-pointer">
                                            <input type="radio" name="stars" value="{{ $star }}" @checked((int) old('stars', $hotel->stars ?? 5) === $star) class="peer sr-only">
                                            <span class="flex items-center justify-center gap-[6px] rounded-[8px] border border-[rgba(192,199,211,0.8)] px-[13px] py-[9px] font-jakarta text-[12px] font-bold text-editorial-ink
                                                         peer-checked:border-editorial peer-checked:bg-[rgba(210,228,255,0.4)] peer-checked:text-editorial">
                                                <img src="{{ $icon('field-star.svg') }}" alt="" class="h-[11px] w-[11.6px]">
                                                {{ $star }} Star
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        {{-- Rich Text Property Description (1:7572). The contenteditable is what the
                             admin types in; it mirrors into the textarea that posts, so the field still
                             works (as plain text) when JavaScript is off. --}}
                        @php $rte = 'flex items-center justify-center rounded-[4px] p-[6px] transition-colors hover:bg-[#e5e9eb] aria-pressed:bg-editorial/10 aria-pressed:text-editorial'; @endphp
                        <div class="flex flex-col gap-[8px]" data-editor>
                            <label for="hotel-description" class="{{ $label }}">Property Description</label>
                            <div class="flex items-center gap-[4px] rounded-t-[8px] border border-[#c0c7d3] bg-[#f1f4f6] px-[13px] py-[9px] font-jakarta text-[12px] text-editorial-body">
                                <button type="button" data-editor-command="bold" title="Bold" aria-label="Bold" class="{{ $rte }} font-bold">B</button>
                                <button type="button" data-editor-command="italic" title="Italic" aria-label="Italic" class="{{ $rte }} italic">I</button>
                                <button type="button" data-editor-command="underline" title="Underline" aria-label="Underline" class="{{ $rte }} underline">U</button>
                                <span class="mx-[4px] h-[16px] w-px bg-[#c0c7d3]"></span>
                                <button type="button" data-editor-command="insertUnorderedList" title="Bulleted list" aria-label="Bulleted list" class="{{ $rte }}">
                                    <img src="{{ $icon('rte-list.svg') }}" alt="" class="h-[9.3px] w-[10.5px]">
                                </button>
                                <button type="button" data-editor-command="insertOrderedList" title="Numbered list" aria-label="Numbered list" class="{{ $rte }}">
                                    <img src="{{ $icon('rte-ol.svg') }}" alt="" class="h-[11.6px] w-[10.5px]">
                                </button>
                            </div>

                            <div data-editor-surface contenteditable="true" role="textbox" aria-multiline="true" aria-label="Property description" hidden
                                 class="rich-text -mt-[8px] min-h-[150px] w-full rounded-b-[8px] border border-t-0 border-[#c0c7d3] bg-surface px-[17px] py-[16px] font-jakarta text-[14px] leading-[22px] text-editorial-ink focus:outline-none">{!! \App\Support\RichText::clean(old('description', $hotel->description)) !!}</div>

                            <textarea id="hotel-description" name="description" rows="6" required data-editor-input
                                      placeholder="Perched along the dramatic cliff edges of Nusa Penida…"
                                      class="-mt-[8px] w-full resize-y rounded-b-[8px] border border-t-0 border-[#c0c7d3] bg-surface px-[17px] py-[16px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial">{{ old('description', $hotel->description) }}</textarea>
                            @error('description') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </section>

                {{-- 2. Room Categories & Inventory Manager (1:7599) --}}
                <section class="rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-surface p-[33px] shadow-[0_4px_10px_rgba(0,0,0,0.05)]" data-rooms>
                    <div class="flex items-center justify-between gap-[12px] border-b border-[#ebeef0] pb-[17px]">
                        <div class="flex items-center gap-[12px]">
                            <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[8px] bg-[rgba(210,228,255,0.5)]">
                                <img src="{{ $icon('editor-rooms.svg') }}" alt="" class="h-[14px] w-[20px]">
                            </span>
                            <div>
                                <h2 class="font-jakarta text-[18px] font-semibold leading-[28px] text-editorial-ink">Room Categories &amp; Inventory Manager</h2>
                                <p class="font-jakarta text-[12px] leading-[16px] text-editorial-body">Configure suite classifications, nightly rack rates, and live allotment</p>
                            </div>
                        </div>
                        <span class="shrink-0 rounded-full bg-[#d5e2e9] px-[10px] py-[4px] font-jakarta text-[12px] font-semibold leading-[16px] text-[#58646a]">
                            <span data-rooms-count>{{ count($roomRows) }}</span> Categories Active
                        </span>
                    </div>
                    @error('rooms') <p class="mt-[12px] font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</p> @enderror

                    <div class="mt-[24px] flex flex-col gap-[16px]" data-rooms-list>
                        @foreach ($roomRows as $i => $room)
                            @include('partials.admin.hotel-room-card', ['i' => $i, 'room' => $room, 'open' => $errors->has("rooms.$i.*") || empty($room['name']), 'money' => $money, 'icon' => $icon, 'input' => $input, 'label' => $label])
                        @endforeach
                    </div>

                    {{-- Button to Add Room (1:7709) --}}
                    <button type="button" data-room-add
                            class="mt-[16px] flex w-full items-center justify-center gap-[8px] rounded-[12px] border-2 border-dashed border-[rgba(0,94,161,0.4)] px-[2px] py-[14px] font-jakarta text-[14px] font-bold text-editorial
                                   transition-colors duration-300 hover:bg-[rgba(210,228,255,0.2)]">
                        <img src="{{ $icon('room-add.svg') }}" alt="" class="size-[15px]">
                        + Add Another Room Category
                    </button>

                    <template data-room-template>
                        @include('partials.admin.hotel-room-card', ['i' => '__INDEX__', 'room' => ['id' => null, 'name' => '', 'guests' => 2, 'bed' => '', 'size_label' => '', 'price_per_night' => '', 'stock' => 1], 'open' => true, 'money' => $money, 'icon' => $icon, 'input' => $input, 'label' => $label])
                    </template>
                </section>

                {{-- 3. Premium Hotel Amenities (1:7714) --}}
                <section class="rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-surface p-[33px] shadow-[0_4px_10px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center gap-[12px] border-b border-[#ebeef0] pb-[17px]">
                        <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[8px] bg-[rgba(210,228,255,0.5)]">
                            <img src="{{ $icon('editor-amenities.svg') }}" alt="" class="h-[15px] w-[20px]">
                        </span>
                        <div>
                            <h2 class="font-jakarta text-[18px] font-semibold leading-[28px] text-editorial-ink">Premium Hotel Amenities</h2>
                            <p class="font-jakarta text-[12px] leading-[16px] text-editorial-body">Select facility highlights that appear on booking search filters and passenger vouchers</p>
                        </div>
                    </div>

                    <div class="mt-[24px] grid [&>*]:min-w-0 gap-[12px] sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($amenities as $amenity)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity['label'] }}" @checked($amenity['checked']) class="peer sr-only">
                                <span class="flex h-full gap-[12px] rounded-[8px] border border-[rgba(192,199,211,0.6)] p-[15px] transition-colors duration-300
                                             peer-checked:border-editorial peer-checked:bg-[rgba(210,228,255,0.2)] peer-checked:[&>span:first-child]:bg-editorial peer-checked:[&>span:first-child>img]:opacity-100">
                                    <span class="flex size-[18px] shrink-0 items-center justify-center rounded-[4px] border border-[#c0c7d3] bg-surface">
                                        <img src="{{ $icon('amenity-check.svg') }}" alt="" class="size-[16px] opacity-0">
                                    </span>
                                    <span class="flex min-w-0 flex-col">
                                        <img src="{{ $icon($amenity['editorIcon']) }}" alt="" class="mb-[4px] h-[16px] w-fit max-w-[24px] object-contain object-left">
                                        <span class="font-jakarta text-[12px] font-bold leading-[16px] text-editorial-ink">{{ $amenity['label'] }}</span>
                                        <span class="font-jakarta text-[11px] leading-[24px] text-editorial-body">{{ $amenity['note'] }}</span>
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </section>

                {{-- 4. Photo Gallery & Room Images (1:7829) --}}
                <section class="rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-surface p-[33px] shadow-[0_4px_10px_rgba(0,0,0,0.05)]" data-gallery>
                    <div class="flex items-center justify-between gap-[12px] border-b border-[#ebeef0] pb-[17px]">
                        <div class="flex items-center gap-[12px]">
                            <span class="flex h-[40px] w-[36px] shrink-0 items-center justify-center rounded-[8px] bg-[rgba(210,228,255,0.5)]">
                                <img src="{{ $icon('editor-gallery.svg') }}" alt="" class="size-[20px]">
                            </span>
                            <div>
                                <h2 class="font-jakarta text-[18px] font-semibold leading-[28px] text-editorial-ink">Photo Gallery &amp; Room Images</h2>
                                <p class="font-jakarta text-[12px] leading-[16px] text-editorial-body">High-resolution imagery for property highlights, suites, dining, and scenic ocean views</p>
                            </div>
                        </div>
                        <span class="shrink-0 font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body"><span data-gallery-count>{{ $photoCount }}</span> images uploaded</span>
                    </div>

                    {{-- Multi-image dropzone (1:7842) --}}
                    <label class="mt-[24px] flex cursor-pointer flex-col items-center gap-[4px] rounded-[12px] border-2 border-dashed border-[#c0c7d3] bg-[rgba(241,244,246,0.3)] p-[26px] transition-colors duration-300 hover:border-editorial">
                        <input type="file" name="gallery[]" accept="image/png,image/jpeg,image/webp" multiple class="sr-only" data-gallery-input>
                        <span class="flex size-[48px] items-center justify-center rounded-full bg-[rgba(210,228,255,0.4)]">
                            <img src="{{ $icon('editor-upload.svg') }}" alt="" class="h-[16px] w-[22px]">
                        </span>
                        <span class="pt-[8px] font-jakarta text-[14px] font-bold leading-[20px] text-editorial-ink">Drag &amp; drop high-resolution JPG or PNG files here</span>
                        <span class="pb-[8px] font-jakarta text-[12px] leading-[16px] text-editorial-body">Recommended size 2560x1440px (Max 12MB per photo). Minimum 4 images required.</span>
                        <span class="rounded-[8px] bg-[#ebeef0] px-[16px] py-[6px] font-jakarta text-[12px] font-bold leading-[16px] text-editorial-ink">Browse Files</span>
                        <span data-gallery-picked class="font-jakarta text-[12px] text-editorial"></span>
                    </label>
                    @error('gallery.*') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror

                    {{-- Image Preview Grid (1:7852): first tile is the featured hero cover --}}
                    <div class="mt-[24px] grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-2 xl:grid-cols-4">
                        <label class="relative block aspect-[4/3] cursor-pointer overflow-hidden rounded-[8px] border border-[rgba(192,199,211,0.5)] shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                            <input type="file" name="cover" accept="image/png,image/jpeg,image/webp" class="sr-only" data-cover-input>
                            <img data-cover-preview src="{{ $hotel->image ? \App\Support\ImagePath::url($hotel->image, 'hotels') : '' }}" alt="" @if (! $hotel->image) hidden @endif class="size-full object-cover">
                            @if (! $hotel->image)
                                <span data-cover-empty class="flex size-full items-center justify-center bg-[#f1f4f6] font-jakarta text-[12px] text-editorial-body">Choose hero image</span>
                            @endif
                            <span class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-[rgba(0,0,0,0.7)] via-transparent to-transparent p-[10px]">
                                <span class="font-jakarta text-[10px] font-bold uppercase leading-[24px] tracking-[0.5px] text-[#fcd34d]">Featured Hero</span>
                                <span class="truncate font-jakarta text-[12px] font-medium leading-[16px] text-white">{{ $hotel->name ?: 'Property cover' }}</span>
                            </span>
                        </label>
                        {{-- New uploads are added to these; tick a photo to drop it when the hotel is saved. --}}
                        @foreach ($hotel->gallery ?? [] as $photo)
                            @php($path = $photo['image'] ?? $photo)

                            <figure class="relative aspect-[4/3] overflow-hidden rounded-[8px] border border-[rgba(192,199,211,0.5)] shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                                <img src="{{ \App\Support\ImagePath::url($path, 'hotels/detail') }}" alt="{{ $photo['alt'] ?? '' }}" class="size-full object-cover">
                                <figcaption class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-[rgba(0,0,0,0.7)] via-transparent to-transparent p-[10px]">
                                    <span class="font-jakarta text-[10px] font-bold uppercase leading-[24px] tracking-[0.5px] text-[#9fcaff]">Gallery</span>
                                    <span class="truncate font-jakarta text-[12px] font-medium leading-[16px] text-white">{{ $photo['alt'] ?? $hotel->name }}</span>
                                </figcaption>

                                <label class="absolute inset-0 cursor-pointer">
                                    <input type="checkbox" name="remove_photos[]" value="{{ $path }}" class="peer sr-only">
                                    <span class="absolute right-[8px] top-[8px] flex size-[28px] items-center justify-center rounded-full bg-white/90 text-[16px] leading-none text-[#b91c1c] shadow-sm
                                                 transition-colors peer-checked:bg-[#b91c1c] peer-checked:text-white">&times;</span>
                                    <span class="absolute inset-0 hidden items-center justify-center bg-[rgba(185,28,28,0.65)] font-jakarta text-[12px] font-semibold text-white peer-checked:flex">
                                        Removed on save
                                    </span>
                                </label>
                            </figure>
                        @endforeach
                    </div>
                    @error('cover') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                </section>
            </div>

            <aside class="flex flex-col gap-[24px]">
                {{-- Publishing Settings — same radio cards as the boat/activity editors --}}
                <x-admin.form-section title="Publishing Settings" icon="nav-schedule.svg">
                    <x-admin.radio-cards name="publish" :options="$publishModes" :selected="$hotel->status === \App\Enums\ListingStatus::Draft ? 'draft' : 'publish'" />
                </x-admin.form-section>

                {{-- Location & Harbor Proximity (1:7894) --}}
                <section class="rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-surface p-[25px] shadow-[0_4px_10px_rgba(0,0,0,0.05)]">
                    <div class="flex items-center gap-[10px] border-b border-[#ebeef0] pb-[13px]">
                        <img src="{{ $icon('side-location.svg') }}" alt="" class="h-[16.6px] w-[11.6px]">
                        <h2 class="font-jakarta text-[24px] font-semibold leading-[32px] text-editorial-ink">Location &amp; Harbor Proximity</h2>
                    </div>

                    <div class="mt-[16px] flex flex-col gap-[16px]">
                        <div class="flex flex-col gap-[6px]">
                            <label for="hotel-region" class="{{ $label }}">Island / Region</label>
                            <span class="relative block">
                                <select id="hotel-region" name="region" class="{{ $input }} appearance-none py-[9px] pl-[13px] pr-[40px] text-[16px] leading-[24px]">
                                    <option value="">Select island…</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region }}" @selected(old('region', $hotel->region) === $region)>{{ $region }}</option>
                                    @endforeach
                                </select>
                                <img src="{{ $icon('side-chevron.svg') }}" alt="" class="pointer-events-none absolute right-[9px] top-1/2 size-[24px] -translate-y-1/2">
                            </span>
                        </div>

                        <div class="flex flex-col gap-[6px]">
                            <label for="hotel-address" class="{{ $label }}">Specific Coastal Area</label>
                            <input id="hotel-address" name="address" value="{{ old('address', $hotel->address) }}" required placeholder="Toya Pakeh, Crystal Bay Road"
                                   class="{{ $input }} text-[16px] leading-[24px]">
                            @error('address') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-[6px]">
                            <label for="hotel-harbor" class="{{ $label }}">Harbor Transfer Distance</label>
                            <span class="flex items-center gap-[8px] rounded-[8px] border border-[rgba(192,199,211,0.4)] bg-[#f1f4f6] px-[13px] py-[9px]">
                                <img src="{{ $icon('side-harbor.svg') }}" alt="" class="h-[9.3px] w-[11.6px] shrink-0">
                                <input id="hotel-harbor" name="harbor_distance" value="{{ old('harbor_distance', $hotel->harbor_distance) }}" placeholder="8 minutes from Banjar Nyuh Harbor"
                                       class="w-full bg-transparent font-jakarta text-[12px] font-semibold leading-[16px] text-editorial-ink placeholder:font-normal placeholder:text-editorial-meta focus:outline-none">
                            </span>
                        </div>

                        {{-- Map Picker Card (1:7924) --}}
                        <div class="flex flex-col gap-[6px]">
                            <label for="hotel-coordinates" class="{{ $label }}">Map Pin Coordinates</label>
                            <div class="relative h-[128px] overflow-hidden rounded-[8px] border border-[rgba(192,199,211,0.6)] p-px">
                                <img src="{{ $icon('map-nusa-penida.png') }}" alt="" class="size-full rounded-[7px] object-cover">
                                <span class="absolute inset-0 flex items-center justify-center bg-[rgba(0,0,0,0.2)]">
                                    <span class="flex items-center gap-[6px] rounded-full bg-[rgba(255,255,255,0.9)] px-[12px] py-[4px] shadow-[0_1px_3px_rgba(0,0,0,0.1)] backdrop-blur-[4px]">
                                        <img src="{{ $icon('side-pin.svg') }}" alt="" class="h-[10px] w-[8px]">
                                        <input id="hotel-coordinates" name="coordinates" value="{{ old('coordinates', $hotel->coordinates) }}" placeholder="-8.6792° S, 115.4851° E" size="22"
                                               class="bg-transparent font-jakarta text-[12px] font-bold leading-[16px] text-editorial placeholder:font-normal placeholder:text-editorial-meta focus:outline-none">
                                    </span>
                                </span>
                            </div>
                        </div>

                        <x-admin.field label="Full Address" name="full_address" type="textarea" :value="$hotel->full_address" placeholder="Jalan Raya Toya Pakeh - Ped, Nusa Penida, Bali 80771" />
                    </div>
                </section>

            </aside>
        </form>
    </x-admin.form-page>
@endsection
