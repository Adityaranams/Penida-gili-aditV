{{-- Figma node 1:1442 — Activity Detail (desktop) / 1:4086 — Activity detail full (mobile) --}}
@extends('layouts.app')

@section('title', $activity['name'])

@section('nav-active', 'activity')

@section('hero')
    {{-- Mobile (< lg) gets its own hero + sections from the mobile Figma frame. --}}
    @include('partials.activity.mobile-detail', ['activity' => $activity])

    <header class="relative hidden h-[184px] w-full overflow-hidden lg:block">
        <img src="{{ asset('images/activities/detail/hero-strip.png') }}" alt=""
             class="absolute inset-0 size-full object-cover">

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'activity'])
        </div>
    </header>
@endsection

@section('content')
    <div class="hidden lg:block">
    {{-- Figma node 1:1447 — image grid --}}
    <section class="container-page pt-[43px]">
        <div data-reveal class="grid [&>*]:min-w-0 gap-[21.4px] overflow-hidden rounded-detail md:grid-cols-3 md:grid-rows-2">
            {{-- Fixed heights keep the collage the same shape regardless of the uploaded photo's aspect ratio.
                 data-lightbox-group ties the three frames together so the viewer can page through them. --}}
            @php($frames = array_slice($activity['gallery_photos'], 0, 3))
            @php($extra = count($activity['gallery_photos']) - count($frames))

            @foreach ($frames as $photo)
                <button type="button" data-lightbox-group="activity" data-lightbox="{{ $photo['url'] }}" data-lightbox-alt="{{ $photo['alt'] }}"
                        @class([
                            'group relative block cursor-zoom-in overflow-hidden rounded-detail',
                            'h-[461px] lg:h-[667px] md:col-span-2 md:row-span-2' => $loop->first,
                            'h-[220px] lg:h-[323px]' => ! $loop->first,
                        ])>
                    <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}"
                         class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">

                    {{-- Anything that does not fit is counted on the last frame; opening it pages through them all. --}}
                    @if ($loop->last && $extra > 0)
                        <span class="absolute inset-0 flex items-center justify-center bg-black/45 text-[30px] font-semibold text-white transition-colors duration-300 group-hover:bg-black/55">
                            +{{ $extra }}
                        </span>
                    @endif
                </button>
            @endforeach

            <x-gallery-extras :photos="array_slice($activity['gallery_photos'], 3)" group="activity" />
        </div>
    </section>

    {{-- Figma node 1:1584 — two column layout --}}
    <div class="container-page mt-[27px] lg:mt-[64px] grid [&>*]:min-w-0 gap-[32px] lg:grid-cols-[minmax(0,986fr)_minmax(0,477fr)] lg:gap-[32px]">
        <div>
            {{-- Figma node 1:1586 — header info --}}
            <div data-reveal class="border-b border-editorial-line pb-[33px]">
                @if ($activity['badge'])
                    <span class="inline-block rounded-full bg-[#d5e2e9] px-[16px] py-[5.3px] text-[16px] leading-[32px] text-[#58646a]">
                        {{ $activity['badge'] }}
                    </span>
                @endif

                <h1 class="mt-[10.7px] text-[27px] lg:text-[64px] font-bold leading-[36px] lg:leading-[80px] tracking-[-1.28px] text-editorial-ink">{{ $activity['name'] }}</h1>

                <ul class="mt-[21.4px] flex flex-wrap gap-[21.4px] text-[21.4px] leading-[32px] text-editorial-body">
                    @foreach ($activity['detail_meta'] as $item)
                        <li class="flex items-center gap-[10.7px]">
                            <img src="{{ asset('images/icons/detail/'.$item['icon']) }}" alt="" class="size-[26.7px] object-contain">
                            {{ $item['label'] }}
                        </li>
                    @endforeach
                </ul>

                <p class="mt-[21.4px] text-[21.4px] leading-[34.7px] text-editorial-ink">{{ $activity['intro'] }}</p>
            </div>

            {{-- Figma node 1:1613 — feature highlights --}}
            <ul data-reveal style="--reveal-delay: 90ms"
                class="flex flex-col gap-[21.4px] border-b border-editorial-line py-[33px] sm:flex-row">
                @foreach ($activity['highlight_items'] as $highlight)
                    <li class="flex flex-1 items-center gap-[16px]">
                        {{-- The SVGs already carry their rounded tint background; give all three the same box. --}}
                        <img src="{{ asset('images/icons/detail/'.$highlight['icon']) }}" alt="" class="h-[56px] w-auto shrink-0 object-contain">
                        <span>
                            <span class="block text-[21.4px] font-semibold leading-[32px] text-editorial-ink">{{ $highlight['title'] }}</span>
                            <span class="block text-[18.7px] font-semibold leading-[26.7px] tracking-[0.93px] text-editorial-body">{{ $highlight['note'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>

            {{-- Figma node 1:1638 — content tabs --}}
            <nav data-reveal class="flex gap-[32px] overflow-x-auto border-b border-editorial-line pb-[12px] pt-[21.4px]" aria-label="Activity sections">
                @foreach ($activity['tabs'] as $tab)
                    <a href="#{{ $tab['anchor'] }}"
                       @class([
                           'shrink-0 whitespace-nowrap pb-[13.3px] text-[21.4px] leading-[32px] transition-colors duration-300',
                           'border-b-[2.7px] border-brand font-bold text-brand' => $loop->first,
                           'text-editorial-body hover:text-brand' => ! $loop->first,
                       ])>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Figma node 1:1647 — summary --}}
            <section id="summary" class="pt-[43px]">
                <h2 data-reveal class="text-[32px] font-semibold leading-[42.7px] text-editorial-ink">Summary</h2>
                <div data-reveal class="rich-text mt-[21.4px] text-[21.4px] leading-[34.7px] text-editorial-ink">{!! $activity['summary_html'] !!}</div>

                <figure data-reveal class="group mt-[21.4px] h-[228px] lg:h-[544px] overflow-hidden rounded-detail">
                    <img src="{{ $activity['summary_image_url'] }}" alt="{{ $activity['name'] }}"
                         class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
                </figure>
            </section>

            {{-- Figma node 1:1654 — experiences --}}
            <section id="experiences" class="pt-[43px]" @if (empty($activity['experiences'])) hidden @endif>
                <h2 data-reveal class="text-[32px] font-semibold leading-[42.7px] text-editorial-ink">Experiences Awaiting You</h2>

                <div class="mt-[32px] flex flex-col gap-[32px]">
                    @foreach ($activity['experiences'] ?? [] as $index => $experience)
                        <div data-reveal style="--reveal-delay: {{ $index * 90 }}ms" class="flex flex-col gap-[10.7px]">
                            <h3 class="text-[24px] font-semibold leading-[32px] text-editorial-ink">{{ $experience['title'] }}</h3>
                            <p class="text-[21.4px] leading-[32px] text-editorial-body">{{ $experience['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- What the price covers, straight from the console's Inclusions fields --}}
            <section id="inclusions" class="pt-[43px]" @if (empty($activity['included']) && empty($activity['excluded'])) hidden @endif>
                <h2 data-reveal class="text-[32px] font-semibold leading-[42.7px] text-editorial-ink">Inclusions</h2>

                <div class="mt-[32px] grid [&>*]:min-w-0 gap-[32px] sm:grid-cols-2">
                    @foreach ([['What&rsquo;s Included', $activity['included'] ?? [], 'included.svg', 'text-[#15803d]'], ['What&rsquo;s Excluded', $activity['excluded'] ?? [], 'excluded.svg', 'text-[#b91c1c]']] as [$title, $items, $icon, $tone])
                        @continue(empty($items))

                        <div data-reveal>
                            <h3 class="text-[24px] font-semibold leading-[32px] text-editorial-ink">{!! $title !!}</h3>

                            <ul class="mt-[16px] flex flex-col gap-[12px]">
                                @foreach ($items as $item)
                                    <li class="flex items-start gap-[10.7px] text-[21.4px] leading-[32px] text-editorial-body">
                                        <span class="{{ $tone }} shrink-0 text-[21.4px] leading-[32px]" aria-hidden="true">{{ $icon === 'included.svg' ? '✓' : '✕' }}</span>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Important Info: one line per point, as typed in the console --}}
            <section id="important-info" class="pt-[43px]" @if (blank($activity['important_notes'])) hidden @endif>
                <h2 data-reveal class="text-[32px] font-semibold leading-[42.7px] text-editorial-ink">Important Info</h2>

                <ul data-reveal class="mt-[32px] flex flex-col gap-[12px]">
                    @foreach (preg_split('/\R+/', trim((string) $activity['important_notes'])) ?: [] as $note)
                        @continue(blank($note))

                        <li class="flex items-start gap-[10.7px] text-[21.4px] leading-[32px] text-editorial-body">
                            <img src="{{ asset('images/icons/detail/info.svg') }}" alt="" class="mt-[6px] size-[20px] shrink-0">
                            {{ ltrim($note, "-• \t") }}
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        @include('partials.activity.booking-card', ['activity' => $activity])
    </div>

    {{-- Figma node 1:1454 — other activities carousel --}}
    <section class="container-page pt-[54px] lg:pt-[128px] pb-[31px] lg:pb-[74px]">
        <div data-reveal class="flex items-end justify-between gap-4">
            <h2 class="font-jakarta text-[36px] font-bold leading-[44px] tracking-[-0.36px] text-editorial-ink">
                Other Activities You Might Like
            </h2>

            <a href="{{ route('activities.index') }}" class="flex shrink-0 items-center gap-[5.3px] text-[21.4px] leading-[32px] text-brand">
                See All
                <img src="{{ asset('images/icons/detail/see-all-arrow.svg') }}" alt="" class="size-[16px]">
            </a>
        </div>

        {{-- Scrolls horizontally, matching the Figma carousel row. --}}
        <div class="mt-[37px] lg:mt-[87px] flex items-stretch gap-[32px] overflow-x-auto pb-4">
            @foreach ($related as $index => $item)
                @include('components.activity-mini-card', ['activity' => $item, 'delay' => $index * 90])
            @endforeach
        </div>
    </section>
    </div>
@endsection
