{{-- One testimonial row inside the boat form. --}}
<div class="rounded-admin border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] p-[20px]" data-repeater-row>
    @if (! empty($row['id']))
        <input type="hidden" name="testimonials[{{ $i }}][id]" value="{{ $row['id'] }}">
    @endif

    <div class="grid [&>*]:min-w-0 gap-[16px] md:grid-cols-3">
        <x-admin.field label="Guest Name" name="testimonials[{{ $i }}][name]" :value="$row['name'] ?? null" placeholder="Sarah Jenkins" />

        <x-admin.field label="Stars" name="testimonials[{{ $i }}][stars]" type="number" step="1" min="1" max="5" :value="$row['stars'] ?? 5" />

        <x-admin.field label="Traveled" name="testimonials[{{ $i }}][experienced_at]" type="month" :value="$row['experienced_at'] ?? null" />
    </div>

    <div class="mt-[16px]">
        <x-admin.field label="Quote" name="testimonials[{{ $i }}][quote]" type="textarea" :value="$row['quote'] ?? null"
                       placeholder="Incredibly smooth ride and the staff were extremely helpful with our luggage." />
    </div>

    @if (! empty($row['id']))
        <label class="mt-[14px] flex w-fit cursor-pointer items-center gap-[10px] font-jakarta text-[14px] text-editorial-body">
            <input type="checkbox" name="testimonials[{{ $i }}][remove]" value="1" class="size-[18px] accent-[#b91c1c]">
            Remove this testimonial when I save
        </label>
    @else
        <button type="button" data-repeater-drop
                class="mt-[14px] font-jakarta text-[14px] font-semibold text-[#b91c1c] hover:underline">Discard this row</button>
    @endif
</div>
