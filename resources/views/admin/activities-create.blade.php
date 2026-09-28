{{-- Figma node 1:8502 — admin Add New Activity (also serves Edit). --}}
@extends('layouts.admin')

@php
    $editing = $activity->exists;
    $backHref = route('admin.activities');
    $selectedDays = old('days', $activity->days ?? []);
    $time = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('H:i') : null;
    $money = fn ($v) => $v ? number_format($v, 0, ',', '.') : null;
    $currentStatus = old('status', $activity->status?->value);
    $chips = fn ($items) => is_array($items) ? implode(', ', $items) : (string) $items;
@endphp

@section('title', $editing ? 'Edit '.$activity->name : 'Add New Activity')

@section('admin-active', 'activity')

@section('content')
    <x-admin.form-page
        :heading="$editing ? 'Edit Activity' : 'Add New Activity'"
        subtitle="Publish cultural tours, watersports, and day experiences for Penida Gili passengers."
        :back-href="$backHref">

        {{-- Action Controls (1:8513) --}}
        <x-slot:actions>
            <button type="submit" form="activity-form" name="submit_as" value="draft"
                    class="flex items-center gap-[8px] rounded-[8px] border border-[#c0c7d3] bg-surface px-[18px] py-[10px] font-jakarta text-[14px] font-semibold text-editorial-ink
                           transition-colors duration-300 hover:bg-[#f1f4f6]">
                <x-admin.icon name="form-save.svg" class="size-[16px] bg-editorial" />
                Save Draft
            </button>
            <button type="submit" form="activity-form" name="submit_as" value="publish"
                    class="flex items-center gap-[8px] rounded-[8px] bg-editorial px-[18px] py-[10px] font-jakarta text-[14px] font-semibold text-white
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                <x-admin.icon name="nav-activity.svg" class="size-[16px] bg-white" />
                Publish Activity
            </button>
        </x-slot:actions>

        <form id="activity-form" action="{{ $editing ? route('admin.activities.update', $activity) : route('admin.activities.store') }}" method="post" enctype="multipart/form-data"
              class="grid [&>*]:min-w-0 gap-[24px] lg:grid-cols-[minmax(0,640fr)_minmax(0,320fr)]" data-activity-form>
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="flex flex-col gap-[24px]">
                {{-- 1. Basic Information (1:8525) --}}
                <x-admin.panel title="Basic Information" description="General identification and core activity categorization" icon="nav-activity.svg">
                    <div class="flex flex-col gap-[20px]">
                        <x-admin.field label="Activity Title" name="name" :value="$activity->name" placeholder="Balinese Traditional Costume Rental at Penglipuran" :required="true" />

                        <div class="grid [&>*]:min-w-0 gap-[20px] sm:grid-cols-2">
                            <x-admin.field label="Category" name="category" :value="$activity->category" :options="$categories" placeholder="Select category..." :required="true" />
                            <x-admin.field label="Short Catchy Tagline / Badge" name="badge" :value="$activity->badge" placeholder="Best Seller" />
                        </div>

                        <div class="grid [&>*]:min-w-0 gap-[20px] sm:grid-cols-2">
                            <x-admin.field label="Location" name="place_label" :value="$activity->place_label" placeholder="Penglipuran"
                                           help="Shown on the activity card next to the pin icon." />
                            <x-admin.field label="Rating" name="rating" type="number" step="0.1" min="1" max="5" :value="$activity->rating > 0 ? $activity->rating : null" placeholder="4.8"
                                           help="Shown on the card. Leave empty to hide the stars." />
                        </div>

                        {{-- Rich-text editor (1:8566). The contenteditable is the control the admin
                             types in; it mirrors into the textarea that actually posts, so the field
                             still works (as plain text) when JavaScript is off. --}}
                        <div class="flex flex-col gap-[8px]" data-editor>
                            <label for="activity-description" class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">Full Description *</label>
                            <div @class(['overflow-hidden rounded-[8px] border bg-[#f7fafc]', 'border-[#dc2626]' => $errors->has('description'), 'border-[rgba(192,199,211,0.5)]' => ! $errors->has('description')])>
                                <div class="flex items-center gap-[4px] border-b border-[rgba(192,199,211,0.3)] bg-surface px-[8px] py-[6px] font-jakarta text-[13px] text-editorial-body">
                                    <button type="button" data-editor-command="bold" title="Bold" aria-label="Bold"
                                            class="rounded-[4px] px-[8px] py-[2px] font-bold transition-colors hover:bg-[#f1f4f6] aria-pressed:bg-editorial/10 aria-pressed:text-editorial">B</button>
                                    <button type="button" data-editor-command="italic" title="Italic" aria-label="Italic"
                                            class="rounded-[4px] px-[8px] py-[2px] italic transition-colors hover:bg-[#f1f4f6] aria-pressed:bg-editorial/10 aria-pressed:text-editorial">I</button>
                                    <button type="button" data-editor-command="underline" title="Underline" aria-label="Underline"
                                            class="rounded-[4px] px-[8px] py-[2px] underline transition-colors hover:bg-[#f1f4f6] aria-pressed:bg-editorial/10 aria-pressed:text-editorial">U</button>
                                    <span class="mx-[4px] h-[16px] w-px bg-[rgba(192,199,211,0.5)]"></span>
                                    <button type="button" data-editor-command="insertUnorderedList" title="Bulleted list"
                                            class="rounded-[4px] px-[8px] py-[2px] transition-colors hover:bg-[#f1f4f6] aria-pressed:bg-editorial/10 aria-pressed:text-editorial">&bull; List</button>
                                    <button type="button" data-editor-command="insertOrderedList" title="Numbered list"
                                            class="rounded-[4px] px-[8px] py-[2px] transition-colors hover:bg-[#f1f4f6] aria-pressed:bg-editorial/10 aria-pressed:text-editorial">1. List</button>
                                </div>

                                <div data-editor-surface contenteditable="true" role="textbox" aria-multiline="true" aria-labelledby="activity-description-label" hidden
                                     class="rich-text min-h-[200px] w-full px-[17px] py-[13px] font-jakarta text-[16px] leading-[26px] text-editorial-ink focus:outline-none">{!! \App\Support\RichText::clean(old('description', $activity->description)) !!}</div>

                                <textarea id="activity-description" name="description" rows="8" required data-editor-input
                                          placeholder="Step into the timeless living heritage of Penglipuran Village…"
                                          class="w-full resize-y bg-transparent px-[17px] py-[13px] font-jakarta text-[16px] leading-[26px] text-editorial-ink placeholder:text-editorial-meta focus:outline-none">{{ old('description', $activity->description) }}</textarea>
                            </div>
                            @error('description') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </x-admin.panel>

                {{-- 2. Schedule & Operational Hours (1:8599) --}}
                <x-admin.panel title="Schedule & Operational Hours" description="Daily timetable, service timeframes, and customer commitment policies" icon="nav-schedule.svg">
                    <fieldset>
                        <div class="flex items-center justify-between">
                            <legend class="font-jakarta text-[14px] font-semibold text-editorial-ink">Operating Days</legend>
                            <button type="button" data-select-all-days class="font-jakarta text-[13px] font-semibold text-editorial hover:underline">Select All Days</button>
                        </div>
                        <div class="mt-[12px] flex flex-wrap gap-[10px]">
                            @foreach ($days as $day)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="days[]" value="{{ $day }}" @checked(in_array($day, $selectedDays, true)) class="peer sr-only">
                                    <span class="block rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] px-[18px] py-[9px] font-jakarta text-[15px] text-editorial-body
                                                 peer-checked:border-editorial peer-checked:bg-editorial/10 peer-checked:text-editorial">{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    <div class="mt-[20px] grid [&>*]:min-w-0 gap-[20px] sm:grid-cols-3">
                        <x-admin.field label="Opening Time" name="opens_at" type="time" :value="$time($activity->opens_at)" />
                        <x-admin.field label="Closing Time" name="closes_at" type="time" :value="$time($activity->closes_at)" />
                        <x-admin.field label="Duration Estimate" name="duration_label" :value="$activity->duration_label" placeholder="1 - 2 Hours" />
                    </div>

                    {{-- Toggles & Dropdown Rules (1:8676) --}}
                    <div class="mt-[20px] grid [&>*]:min-w-0 gap-[20px] sm:grid-cols-2">
                        <div class="rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] px-[16px] py-[14px]">
                            <x-admin.toggle name="instant_confirmation" label="Instant Confirmation" description="Automatically issue ticket vouchers on payment" :checked="old('instant_confirmation', $activity->instant_confirmation ?? true)" />
                        </div>
                        <x-admin.field label="Cancellation Policy" name="cancellation_policy" :value="$activity->cancellation_policy ?? 'free_24h'" :options="$cancellationPolicies" />
                    </div>
                </x-admin.panel>

                {{-- 3. Inclusions & Important Info (1:8695) — the two tabs of the same name on the activity page --}}
                <x-admin.panel title="Inclusions &amp; Important Info" description="Fills the Inclusions and Important Info tabs on the activity page" icon="form-check.svg">
                    <div class="flex flex-col gap-[20px]">
                        @foreach (['included' => ["Inclusions — What's Included", '+ Add inclusion...', 'bg-editorial/10 text-editorial'], 'excluded' => ["Inclusions — What's Excluded", '+ Add exclusion...', 'bg-[#fee2e2] text-[#991b1b]']] as $field => [$label, $placeholder, $tone])
                            <div class="flex flex-col gap-[8px]" data-keywords data-chip-class="{{ $tone }}">
                                <span class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">{{ $label }}</span>
                                <input type="hidden" name="{{ $field }}" value="{{ $chips(old($field, $activity->{$field} ?? [])) }}">
                                <div class="flex flex-wrap items-center gap-[8px] rounded-[8px] border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] px-[12px] py-[8px] focus-within:border-editorial">
                                    <span class="contents" data-keywords-list></span>
                                    <input type="text" placeholder="{{ $placeholder }}" autocomplete="off"
                                           class="min-w-[160px] flex-1 bg-transparent py-[5px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-none">
                                </div>
                                @error($field) <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                            </div>
                        @endforeach

                        {{-- Real newlines: an &#10; entity inside a Blade echo would print literally. --}}
                        <x-admin.field label="Important Info" name="important_notes" type="textarea" :value="$activity->important_notes"
                                       :placeholder="implode(PHP_EOL, [
                                           '- Please show your mobile e-voucher at the local reception counter upon arrival.',
                                           '- Costumes are available for adults and children (ages 5+).',
                                           '- Hands-on help with sarongs and sashes is included.',
                                       ])"
                                       help="Shown under the Important Info tab; one line per point." />
                    </div>
                </x-admin.panel>

                {{-- 4. Experiences Awaiting You (1:1654 on the public page) --}}
                <x-admin.panel title="Experiences Awaiting You" description="Highlights listed one by one on the activity page." icon="form-check.svg">
                    @include('partials.admin.activity-experiences')
                </x-admin.panel>

                {{-- 5. Cover photo (1:8775) — the single image the activity card shows --}}
                <x-admin.panel title="Cover Photo" description="Shown on the activity card in the catalogue. One photo, landscape works best." icon="form-camera.svg">
                    <x-admin.uploader name="cover" :multiple="false" hint="PNG, JPG or WEBP, one photo (max 10MB).">
                        @if ($activity->image)
                            <x-admin.gallery-preview :cover="$activity->image" folder="activities" />
                        @endif
                    </x-admin.uploader>
                </x-admin.panel>

                {{-- 5. Gallery (1:8796) — extra photos on the detail page --}}
                <x-admin.panel title="Media & Gallery Upload" description="Extra photos shown inside the activity page. The cover above stays separate." icon="form-camera.svg" :badge="count($activity->gallery ?? []).' / 8 Photos'" badge-tone="muted">
                    <x-admin.uploader name="gallery" hint="PNG, JPG or WEBP, up to 8 photos. Pick several at once, or add them one by one.">
                        @if ($activity->gallery)
                            <p class="mt-[20px] font-jakarta text-[14px] leading-[22px] text-editorial-meta">
                                Already in the gallery &mdash; new photos are added to these. Tick one to remove it when you save.
                            </p>

                            <x-admin.gallery-preview :items="$activity->gallery" folder="activities" remove="remove_photos" />
                        @endif
                    </x-admin.uploader>
                </x-admin.panel>
            </div>

            <aside class="flex flex-col gap-[24px]">
                {{-- 1. Publishing Status (1:8828) --}}
                <x-admin.panel title="Publishing Status" icon="form-vessel.svg">
                    <x-admin.radio-cards name="status" :options="$statuses" :selected="$currentStatus" />

                    <div class="mt-[16px] border-t border-[rgba(192,199,211,0.3)] pt-[16px]">
                        <x-admin.toggle name="is_public" label="Public Visibility" description="Show on Penida Gili website portal" :checked="old('is_public', $activity->is_public ?? true)" />
                    </div>
                </x-admin.panel>

                {{-- 2. Pricing & Quota Capacity (1:8875) --}}
                <x-admin.panel title="Pricing & Quota Capacity" icon="kpi-revenue.svg">
                    <div class="flex flex-col gap-[16px]">
                        <x-admin.field label="Base Price per Pax (IDR)" name="price_adult" :value="$money($activity->price_adult)" placeholder="75.000" prefix="Rp" :required="true" />
                        {{-- Set the discount either way round: type the percentage and the
                             strikethrough price is worked out, or type the old price and the
                             percentage follows. Only the two prices are stored. --}}
                        <div>
                            <x-admin.field label="Discount (%)" name="discount_percent" type="number" min="0" max="95" step="1"
                                           :value="$activity->discount_percent ?: null" placeholder="22"
                                           help="Optional. Fills the original price below from the base price." />
                        </div>

                        <div>
                            <x-admin.field label="Original / Strikethrough Price" name="price_was" :value="$money($activity->price_was)" placeholder="150.000" prefix="Rp" />
                            <p data-discount-note class="mt-[6px] font-jakarta text-[13px] leading-[18px] text-editorial" @if (! $activity->discount_percent) hidden @endif>
                                Displays {{ $activity->discount_percent }}% discount badge to customers
                            </p>
                        </div>

                        <div class="flex flex-col gap-[8px]">
                            <label for="max-daily-capacity" class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">Max Daily Capacity / Quota *</label>
                            <span @class(['flex items-center rounded-[8px] border bg-[#f7fafc] pr-[17px]', 'border-[#dc2626]' => $errors->has('max_daily_capacity'), 'border-[rgba(192,199,211,0.5)]' => ! $errors->has('max_daily_capacity')])>
                                <input id="max-daily-capacity" name="max_daily_capacity" type="number" min="1" value="{{ old('max_daily_capacity', $activity->max_daily_capacity ?? 50) }}" required
                                       class="w-full bg-transparent px-[17px] py-[13px] font-jakarta text-[16px] leading-[24px] text-editorial-ink focus:outline-none">
                                <span class="whitespace-nowrap font-jakarta text-[14px] text-editorial-body">pax / day</span>
                            </span>
                            @error('max_daily_capacity') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </x-admin.panel>
            </aside>
        </form>
    </x-admin.form-page>
@endsection
