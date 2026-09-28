{{-- Figma node 1:2726 — sticky sidebar: TOC, popular articles, assistance --}}
@props(['article', 'popular' => []])

<aside data-reveal style="--reveal-delay: 120ms" class="flex flex-col gap-[41px] font-jakarta lg:sticky lg:top-8">
    {{-- Figma node 1:2727 — built from the headings in the article itself. --}}
    @if ($article->toc_items)
        <nav class="rounded-editorial border border-editorial-rule bg-surface p-[32px] shadow-editorial" aria-label="Table of contents">
            <h2 class="flex items-center gap-[10px] text-[20.7px] font-bold leading-[31px] text-editorial-ink">
                <img src="{{ asset('images/icons/article-detail/toc.svg') }}" alt="" class="size-[19.4px]">
                Table of Contents
            </h2>

            <ul class="mt-[15.5px] flex flex-col gap-[10px]">
                @foreach ($article->toc_items as $item)
                    <li>
                        {{-- Every entry starts at the same edge, whatever heading level it came from. --}}
                        <a href="#{{ $item['anchor'] }}"
                           class="block rounded-[10px] px-[13px] py-[8px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body
                                  transition-colors duration-300 hover:bg-editorial/5 hover:text-editorial">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif

    {{-- Figma node 1:2741 — the most-read published articles, so it fills up as the blog grows. --}}
    @if (count($popular))
        <section class="rounded-editorial border border-editorial-rule bg-surface p-[32px] shadow-editorial">
            <h2 class="flex items-center gap-[10px] text-[20.7px] font-bold leading-[31px] text-editorial-ink">
                <img src="{{ asset('images/icons/article-detail/popular.svg') }}" alt="" class="h-[13px] w-[21.5px]">
                Popular Articles
            </h2>

            <ul class="mt-[20.7px] flex flex-col gap-[20.7px]">
                @foreach ($popular as $item)
                    <li>
                        <a href="{{ route('articles.show', $item) }}" class="group flex items-center gap-[18px]">
                            <span class="size-[82.75px] shrink-0 overflow-hidden rounded-[15.5px] bg-editorial-rule">
                                <img src="{{ $item->image_url }}" alt=""
                                     class="size-full object-cover transition-transform duration-500 ease-smooth group-hover:scale-105">
                            </span>
                            <span class="min-w-0">
                                <span class="block text-[15.5px] font-semibold leading-[23.3px] text-editorial">{{ $item->category }}</span>
                                <span class="block break-words pb-[6px] text-[18.1px] font-bold leading-[26px] tracking-[0.9px] text-editorial-ink transition-colors duration-300 group-hover:text-editorial">{{ $item->title }}</span>
                                <span class="block text-[15.5px] leading-[23.3px] text-editorial-body">{{ $item->read_time_label }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- Figma node 1:2774 --}}
    <section class="rounded-editorial border border-[#d5e2e9] bg-[rgba(213,226,233,0.4)] p-[32px]">
        <div class="flex items-center gap-[15.5px]">
            <span class="flex size-[51.7px] shrink-0 items-center justify-center rounded-full bg-editorial">
                <img src="{{ asset('images/icons/article-detail/support.svg') }}" alt="" class="h-[19.4px] w-[21.5px]">
            </span>
            <span>
                <span class="block text-[20.7px] font-bold leading-[31px] text-editorial-ink">Need Trip Assistance?</span>
                <span class="block text-[15.5px] leading-[23.3px] text-editorial-body">24/7 Harbour Support Team</span>
            </span>
        </div>

        <p class="pb-[10px] pt-[10px] text-[20.7px] leading-[31px] text-editorial-body">
            Have questions about private boat charters, group discounts, or sea conditions? Chat directly with our port master.
        </p>

        <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
           class="flex items-center justify-center gap-[10px] rounded-[15.5px] border border-[#c0c7d3] bg-surface px-[22px] py-[14px] text-[20.7px] font-bold leading-[31px] text-editorial
                  transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
            <img src="{{ asset('images/icons/article-detail/whatsapp.svg') }}" alt="" class="size-[21.5px]">
            WhatsApp Port Desk
        </a>
    </section>
</aside>
