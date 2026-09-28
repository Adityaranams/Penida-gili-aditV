{{-- Figma node 1:6261 — "article detail full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['article'])

@php
    $portIcons = ['port-sanur.svg', 'port-kusamba.svg', 'port-padangbai.svg'];
    $portBlurbs = [
        'Best for travelers staying in Kuta, Seminyak, Canggu, or Ubud. Modern terminal with air-conditioned boarding bridges.',
        'Closest straight-line distance across the channel. Ideal if you are departing from Sidemen, Candidasa, or East Bali.',
        'Recommended primarily if you need to transport your personal motorcycle or cargo via large roll-on/roll-off car ferry.',
    ];
    $sectionTitles = [
        'Departure Ports: Sanur vs Kusamba vs Padang Bai',
        'Standard Fast Boat Timetable',
        'Luggage Policies & Boarding Tips',
        'Arriving at Banjar Nyuh / Buyuk Port',
    ];
@endphp

<div class="bg-[#f7fafc] font-jakarta lg:hidden">
    <div class="flex flex-col gap-[16px] px-[20px] pb-[32px] pt-[16px]">
        {{-- Header meta (1:6306) --}}
        <header data-reveal class="flex flex-col gap-[8px]">
            <div class="flex items-center gap-[8px]">
                <span class="rounded-full bg-[#d5e2e9] px-[12px] py-[4px] text-[12px] font-semibold uppercase leading-[24px] tracking-[0.3px] text-[#58646a]">{{ $article['category'] }}</span>
                <span class="flex items-center gap-[4px] text-[13px] font-medium leading-[24px] text-[#717782]">
                    <img src="{{ asset('images/icons/mobile/article/clock.svg') }}" alt="" class="size-[12.5px]">
                    {{ $article['readTime'] }} • {{ $article['date'] }}
                </span>
            </div>

            <h1 class="pt-[4px] text-[28px] font-bold leading-[35px] text-[#005ea1]">{{ $article['title'] }}</h1>
            <p class="text-[16px] leading-[26px] text-[#414751]">{{ $article['subtitle'] }}</p>

            <div class="flex items-center justify-between border-b border-[#e0e3e5] pb-[5px] pt-[8px]">
                <span class="flex items-center gap-[12px]">
                    <span class="flex size-[40px] shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#d2e4ff] bg-[#d5e2e9] text-[14px] font-bold text-[#58646a]">
                        @if ($article->writer?->photo_url)
                            <img src="{{ $article->writer->photo_url }}" alt="{{ $article['author'] }}" class="size-full object-cover">
                        @else
                            {{ $article->writer?->initials ?? mb_strtoupper(mb_substr($article['author'], 0, 1)) }}
                        @endif
                    </span>
                    <span>
                        <span class="block text-[14px] font-bold leading-[24px] text-[#181c1e]">{{ $article['author'] }}</span>
                        @if (filled($article['author_role']))
                            <span class="block text-[12px] leading-[24px] text-[#717782]">{{ $article['author_role'] }}</span>
                        @endif
                    </span>
                </span>
            </div>
        </header>

        {{-- Hero image (1:6331) --}}
        <figure data-reveal class="relative h-[230px] overflow-hidden rounded-[16px] bg-[#f1f4f6] shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)]">
            <img src="{{ $article['image_url'] }}" alt="{{ $article['hero_alt'] ?: $article['title'] }}" class="size-full object-cover">
            <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[rgba(24,28,30,0.7)] via-transparent to-transparent"></span>
            @if (filled($article['hero_caption']))
                <figcaption class="absolute inset-x-[12px] bottom-[12px]">
                    <span class="flex min-w-0 items-center gap-[6px] rounded-[6px] bg-[rgba(24,28,30,0.4)] px-[10px] py-[4px] text-[12px] font-medium leading-[24px] text-white backdrop-blur-[2px]">
                        <img src="{{ asset('images/icons/mobile/article/camera.svg') }}" alt="" class="h-[11.3px] w-[12.5px] shrink-0">
                        <span class="truncate">{{ $article['hero_caption'] }}</span>
                    </span>
                </figcaption>
            @endif
        </figure>

        {{-- Article body --}}
        <article class="flex flex-col gap-[24px] pt-[8px]">
            {{-- Only the seeded long-form articles carry a separate lead paragraph. --}}
            @if (filled($article['lead']))
                <p data-reveal class="text-[18px] leading-[29.25px] text-[#414751]">
                    <span class="float-left mr-[6px] text-[48px] font-bold leading-[48px] text-[#005ea1]">{{ mb_substr($article['lead'], 0, 1) }}</span>{{ mb_substr($article['lead'], 1) }}
                </p>
            @endif

            @if ($article['has_structured_content'])
            {{-- 01 Ports --}}
            <section id="m-section-1" class="flex flex-col gap-[12px]">
                <h2 data-reveal class="flex items-start gap-[8px] text-[20px] font-bold leading-[32.5px] text-[#181c1e]">
                    <span class="mt-[4px] flex h-[24px] shrink-0 items-center justify-center rounded-[6px] bg-[#005ea1] px-[6px] text-[12px] leading-[19.5px] text-white">1</span>
                    {{ $sectionTitles[0] }}
                </h2>
                <p class="text-[16px] leading-[24px] text-[#414751]">Choosing the right harbor depends predominantly on your starting location in Bali and your tolerance for open sea crossing times.</p>

                <div class="flex flex-col gap-[12px] pt-[4px]">
                    @foreach ($article['ports']['rows'] as $index => $row)
                        <div data-reveal style="--reveal-delay: {{ $index * 60 }}ms" class="rounded-[12px] border border-[rgba(192,199,211,0.4)] bg-white p-[17px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                            <div class="flex items-center justify-between gap-[8px]">
                                <span class="flex items-center gap-[8px]">
                                    <img src="{{ asset('images/icons/mobile/article/'.$portIcons[$index]) }}" alt="" class="h-[16.7px] w-[16.7px] shrink-0 object-contain">
                                    <span class="text-[15px] font-bold leading-[24.4px] text-[#181c1e]">{{ $row[0] }}</span>
                                </span>
                                <span class="shrink-0 rounded-[4px] bg-[#d8e4eb] px-[8px] py-[2px] text-center text-[11px] font-bold leading-[17.9px] text-[#3c494e]">{{ $row[1] }}</span>
                            </div>
                            <p class="mt-[8px] text-[13px] leading-[21.1px] text-[#414751]">{{ $portBlurbs[$index] }}</p>
                            @if ($loop->first)
                                <p class="mt-[8px] flex flex-wrap gap-[12px] text-[12px] font-medium leading-[19.5px] text-[#717782]">
                                    <span class="flex items-center gap-[4px]"><img src="{{ asset('images/icons/mobile/article/check.svg') }}" alt="" class="size-[12.5px]">Modern Pier</span>
                                    <span class="flex items-center gap-[4px]"><img src="{{ asset('images/icons/mobile/article/check.svg') }}" alt="" class="size-[12.5px]">25+ Daily Boats</span>
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 02 Timetable --}}
            <section id="m-section-2" class="flex flex-col gap-[12px]">
                <h2 data-reveal class="flex items-start gap-[8px] text-[20px] font-bold leading-[32.5px] text-[#181c1e]">
                    <span class="mt-[4px] flex size-[24px] shrink-0 items-center justify-center rounded-[6px] bg-[#005ea1] text-[12px] leading-[19.5px] text-white">2</span>
                    {{ $sectionTitles[1] }}
                </h2>
                <p class="text-[16px] leading-[24px] text-[#414751]">Boats depart in synchronized waves throughout the day. Below are the verified primary departures:</p>

                <div data-reveal class="flex flex-col gap-[16px] rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-white p-[17px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                    @foreach ($article['timetables'] as $tIndex => $timetable)
                        <div class="flex flex-col gap-[10px]">
                            <div class="flex items-center justify-between border-b border-[#ebeef0] pb-[9px]">
                                <span class="flex items-center gap-[8px] text-[13px] font-bold leading-[21.1px] {{ $tIndex === 0 ? 'text-[#005ea1]' : 'text-[#181c1e]' }}">
                                    <img src="{{ asset('images/icons/mobile/article/'.($tIndex === 0 ? 'route-out.svg' : 'route-return.svg')) }}" alt="" class="size-[14px] object-contain">
                                    {{ $timetable['route'] }}
                                </span>
                                <span class="text-[11px] font-semibold leading-[17.9px] text-[#717782]">{{ $timetable['badge'] }}</span>
                            </div>
                            <div class="flex gap-[8px]">
                                @foreach ($timetable['sailings'] as $sailing)
                                    @php $hot = $sailing['tone'] !== 'muted'; @endphp
                                    <div @class([
                                        'flex min-w-0 flex-1 flex-col items-center gap-[5.5px] rounded-[8px] border px-[9px] pb-[11.5px] pt-[9px] text-center',
                                        'border-[rgba(0,94,161,0.2)] bg-[rgba(0,94,161,0.1)]' => $hot,
                                        'border-[rgba(192,199,211,0.2)] bg-[#f1f4f6]' => ! $hot,
                                    ])>
                                        <span @class(['text-[14px] font-bold leading-[22.75px]', 'text-[#005ea1]' => $hot, 'text-[#181c1e]' => ! $hot])>{{ $sailing['depart'] }}</span>
                                        <span @class(['text-[11px] leading-[17.9px]', 'font-semibold text-[#005ea1]' => $hot, 'text-[#717782]' => ! $hot])>{{ $sailing['note'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- 03 Luggage --}}
            <section id="m-section-3" class="flex flex-col gap-[12px]">
                <h2 data-reveal class="flex items-start gap-[8px] text-[20px] font-bold leading-[32.5px] text-[#181c1e]">
                    <span class="mt-[4px] flex h-[24px] shrink-0 items-center justify-center rounded-[6px] bg-[#005ea1] px-[6px] text-[12px] leading-[19.5px] text-white">3</span>
                    {{ $sectionTitles[2] }}
                </h2>
                <div class="flex flex-col gap-[10px]">
                    @foreach ($article['luggage'] as $index => $item)
                        <div data-reveal style="--reveal-delay: {{ $index * 60 }}ms" class="flex items-start gap-[12px] rounded-[12px] bg-[#f1f4f6] p-[12px]">
                            <img src="{{ asset('images/icons/article-detail/'.$item['icon']) }}" alt="" class="mt-[2px] h-[20px] w-[18px] shrink-0 object-contain">
                            <p>
                                <span class="block text-[14px] font-bold leading-[22.75px] text-[#181c1e]">{{ $item['title'] }}</span>
                                <span class="block text-[13px] leading-[21.1px] text-[#414751]">{{ $item['body'] }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Pro tip quote (1:6479) --}}
            <blockquote data-reveal class="relative flex flex-col gap-[8px] overflow-hidden rounded-[16px] bg-[#005ea1] p-[20px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)]">
                <img src="{{ asset('images/icons/mobile/article/quote-deco.svg') }}" alt="" class="pointer-events-none absolute -bottom-[23px] -right-[16px] h-[50px] w-[71px]" aria-hidden="true">
                <p class="flex items-center gap-[8px] text-[12px] font-bold uppercase leading-[19.5px] tracking-[0.6px] text-[#d2e4ff]">
                    <img src="{{ asset('images/icons/mobile/article/quote.svg') }}" alt="" class="h-[18.3px] w-[18.8px]">
                    Captain's Insider Advice
                </p>
                <p class="pb-[4px] text-[14px] italic leading-[22.75px] text-[#9fcaff]">{{ $article['advice'][1]['body'] ?? $article['excerpt'] }}</p>
                <footer class="flex items-center gap-[10px] border-t border-[#2178c3] pt-[13px]">
                    @if ($article->writer?->photo_url)
                        <img src="{{ $article->writer->photo_url }}" alt="" class="size-[28px] rounded-full border border-[#d2e4ff] object-cover p-px">
                    @endif
                    <span class="text-[12px] font-medium leading-[19.5px] text-white">{{ $article['author'] }} • 14 Years Strait Navigation</span>
                </footer>
            </blockquote>

            {{-- 04 Arrival --}}
            <section id="m-section-4" class="flex flex-col gap-[12px]">
                <h2 data-reveal class="flex items-start gap-[8px] text-[20px] font-bold leading-[32.5px] text-[#181c1e]">
                    <span class="mt-[4px] flex h-[24px] shrink-0 items-center justify-center rounded-[6px] bg-[#005ea1] px-[6px] text-[12px] leading-[19.5px] text-white">4</span>
                    {{ $sectionTitles[3] }}
                </h2>
                <p class="text-[16px] leading-[24px] text-[#414751]">Fast boats dock at either Banjar Nyuh or Buyuk Harbour. Upon stepping off the gangway, the arrival concourse has well-organized transportation desks:</p>
                <ul class="flex flex-col gap-[8px] pl-[4px]">
                    @foreach ($article['arrival'] as $item)
                        <li class="flex items-start gap-[8px] text-[14px] leading-[22.75px] text-[#414751]">
                            <img src="{{ asset('images/icons/article-detail/'.$item['icon']) }}" alt="" class="mt-[5px] h-[13px] w-[15px] shrink-0 object-contain">
                            <span><strong class="font-bold">{{ $item['title'] }}:</strong> {{ $item['body'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            @else
                {{-- Written in the console editor, stored as a safe subset of HTML. --}}
                <div data-reveal class="rich-text prose-article text-[17px] leading-[28px] text-editorial-body">
                    {!! $article->mobile_body_html !!}
                </div>
            @endif
            {{-- Tags (1:6512) --}}
            <ul class="flex flex-wrap gap-[8px] py-[8px]">
                @foreach (array_slice($article['tags'], 0, 4) as $tag)
                    <li class="rounded-full bg-[#ebeef0] px-[12px] py-[4px] text-[12px] font-medium leading-[19.5px] text-[#414751]">{{ $tag }}</li>
                @endforeach
            </ul>

            {{-- Author bio (1:6522) — filled from the author record, hidden until one is written. --}}
            @if (filled($article->writer?->bio))
                <div data-reveal class="flex items-center gap-[12px] rounded-[16px] border border-[rgba(192,199,211,0.3)] bg-white p-[17px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                    <span class="flex size-[56px] shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-[#005ea1] bg-[#d5e2e9] text-[18px] font-bold text-[#58646a]">
                        @if ($article->writer->photo_url)
                            <img src="{{ $article->writer->photo_url }}" alt="{{ $article['author'] }}" class="size-full object-cover">
                        @else
                            {{ $article->writer->initials }}
                        @endif
                    </span>
                    <div class="min-w-0">
                        <p class="text-[15px] font-bold leading-[24.4px] text-[#181c1e]">{{ $article['author'] }}</p>
                        @if (filled($article->writer->role))
                            <p class="text-[12px] leading-[19.5px] text-[#717782]">{{ $article->writer->role }}</p>
                        @endif
                        <p class="break-words pt-[4px] text-[12px] leading-[19.5px] text-[#414751]">{{ $article->writer->bio }}</p>
                    </div>
                </div>
            @endif

            {{-- Related (1:6533) --}}
            <section class="flex flex-col gap-[12px] pt-[8px]">
                <div class="flex items-center justify-between">
                    <h2 class="text-[18px] font-bold leading-[29.25px] text-[#181c1e]">Related Articles</h2>
                    <a href="{{ route('articles.index') }}" class="text-[13px] font-bold leading-[21.1px] text-[#005ea1]">View All</a>
                </div>
                <div class="flex flex-col gap-[12px]">
                    @foreach ($related->take(2) as $index => $item)
                        <a href="{{ route('articles.show', $item) }}" data-reveal style="--reveal-delay: {{ $index * 60 }}ms" class="flex items-start gap-[12px] rounded-[12px] border border-[rgba(192,199,211,0.3)] bg-white p-[13px]">
                            <img src="{{ $item['image_url'] }}" alt="" class="size-[80px] shrink-0 rounded-[8px] object-cover">
                            <span class="min-w-0">
                                <span class="block text-[11px] font-bold uppercase leading-[17.9px] tracking-[0.55px] text-[#005ea1]">{{ $item['category'] }}</span>
                                <span class="line-clamp-2 text-[14px] font-bold leading-[19.25px] text-[#181c1e]">{{ $item['title'] }}</span>
                                <span class="block pt-[4px] text-[12px] leading-[19.5px] text-[#717782]">{{ $item['readTime'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        </article>
    </div>
</div>
