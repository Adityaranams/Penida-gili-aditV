{{-- Figma 1:7613 — one "Room N Card": numbered header with stock pill and Edit / Remove,
     four stat tiles, and the inline field editor that Edit reveals. --}}
@php
    $stock = (int) ($room['stock'] ?? 0);
    $highDemand = $stock > 0 && $stock < 5;
    $priceText = ($room['price_per_night'] ?? '') !== '' ? 'IDR '.number_format((int) preg_replace('/\D+/', '', (string) $room['price_per_night']), 0, ',', '.') : '—';
@endphp
<div class="rounded-[12px] border border-[rgba(192,199,211,0.6)] bg-[rgba(241,244,246,0.4)] p-[21px]" data-room>
    <input type="hidden" name="rooms[{{ $i }}][id]" value="{{ $room['id'] ?? '' }}">

    <div class="flex flex-wrap items-center justify-between gap-[12px] border-b border-[rgba(192,199,211,0.3)] pb-[13px]">
        <span class="flex items-center gap-[8px]">
            <span class="flex size-[24px] items-center justify-center rounded-full bg-editorial font-jakarta text-[12px] font-bold text-white" data-room-number>{{ is_numeric($i) ? $i + 1 : '' }}</span>
            <span class="font-jakarta text-[16px] font-bold leading-[24px] text-editorial-ink" data-room-title>{{ $room['name'] ?: 'New Room Category' }}</span>
            <span @class(['rounded-full px-[8px] py-[2px] font-jakarta text-[11px] font-semibold leading-[24px]', 'bg-[#fef3c7] text-[#92400e]' => $highDemand, 'bg-[#d1fae5] text-[#065f46]' => ! $highDemand]) data-room-pill>
                {{ $highDemand ? 'High Demand' : 'Available' }}
            </span>
        </span>
        <span class="flex items-center gap-[8px]">
            <button type="button" data-room-edit class="flex items-center gap-[4px] font-jakarta text-[12px] font-semibold text-editorial hover:underline">
                <img src="{{ $icon('room-edit.svg') }}" alt="" class="size-[10.5px]">
                Edit
            </button>
            <button type="button" data-room-remove class="flex items-center gap-[4px] font-jakarta text-[12px] font-semibold text-[#ba1a1a] hover:underline">
                <img src="{{ $icon('room-remove.svg') }}" alt="" class="h-[10.5px] w-[9.3px]">
                Remove
            </button>
        </span>
    </div>

    {{-- Stat tiles (1:7631) --}}
    <div class="mt-[16px] grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-4" data-room-stats>
        <div class="rounded-[8px] border border-[rgba(192,199,211,0.3)] bg-surface p-[13px]">
            <span class="block font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body">Capacity &amp; Bed</span>
            <span class="mt-[4px] flex items-center gap-[6px] font-jakarta text-[12px] font-bold leading-[16px] text-editorial-ink">
                <img src="{{ $icon('room-bed.svg') }}" alt="" class="h-[10.7px] w-[14.6px] shrink-0">
                <span data-room-stat="capacity">{{ ($room['guests'] ?? 2).' Guests'.(filled($room['bed'] ?? null) ? ', '.$room['bed'] : '') }}</span>
            </span>
        </div>
        <div class="rounded-[8px] border border-[rgba(192,199,211,0.3)] bg-surface p-[13px]">
            <span class="block font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body">Room Size</span>
            <span class="mt-[4px] flex items-center gap-[6px] font-jakarta text-[12px] font-bold leading-[16px] text-editorial-ink">
                <img src="{{ $icon('room-size.svg') }}" alt="" class="size-[11.3px] shrink-0">
                <span data-room-stat="size">{{ $room['size_label'] ?: '—' }}</span>
            </span>
        </div>
        <div class="rounded-[8px] border border-[rgba(192,199,211,0.3)] bg-surface p-[13px]">
            <span class="block font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body">Base Price / Night</span>
            <span class="mt-[4px] block font-jakarta text-[14px] font-bold leading-[20px] text-editorial" data-room-stat="price">{{ $priceText }}</span>
        </div>
        <div class="rounded-[8px] border border-[rgba(192,199,211,0.3)] bg-surface p-[13px]">
            <span class="block font-jakarta text-[12px] font-medium leading-[16px] text-editorial-body">Inventory Allotment</span>
            <span @class(['mt-[4px] flex items-center gap-[6px] font-jakarta text-[12px] font-bold leading-[16px]', 'text-[#b45309]' => $highDemand, 'text-[#047857]' => ! $highDemand]) data-room-stock>
                <img src="{{ $icon($highDemand ? 'room-stock-low.svg' : 'room-stock-ok.svg') }}" alt="" class="size-[13.3px] shrink-0" data-room-stock-icon data-ok="{{ $icon('room-stock-ok.svg') }}" data-low="{{ $icon('room-stock-low.svg') }}">
                <span data-room-stat="stock">{{ $stock }} Units Left</span>
            </span>
        </div>
    </div>

    {{-- Inline editor: hidden until Edit (open by default for new / invalid rows) --}}
    <div class="mt-[16px] grid [&>*]:min-w-0 gap-[12px] sm:grid-cols-6" data-room-fields @unless ($open) hidden @endunless>
        <div class="sm:col-span-3"><x-admin.field label="Room Name" name="rooms[{{ $i }}][name]" :value="$room['name'] ?? ''" placeholder="Deluxe Ocean Room" /></div>
        <x-admin.field label="Guests" name="rooms[{{ $i }}][guests]" type="number" :value="$room['guests'] ?? 2" />
        <div class="sm:col-span-2"><x-admin.field label="Bed" name="rooms[{{ $i }}][bed]" :value="$room['bed'] ?? ''" placeholder="1 King Bed" /></div>
        <div class="sm:col-span-2"><x-admin.field label="Size Label" name="rooms[{{ $i }}][size_label]" :value="$room['size_label'] ?? ''" placeholder="45 m² Ocean Terrace" /></div>
        <div class="sm:col-span-3"><x-admin.field label="Base Price / Night (IDR)" name="rooms[{{ $i }}][price_per_night]" :value="$money($room['price_per_night'] ?? '')" prefix="Rp" placeholder="2.500.000" /></div>
        <x-admin.field label="Units" name="rooms[{{ $i }}][stock]" type="number" :value="$room['stock'] ?? 1" />

        {{-- Room photo: shown on "Select Your Room". Left empty, the design's default picture is used. --}}
        <div class="sm:col-span-6" data-room-photo>
            <span class="block font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">Room Photo</span>
            <input type="hidden" name="rooms[{{ $i }}][image]" value="{{ $room['image'] ?? '' }}">

            <label class="mt-[8px] flex cursor-pointer items-center gap-[14px] rounded-[10px] border border-dashed border-[rgba(192,199,211,0.6)] bg-surface p-[12px] transition-colors duration-300 hover:border-editorial">
                <input type="file" name="rooms[{{ $i }}][photo]" accept="image/png,image/jpeg,image/webp" class="sr-only" data-room-photo-input>

                <img data-room-photo-preview src="{{ ($room['image'] ?? null) ? \App\Support\ImagePath::url($room['image'], 'hotels/detail') : '' }}"
                     alt="" @unless ($room['image'] ?? null) hidden @endunless class="h-[64px] w-[86px] shrink-0 rounded-[8px] object-cover">

                <span class="min-w-0">
                    <span class="block font-jakarta text-[13px] font-semibold text-editorial-ink">{{ ($room['image'] ?? null) ? 'Replace photo' : 'Upload a room photo' }}</span>
                    <span data-room-photo-name class="block truncate font-jakarta text-[12px] text-editorial-body">PNG, JPG or WEBP &mdash; shown on the room card</span>
                </span>
            </label>
            @error("rooms.$i.photo") <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
