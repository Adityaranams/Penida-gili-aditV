{{-- Guest testimonials for one boat. Rows post as testimonials[i][...]; the row's
     hidden id marks an existing review, and ticking Remove deletes it on save. --}}
@php
    $rows = old('testimonials', $vessel->exists
        ? $vessel->reviews->map(fn ($review) => [
            'id' => $review->id,
            'name' => $review->name,
            'stars' => $review->stars,
            'quote' => $review->quote,
            'experienced_at' => $review->experienced_at?->format('Y-m'),
        ])->all()
        : []);
@endphp

<div data-repeater>
    <p class="mb-[16px] font-jakarta text-[15px] leading-[24px] text-editorial-body">
        Shown under &ldquo;Guest Testimonials&rdquo; on this boat&rsquo;s page. Each boat keeps its own.
    </p>

    <div class="flex flex-col gap-[16px]" data-repeater-list>
        @foreach ($rows as $i => $row)
            @include('partials.admin.boat-testimonial-row', ['i' => $i, 'row' => $row])
        @endforeach
    </div>

    <p class="mt-[16px] font-jakarta text-[14px] leading-[22px] text-editorial-meta" data-repeater-empty @if ($rows) hidden @endif>
        No testimonials yet for this boat.
    </p>

    <button type="button" data-repeater-add
            class="mt-[20px] rounded-[8px] border border-[rgba(192,199,211,0.6)] bg-[#f7fafc] px-[18px] py-[11px] font-jakarta text-[15px] font-semibold text-editorial
                   transition-colors duration-300 hover:border-editorial hover:bg-[#eef4f8]">
        + Add Testimonial
    </button>

    {{-- Blank row the script clones; __INDEX__ is swapped for the next free index. --}}
    <template data-repeater-template>
        @include('partials.admin.boat-testimonial-row', ['i' => '__INDEX__', 'row' => []])
    </template>
</div>
