{{-- One "Experiences Awaiting You" entry. --}}
<div class="rounded-admin border border-[rgba(192,199,211,0.5)] bg-[#f7fafc] p-[20px]" data-repeater-row>
    <x-admin.field label="Heading" name="experiences[{{ $i }}][title]" :value="$row['title'] ?? null" placeholder="Watch the Battle of Good and Evil" />

    <div class="mt-[16px]">
        <x-admin.field label="Description" name="experiences[{{ $i }}][body]" type="textarea" :value="$row['body'] ?? null"
                       placeholder="Follow the mythical Barong as it faces the witch Rangda in a dance passed down through generations." />
    </div>

    <button type="button" data-repeater-drop
            class="mt-[14px] font-jakarta text-[14px] font-semibold text-[#b91c1c] hover:underline">Remove this experience</button>
</div>
