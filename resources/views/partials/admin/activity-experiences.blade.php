{{-- "Experiences Awaiting You" rows on the activity page. Posts as experiences[i][title|body];
     rows left blank are ignored, and dropping a row removes it on save. --}}
@php
    $rows = old('experiences', $activity->experiences ?? []);
@endphp

<div data-repeater>
    <p class="mb-[16px] font-jakarta text-[15px] leading-[24px] text-editorial-body">
        Each entry becomes a titled paragraph under &ldquo;Experiences Awaiting You&rdquo; on the activity page.
    </p>

    <div class="flex flex-col gap-[16px]" data-repeater-list>
        @foreach ($rows as $i => $row)
            @include('partials.admin.activity-experience-row', ['i' => $i, 'row' => $row])
        @endforeach
    </div>

    <p class="mt-[16px] font-jakarta text-[14px] leading-[22px] text-editorial-meta" data-repeater-empty @if ($rows) hidden @endif>
        No experiences yet &mdash; the section stays hidden on the page.
    </p>

    <button type="button" data-repeater-add
            class="mt-[20px] rounded-[8px] border border-[rgba(192,199,211,0.6)] bg-[#f7fafc] px-[18px] py-[11px] font-jakarta text-[15px] font-semibold text-editorial
                   transition-colors duration-300 hover:border-editorial hover:bg-[#eef4f8]">
        + Add Experience
    </button>

    {{-- Blank row the script clones; __INDEX__ is swapped for the next free index. --}}
    <template data-repeater-template>
        @include('partials.admin.activity-experience-row', ['i' => '__INDEX__', 'row' => []])
    </template>
</div>
