{{-- Figma node 1:4086 — "Activity detail full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['activity'])

@php
    // Mobile shows the first two highlights and its own icon set (desktop keeps the 3-up row).
    $mHighlightIcon = ['cancellation.svg' => ['w' => 34, 'h' => 42], 'confirmation.svg' => ['w' => 32, 'h' => 42]];
    $mMetaIcon      = ['pin.svg' => ['w' => 16, 'h' => 20], 'clock.svg' => ['w' => 20, 'h' => 20]];
@endphp

<div class="bg-[#f7fafc] lg:hidden">
    {{-- Hero gallery (1:4130): swipe through every photo, with the count and dots following. --}}
    <header class="relative h-[400px] w-full overflow-hidden">
        <x-photo-swiper :photos="$activity['gallery_photos']" />
    </header>

    {{-- Content pulls up over the hero by 24px, as in Figma. --}}
    <div class="relative -mt-[24px] flex flex-col gap-[16px] px-[20px] pb-[32px]">
        {{-- Main info card (1:4137) --}}
        <section data-reveal class="rounded-[12px] bg-white px-[24px] pb-[24px] pt-[24px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)]">
            @if ($activity['badge'])<span class="inline-block rounded-full bg-[#d5e2e9] px-[12px] py-[4px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#58646a]">{{ $activity['badge'] }}</span>@endif

            <h1 class="mt-[8px] text-[28px] font-bold leading-[36px] text-[#181c1e]">{{ $activity['name'] }}</h1>

            <ul class="mt-[16px] flex flex-wrap items-center gap-[16px] text-[16px] leading-[24px] text-[#414751]">
                @foreach (array_slice($activity['detail_meta'], 0, 2) as $item)
                    @php $size = $mMetaIcon[$item['icon']] ?? ['w' => 20, 'h' => 20]; @endphp
                    <li class="flex items-center gap-[4px]">
                        <img src="{{ asset('images/icons/mobile/detail/'.$item['icon']) }}" alt="" style="width: {{ $size['w'] }}px; height: {{ $size['h'] }}px">
                        {{ $item['label'] }}
                    </li>
                @endforeach
            </ul>

            <p class="mt-[16px] text-[16px] leading-[24px] text-[#414751]">{{ \Illuminate\Support\Str::before((string) $activity['intro'], '. ') }}.</p>

            <ul class="mt-[16px] flex flex-col gap-[16px] border-t border-[rgba(192,199,211,0.3)] pt-[25px]">
                @foreach (array_slice($activity['highlight_items'], 0, 2) as $highlight)
                    @php $size = $mHighlightIcon[$highlight['icon']] ?? ['w' => 34, 'h' => 42]; @endphp
                    <li class="flex items-start gap-[12px]">
                        <img src="{{ asset('images/icons/mobile/detail/'.$highlight['icon']) }}" alt="" class="shrink-0" style="width: {{ $size['w'] }}px; height: {{ $size['h'] }}px">
                        <span>
                            <span class="block text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#181c1e]">{{ $highlight['title'] }}</span>
                            <span class="block text-[14px] leading-[21px] text-[#414751]">{{ $highlight['note'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>

        {{-- Tabs (1:4174) --}}
        <nav data-reveal class="flex gap-[24px] overflow-x-auto border-b border-[rgba(192,199,211,0.3)] px-[8px]" aria-label="Activity sections">
            @foreach ($activity['tabs'] as $tab)
                <a href="#m-{{ $tab['anchor'] }}"
                   @class([
                       'shrink-0 whitespace-nowrap border-b-2 pb-[14px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] transition-colors duration-300',
                       'border-brand text-brand' => $loop->first,
                       'border-transparent text-[#414751]' => ! $loop->first,
                   ])>
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Summary (1:4184) --}}
        <section id="m-summary" data-reveal class="flex flex-col gap-[16px]">
            <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Summary</h2>
            <div class="rich-text text-[16px] leading-[26px] text-[#414751]">{!! $activity['summary_html'] !!}</div>
        </section>

        <section id="m-experiences" data-reveal class="flex flex-col gap-[16px] pt-[16px]" @if (empty($activity['experiences'])) hidden @endif>
            <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Experiences Awaiting You</h2>
            @foreach ($activity['experiences'] ?? [] as $experience)
                <div>
                    <h3 class="text-[16px] font-semibold leading-[24px] text-[#181c1e]">{{ $experience['title'] }}</h3>
                    <p class="mt-[4px] text-[16px] leading-[26px] text-[#414751]">{{ $experience['body'] }}</p>
                </div>
            @endforeach
        </section>

        <section id="m-inclusions" data-reveal class="flex flex-col gap-[16px] pt-[16px]" @if (empty($activity['included']) && empty($activity['excluded'])) hidden @endif>
            <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Inclusions</h2>

            @foreach ([["What's Included", $activity['included'] ?? [], '✓', 'text-[#15803d]'], ["What's Excluded", $activity['excluded'] ?? [], '✕', 'text-[#b91c1c]']] as [$title, $items, $mark, $tone])
                @continue(empty($items))

                <div>
                    <h3 class="text-[16px] font-semibold leading-[24px] text-[#181c1e]">{{ $title }}</h3>
                    <ul class="mt-[8px] flex flex-col gap-[6px]">
                        @foreach ($items as $item)
                            <li class="flex items-start gap-[8px] text-[16px] leading-[26px] text-[#414751]">
                                <span class="{{ $tone }} shrink-0" aria-hidden="true">{{ $mark }}</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </section>

        <section id="m-important-info" data-reveal class="flex flex-col gap-[16px] pt-[16px]" @if (blank($activity['important_notes'])) hidden @endif>
            <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Important Info</h2>

            <ul class="flex flex-col gap-[8px]">
                @foreach (preg_split('/\R+/', trim((string) $activity['important_notes'])) ?: [] as $note)
                    @continue(blank($note))

                    <li class="flex items-start gap-[8px] text-[16px] leading-[26px] text-[#414751]">
                        <img src="{{ asset('images/icons/detail/info.svg') }}" alt="" class="mt-[4px] size-[16px] shrink-0">
                        {{ ltrim($note, "-• \t") }}
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Booking footer (1:4189) --}}
    <div class="border-t border-[rgba(192,199,211,0.3)] bg-white px-[20px] pb-[20px] pt-[20px] drop-shadow-[0px_-4px_10px_rgba(0,0,0,0.05)]">
        <p class="text-[14px] leading-[21px] text-[#414751]">From</p>
        <p class="flex items-baseline gap-[4px]">
            <span class="text-[24px] font-bold leading-[32px] text-brand">{{ $activity['price_label'] }}</span>
            <span class="text-[14px] leading-[32px] text-[#414751]">/person</span>
        </p>

        <div class="mt-[12px] flex flex-col gap-[12px]">
            <a href="{{ route('activities.order', $activity['slug']) }}"
               class="flex items-center justify-center rounded-[8px] bg-brand px-[24px] py-[12px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-white transition-transform duration-300 ease-smooth active:scale-[0.98]">
                Book Now
            </a>
            <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
               class="flex items-center justify-center gap-[8px] rounded-[8px] border border-brand bg-white px-[25px] py-[13px] text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-brand">
                <img src="{{ asset('images/icons/mobile/detail/whatsapp.svg') }}" alt="" class="size-[20px]">
                Ask via WhatsApp
            </a>
        </div>
    </div>
</div>
