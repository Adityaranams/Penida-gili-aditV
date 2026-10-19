{{-- Shared section heading: eyebrow + display title (+ optional right-aligned intro) --}}
@props(['eyebrow', 'title', 'intro' => null])

<div class="grid gap-x-[113px] gap-y-6 lg:grid-cols-2">
    <div data-reveal>
        <p class="text-[20px] font-semibold uppercase leading-[30px] text-brand">{{ $eyebrow }}</p>
        <h2 class="mt-[50px] max-w-[827px] text-[30px] lg:text-[71px] leading-[53px] lg:leading-[118px] text-ink">{!! $title !!}</h2>
    </div>

    @if ($intro)
        <p data-reveal style="--reveal-delay: 120ms"
           class="max-w-[640px] text-[16px] leading-[30px] text-ink-muted lg:text-[19px] lg:leading-[34px] lg:self-end lg:pb-[60px]">{{ $intro }}</p>
    @endif
</div>
