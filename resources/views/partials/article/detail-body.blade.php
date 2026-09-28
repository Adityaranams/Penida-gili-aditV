{{-- Figma nodes 1:2466 / 1:2473 / 1:2536 / 1:2607 / 1:2660 — the article body sections.

     Every section shares one shell: a white rounded card with a numbered badge
     and a heading, so `card` and `section-heading` are declared once here. --}}
@props(['article'])

@php
    $card = 'rounded-editorial border border-[rgba(229,233,235,0.6)] bg-surface p-[42.7px] shadow-editorial';
    $toneClass = [
        'brand' => 'text-editorial',
        'muted' => 'text-[#546066]',
        'alert' => 'text-[#ba1a1a]',
    ];
@endphp

<div class="flex flex-col gap-[52px] font-jakarta">
    {{-- Lead introduction (1:2466). Only the seeded long-form articles carry one. --}}
    @if (filled($article['lead']))
        <div data-reveal class="{{ $card }}">
            <p class="text-[23.3px] leading-[37.8px] text-editorial-ink
                      first-letter:float-left first-letter:pr-[10px] first-letter:text-[44px] first-letter:font-bold first-letter:leading-[0.9] lg:first-letter:text-[62px]">
                {{ $article['lead'] }}
            </p>

            @if (filled($article['lead_follow']))
                <p class="mt-[20.7px] text-[20.7px] leading-[33.6px] text-editorial-body">{{ $article['lead_follow'] }}</p>
            @endif
        </div>
    @endif

    @if ($article['has_structured_content'])
    {{-- Section 1 — port comparison table (1:2473) --}}
    <section id="section-1" data-reveal class="{{ $card }}">
        <h2 class="flex items-center gap-[15.5px]">
            <span class="flex size-[41.4px] shrink-0 items-center justify-center rounded-[10px] bg-[#d2e4ff] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial">01</span>
            <span class="text-[31px] font-semibold leading-[41.4px] text-editorial-ink">Port of Departures: Sanur vs Kusamba vs Padang Bai</span>
        </h2>

        <p class="mt-[20.7px] text-[20.7px] leading-[33.6px] text-editorial-body">
            Bali offers three primary gateways to Nusa Penida, but they cater to vastly different itineraries
            and comfort requirements. Here is how they compare in distance, passenger safety, and overall convenience:
        </p>

        {{-- Wide table scrolls inside its own box rather than pushing the page sideways. --}}
        <div class="mt-[20.7px] overflow-x-auto rounded-[15.5px] border border-editorial-rule">
            <table class="w-full min-w-[760px] border-collapse text-left">
                <thead class="bg-[#f1f4f6]">
                    <tr>
                        @foreach ($article['ports']['headers'] as $header)
                            <th scope="col" class="border-b border-editorial-rule p-[20.7px] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial-ink">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($article['ports']['rows'] as $row)
                        <tr class="border-t border-editorial-rule first:border-t-0">
                            <th scope="row" @class([
                                'p-[20.7px] text-[20.7px] font-semibold leading-[31px]',
                                'text-editorial' => ! empty($row['highlight']),
                                'text-editorial-ink' => empty($row['highlight']),
                            ])>{{ $row[0] }}</th>
                            <td class="p-[20.7px] text-[20.7px] leading-[31px] text-editorial-ink">{{ $row[1] }}</td>
                            <td class="p-[20.7px] text-[20.7px] leading-[31px] text-editorial-body">{{ $row[2] }}</td>
                            <td class="p-[20.7px] text-[20.7px] leading-[31px] text-editorial-body">
                                @if (! empty($row['highlight']))
                                    <span class="flex items-center gap-[5px] text-[16.8px] font-medium text-editorial">
                                        <img src="{{ asset('images/icons/article-detail/check.svg') }}" alt="" class="size-[17.2px]">
                                        {{ $row[3] }}
                                    </span>
                                @else
                                    {{ $row[3] }}
                                @endif
                            </td>
                            <td class="p-[20.7px] text-[20.7px] leading-[31px] text-editorial-body">{{ $row[4] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="mt-[20.7px] flex items-start gap-[15.5px] rounded-[15.5px] border border-[#d5e2e9] bg-[rgba(213,226,233,0.4)] px-[22px] pb-[22px] pt-[32px] text-[18.1px] leading-[26px] tracking-[0.9px]">
            <img src="{{ asset('images/icons/article-detail/insight.svg') }}" alt="" class="h-[24px] w-[21.5px] shrink-0">
            <span class="font-semibold text-editorial-ink">
                <strong class="font-extrabold">Local Insight:</strong>
                Since the completion of the new Sanur Harbor terminal pier, passengers no longer need to wade
                knee-deep into the seawater to board boats. You walk straight onto the deck via modern floating gangways!
            </span>
        </p>
    </section>

    {{-- Section 2 — timetable (1:2536) --}}
    <section id="section-2" data-reveal class="{{ $card }}">
        <h2 class="flex items-center gap-[15.5px]">
            <span class="flex size-[41.4px] shrink-0 items-center justify-center rounded-[10px] bg-[#d2e4ff] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial">02</span>
            <span class="text-[31px] font-semibold leading-[41.4px] text-editorial-ink">Standard Fast Boat Timetable &amp; Crossing Durations</span>
        </h2>

        <p class="mt-[20.7px] text-[20.7px] leading-[33.6px] text-editorial-body">
            Sanjaya Fastboat operates five daily express sailings between Sanur and Banjar Nyuh Harbour.
            Every vessel features high-thrust quadri-engine setups reaching 35 knots, equipped with GPS
            navigational radars, AIS tracking, and complete Solas-certified life rafts.
        </p>

        <div class="mt-[20.7px] grid [&>*]:min-w-0 gap-[20.7px] lg:grid-cols-2">
            @foreach ($article['timetables'] as $table)
                <div class="rounded-[15.5px] border border-editorial-rule bg-[rgba(241,244,246,0.5)] p-[27px]">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="flex items-center gap-[10px] text-[20.7px] font-bold leading-[31px] text-editorial-ink">
                            <img src="{{ asset('images/icons/article-detail/'.$table['icon']) }}" alt="" class="size-[23.7px] object-contain">
                            {{ $table['route'] }}
                        </h3>
                        <span @class([
                            'shrink-0 rounded-[5px] px-[10px] py-[3px] text-[15.5px] font-semibold leading-[23.3px]',
                            'bg-[#d2e4ff] text-[#001d37]' => $table['badgeTone'] === 'blue',
                            'bg-[#d8e4eb] text-[#111d22]' => $table['badgeTone'] === 'grey',
                        ])>{{ $table['badge'] }}</span>
                    </div>

                    <ul class="mt-[15.5px] flex flex-col gap-[13px]">
                        @foreach ($table['sailings'] as $sailing)
                            <li class="flex items-center justify-between gap-2 rounded-[10px] bg-surface p-[10.3px]">
                                <span class="text-[20.7px] font-medium leading-[31px] text-editorial-ink">{{ $sailing['depart'] }}</span>
                                <span class="text-[18.1px] leading-[31px] text-editorial-body">{{ $sailing['arrive'] }}</span>
                                <span class="text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] {{ $toneClass[$sailing['tone']] }}">{{ $sailing['note'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <p class="mt-[20.7px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body">
            * Schedules are subject to slight changes during heavy tides or harbour master advisories (Syahbandar).
            Check live board status prior to arrival.
        </p>
    </section>

    {{-- Section 3 — luggage + pull quote (1:2607) --}}
    <section id="section-3" data-reveal class="{{ $card }}">
        <h2 class="flex items-center gap-[15.5px]">
            <span class="flex size-[41.4px] shrink-0 items-center justify-center rounded-[10px] bg-[#d2e4ff] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial">03</span>
            <span class="text-[31px] font-semibold leading-[41.4px] text-editorial-ink">Luggage Policies &amp; Boarding Tips</span>
        </h2>

        <p class="mt-[20.7px] text-[20.7px] leading-[33.6px] text-editorial-body">
            Unlike budget airlines with strict millimeter baggage gauges, fast boat travel in Bali is relatively
            generous, though passenger safety requires strict adherence to deck weight distributions:
        </p>

        <div class="mt-[20.7px] grid [&>*]:min-w-0 gap-[20.7px] sm:grid-cols-3">
            @foreach ($article['luggage'] as $item)
                <div class="flex flex-col items-center gap-[5px] rounded-[15.5px] border border-editorial-rule bg-[#f1f4f6] p-[22px] text-center">
                    <img src="{{ asset('images/icons/article-detail/'.$item['icon']) }}" alt="" class="h-[34.5px] w-[27.6px] object-contain">
                    <h3 class="pt-[5px] text-[20.7px] font-bold leading-[31px] text-editorial-ink">{{ $item['title'] }}</h3>
                    <p class="text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body">{{ $item['body'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Pull quote (1:2637) --}}
        <blockquote class="relative mt-[20.7px] overflow-hidden rounded-editorial bg-gradient-to-r from-[#005ea1] to-[#2178c3] px-[41.4px] pb-[41.4px] pt-[26px] lg:pt-[62px] shadow-xl">
            <img src="{{ asset('images/icons/article-detail/quote-deco.svg') }}" alt=""
                 class="pointer-events-none absolute -bottom-[42px] -right-[41px] h-[103px] w-[147px]" aria-hidden="true">

            <div class="relative flex items-start gap-[20.7px]">
                <img src="{{ asset('images/icons/article-detail/quote-mark.svg') }}" alt="" class="size-[61px] shrink-0">
                <div class="flex flex-col gap-[5px] pt-[6px]">
                    <p class="text-[15.5px] font-extrabold uppercase leading-[23.3px] tracking-[0.78px] text-[#d2e4ff]">Pro tip from local skippers</p>
                    <p class="text-[31px] font-semibold leading-[41.4px] text-white">
                        &ldquo;Always book morning departures between 07:30 and 09:00 AM. The Badung Strait sea condition is
                        glass-flat, the morning ocean breeze is revitalizing, and you get magnificent, crystal-clear vistas
                        of Mount Agung looming above Bali&rsquo;s coastline.&rdquo;
                    </p>
                    <footer class="pt-[10px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-white/80">
                        &mdash; Captain Wayan Sudira, 18 Years Maritime Master on Badung Strait
                    </footer>
                </div>
            </div>
        </blockquote>

        <ul class="mt-[20.7px] flex flex-col gap-[15.5px]">
            @foreach ($article['advice'] as $item)
                <li class="flex items-start gap-[15.5px]">
                    <img src="{{ asset('images/icons/article-detail/'.$item['icon']) }}" alt="" class="h-[26.7px] w-[21.5px] shrink-0">
                    <p class="text-[20.7px] leading-[31px] text-editorial-body">
                        <strong class="font-bold text-editorial-ink">{{ $item['lead'] }}</strong>
                        {{ $item['body'] }}
                    </p>
                </li>
            @endforeach
        </ul>
    </section>

    {{-- Section 4 — arrival (1:2660) --}}
    <section id="section-4" data-reveal class="{{ $card }}">
        <h2 class="flex items-center gap-[15.5px]">
            <span class="flex size-[41.4px] shrink-0 items-center justify-center rounded-[10px] bg-[#d2e4ff] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial">04</span>
            <span class="text-[31px] font-semibold leading-[41.4px] text-editorial-ink">Arriving at Banjar Nyuh / Buyuk Port</span>
        </h2>

        <p class="mt-[20.7px] text-[20.7px] leading-[33.6px] text-editorial-body">
            Most express fast boats, including Sanjaya Fastboat, moor directly at Banjar Nyuh Harbour on the western
            coast of Nusa Penida. This is the optimal entry point because of its paved road connections towards both
            the famous Western highlights (Kelingking Beach, Broken Beach, Angel&rsquo;s Billabong) and East Nusa Penida
            (Diamond Beach).
        </p>

        <div class="mt-[20.7px] grid [&>*]:min-w-0 gap-[20.7px] sm:grid-cols-2">
            @foreach ($article['arrival'] as $item)
                <div class="rounded-[15.5px] border border-editorial-rule bg-[rgba(241,244,246,0.3)] p-[27px]">
                    <h3 class="flex items-center gap-[10px] text-[20.7px] font-bold leading-[31px] text-editorial-ink">
                        <img src="{{ asset('images/icons/article-detail/'.$item['icon']) }}" alt="" class="h-[21.5px] w-[26px] object-contain">
                        {{ $item['title'] }}
                    </h3>
                    <p class="mt-[10px] text-[20.7px] leading-[33.6px] text-editorial-body">{{ $item['body'] }}</p>
                </div>
            @endforeach
        </div>

        <p class="mt-[20.7px] flex items-center gap-[15.5px] rounded-[15.5px] border border-[rgba(192,199,211,0.5)] bg-[#f1f4f6] px-[22px] pb-[22px] pt-[32px]">
            <img src="{{ asset('images/icons/article-detail/ticket.svg') }}" alt="" class="h-[20.7px] w-[28.4px] shrink-0">
            <span>
                <strong class="block text-[20.7px] font-bold leading-[31px] text-editorial-ink">Klungkung Regency Tourism Retribution Fee</strong>
                <span class="block text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body">
                    IDR 25,000 (Adult) / IDR 15,000 (Child) collected at the harbor gate. Keep your official stamped ticket!
                </span>
            </span>
        </p>
    </section>

    @else
        {{-- Written in the console editor, stored as a safe subset of HTML. Same white
             card as the seeded sections, so the copy never floats outside it. --}}
        <div data-reveal class="{{ $card }} rich-text prose-article text-[17px] lg:text-[20.7px] leading-[28px] lg:leading-[33.6px] text-editorial-body">
            {!! $article->body_html !!}
        </div>
    @endif
    {{-- Tags & save (1:2693) --}}
    <div data-reveal class="flex flex-col gap-[20.7px] pt-[20.7px]">
        <div class="flex flex-wrap items-center gap-[10px]">
            <span class="text-[18.1px] font-medium leading-[26px] tracking-[0.9px] text-editorial-body">Tags:</span>
            @foreach ($article['tags'] as $tag)
                <a href="#" class="rounded-full bg-editorial-rule px-[15.5px] py-[5px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-ink
                                   transition-colors duration-300 hover:bg-[#d2e4ff] hover:text-editorial">{{ $tag }}</a>
            @endforeach
        </div>

    </div>

    {{-- Author bio (1:2711) — filled from the author record, hidden until one is written. --}}
    @if (filled($article->writer?->bio))
        <div data-reveal class="flex flex-col gap-[31px] rounded-editorial border border-editorial-rule bg-surface p-[42.7px] shadow-editorial sm:flex-row sm:items-start">
            <span class="flex size-[103.4px] shrink-0 items-center justify-center overflow-hidden rounded-editorial bg-[#d5e2e9] text-[34px] font-bold text-[#58646a] ring-[5px] ring-[rgba(210,228,255,0.4)]">
                @if ($article->writer->photo_url)
                    <img src="{{ $article->writer->photo_url }}" alt="{{ $article['author'] }}" class="size-full object-cover">
                @else
                    {{ $article->writer->initials }}
                @endif
            </span>

            <div class="flex min-w-0 flex-col gap-[10px]">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="break-words text-[31px] font-bold leading-[41.4px] text-editorial-ink">{{ $article['author'] }}</h2>
                        @if (filled($article->writer->role))
                            <p class="break-words text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial">{{ $article->writer->role }}</p>
                        @endif
                    </div>

                    @if (filled($article->writer->credential))
                        <span class="flex items-center gap-[5px] rounded-[5px] bg-[#d8e4eb] px-[13px] py-[5px] text-[15.5px] font-medium leading-[23.3px] text-[#111d22]">
                            <img src="{{ asset('images/icons/article-detail/certified.svg') }}" alt="" class="h-[15.8px] w-[16.6px]">
                            {{ $article->writer->credential }}
                        </span>
                    @endif
                </div>

                {{-- A pasted bio can be one unbroken string; wrap it rather than widen the card. --}}
                <p class="break-words text-[20.7px] leading-[33.6px] text-editorial-body">{{ $article->writer->bio }}</p>
            </div>
        </div>
    @endif
</div>
