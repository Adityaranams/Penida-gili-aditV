{{-- Figma node 1:5965 — "article full" (mobile, 390px). Rendered below lg only; the desktop layout is hidden there. --}}
@props(['featured', 'articles', 'categories' => [], 'category' => '', 'term' => ''])

@php
    $initials = fn (string $name) => collect(explode(' ', preg_replace('/^Capt(ain)?\.?\s+/i', '', $name)))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
@endphp

<div class="bg-[#f7fafc] font-jakarta lg:hidden">
    {{-- Header title (1:6010) --}}
    <section data-reveal class="flex flex-col gap-[8px] px-[20px] pb-[16px] pt-[24px]">
        <h1 class="text-[28px] font-bold leading-[36px] tracking-[-0.7px] text-[#181c1e]">Travel Articles &amp; Island Guides</h1>
        <p class="text-[16px] leading-[26px] text-[#414751]">Inspiration, guides, and tips for your fast boat travel and island adventures across Bali &amp; Nusa Penida.</p>
    </section>

    {{-- Search + filter pills (1:6019) --}}
    <section class="pb-[20px]">
        <form action="{{ route('articles.index') }}" method="get" class="relative px-[20px]">
            @if ($category)
                <input type="hidden" name="category" value="{{ $category }}">
            @endif
            <img src="{{ asset('images/icons/mobile/article/search.svg') }}" alt="" class="pointer-events-none absolute left-[37px] top-1/2 size-[18px] -translate-y-1/2">
            <input type="search" name="q" value="{{ $term }}" data-article-search placeholder="Search guides, ports, tips..."
                   class="h-[46px] w-full rounded-[12px] border border-[#c0c7d3] bg-white pl-[44px] pr-[16px] text-[16px] text-[#181c1e] shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)] placeholder:text-[#717782] focus:border-brand focus:outline-none">
        </form>

        <div class="flex gap-[8px] overflow-x-auto px-[20px] pt-[16px] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach (array_merge([''], $categories) as $option)
                @php($isActive = $category === $option)

                <a href="{{ route('articles.index', array_filter(['category' => $option, 'q' => $term])) }}" data-article-filter
                   @class([
                       'shrink-0 whitespace-nowrap rounded-full text-[14px] font-semibold leading-[20px] tracking-[0.7px] transition-colors duration-300',
                       'bg-[#005ea1] px-[16px] py-[6px] text-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]' => $isActive,
                       'border border-[rgba(192,199,211,0.6)] bg-[#f1f4f6] px-[17px] py-[7px] text-[#414751]' => ! $isActive,
                   ])>
                    {{ $option ?: 'All Articles' }}
                </a>
            @endforeach
        </div>
    </section>

    <div data-article-results>
    {{-- Featured guide card (1:6039) --}}
    @if ($featured)
    <a href="{{ $featured['href'] }}" data-reveal class="mx-[20px] mb-[24px] block overflow-hidden rounded-[16px] border border-[rgba(192,199,211,0.5)] bg-white p-px shadow-[0px_4px_20px_0px_rgba(0,0,0,0.05)]">
        <div class="relative h-[224px] overflow-hidden rounded-t-[15px] bg-[#d7dadc]">
            <img src="{{ $featured['image_url'] }}" alt="{{ $featured['title'] }}" class="size-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-[rgba(24,28,30,0.8)] via-[rgba(24,28,30,0)] to-[rgba(0,0,0,0.3)]"></div>

            <span class="absolute left-[12px] top-[12px] flex items-center gap-[4px] rounded-full bg-[#005ea1] px-[10px] py-[4px] text-[12px] leading-[16px] text-white drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                <img src="{{ asset('images/icons/mobile/article/featured.svg') }}" alt="" class="h-[9.5px] w-[10px]">
                Featured Guide
            </span>
            <span class="absolute bottom-[12px] left-[12px] rounded-[6px] bg-[rgba(24,28,30,0.6)] px-[10px] py-[2px] text-[12px] font-medium leading-[16px] text-white backdrop-blur-[2px]">
                {{ $featured['category'] }} • {{ $featured['readTime'] }}
            </span>
        </div>

        <div class="p-[20px]">
            <h2 class="text-[24px] font-semibold leading-[30px] text-[#181c1e]">{{ $featured['title'] }}</h2>
            <p class="mt-[8px] line-clamp-2 text-[16px] leading-[24px] text-[#414751]">{{ $featured['excerpt'] }}</p>

            <div class="mt-[16px] flex items-center justify-between gap-[12px] border-t border-[#ebeef0] pt-[17px]">
                <span class="flex min-w-0 items-center gap-[10px]">
                    <span class="flex size-[36px] shrink-0 items-center justify-center rounded-full bg-[#d2e4ff] text-[14px] font-bold leading-[20px] text-[#001d37]">{{ $initials($featured['author']) }}</span>
                    <span class="min-w-0">
                        <span class="block truncate text-[13px] font-semibold leading-[18px] text-[#181c1e]">{{ $featured['author'] }}</span>
                        <span class="block truncate text-[11px] leading-[16px] text-[#525c6f]">{{ $featured['author_role'] }}</span>
                    </span>
                </span>
                <span class="flex shrink-0 items-center gap-[6px] whitespace-nowrap text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-[#005ea1]">
                    Read Guide
                    <img src="{{ asset('images/icons/mobile/article/arrow.svg') }}" alt="" class="size-[10.7px]">
                </span>
            </div>
        </div>
    </a>
    @endif

    {{-- Recent Articles (1:6077 + 1:6085) --}}
    <div data-reveal class="px-[20px] pb-[12px] pt-[8px]">
        <h2 class="text-[24px] font-semibold leading-[32px] text-[#181c1e]">Recent Articles</h2>
        <p class="text-[12px] leading-[16px] text-[#414751]">Curated stories and tactical travel dispatches</p>
    </div>

    <div class="flex flex-col gap-[14px] px-[20px] pb-[32px]">
        @foreach ($articles as $index => $article)
            <a href="{{ route('articles.show', $article) }}" data-reveal style="--reveal-delay: {{ ($index % 4) * 60 }}ms"
               class="flex items-start gap-[14px] rounded-[12px] border border-[rgba(192,199,211,0.4)] bg-white p-[15px] drop-shadow-[0px_2px_6px_rgba(0,0,0,0.03)]">
                <div class="relative size-[96px] shrink-0 overflow-hidden rounded-[8px] bg-[#d7dadc]">
                    <img src="{{ $article['image_url'] }}" alt="" class="size-full object-cover">
                    <span class="absolute bottom-[4px] left-[4px] rounded-[4px] bg-[rgba(24,28,30,0.7)] px-[6px] py-[2px] text-[10px] font-medium leading-[16px] text-white">
                        {{ \Illuminate\Support\Str::before($article['readTime'], ' read') }}
                    </span>
                </div>

                <div class="flex min-h-[96px] min-w-0 flex-1 flex-col justify-between">
                    <div class="flex flex-col gap-[4px]">
                        <p class="flex items-center gap-[8px] text-[11px] leading-[16px]">
                            <span class="font-semibold uppercase tracking-[0.55px] text-[#005ea1]">{{ $article['category'] }}</span>
                            <span class="text-[10px] text-[#717782]">•</span>
                            <span class="text-[#414751]">{{ $article['date'] }}</span>
                        </p>
                        <h3 class="line-clamp-2 text-[14px] font-bold leading-[19.25px] text-[#181c1e]">{{ $article['title'] }}</h3>
                    </div>

                    <div class="flex items-center justify-between pt-[4px]">
                        <span class="min-w-0 truncate pr-[8px] text-[12px] font-medium leading-[16px] text-[#414751]">By {{ $article['author'] }}</span>
                        <img src="{{ asset('images/icons/mobile/article/arrow.svg') }}" alt="" class="size-[10.7px] shrink-0">
                    </div>
                </div>
            </a>
        @endforeach

        @if ($articles->lastPage() > 1)
            <div class="pt-[16px]">
                @include('components.pagination-mobile', ['paginator' => $articles, 'simple' => true])
            </div>
        @endif
    </div>
    </div>

    {{-- Newsletter CTA (1:6170) --}}
    <section class="px-[20px] pb-[32px]">
        <form action="{{ route('newsletter.store') }}" method="post" data-confirm="newsletter" data-reveal
              class="relative flex flex-col gap-[4px] overflow-hidden rounded-[16px] p-[20px] text-white shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)]"
              style="background-image: linear-gradient(140deg, #005ea1 0%, #2178c3 50%, #00386b 100%)">
            @csrf
            <input type="hidden" name="source" value="articles-mobile">
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
            <span class="pointer-events-none absolute -bottom-[24px] -right-[24px] size-[128px] rounded-full bg-white/10 blur-[12px]"></span>

            <p class="flex items-center gap-[8px] text-[12px] font-semibold uppercase leading-[16px] tracking-[0.6px] text-[#d8e4eb]">
                <img src="{{ asset('images/icons/mobile/article/mail.svg') }}" alt="" class="h-[13.3px] w-[16.7px]">
                Maritime Newsletter
            </p>
            <h2 class="pt-[4px] text-[20px] font-bold leading-[25px]">Get Island Guides &amp; Exclusive Ticket Deals</h2>
            <p class="text-[12px] leading-[19.5px] text-[#d2e4ff]/90">Direct schedule alerts, secret island coves, and 15% off seasonal fast boat transfers delivered bi-weekly.</p>

            <div class="flex flex-col gap-[10px] pt-[12px]">
                <label for="m-newsletter-email" class="sr-only">Email address</label>
                <input id="m-newsletter-email" type="email" name="email" required value="{{ old('email') }}" placeholder="Enter your email address"
                       class="rounded-[8px] bg-white px-[14px] py-[11px] text-[14px] text-[#181c1e] placeholder:text-[#717782] focus:outline-none">
                <button type="submit" class="rounded-[8px] bg-[#d5e2e9] py-[10px] text-[14px] font-semibold leading-[20px] text-[#111d22] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">Subscribe</button>
            </div>

            <p class="pt-[6px] text-center text-[10px] leading-[24px] text-[rgba(210,228,255,0.8)]">No spam. Unsubscribe anytime with 1-click.</p>
            @if (session('newsletter'))
                <p class="text-[13px] text-white">{{ session('newsletter') }}</p>
            @endif
            @error('email')
                <p class="text-[13px] text-white !text-red-500">{{ $message }}</p>
            @enderror
        </form>
    </section>
</div>
