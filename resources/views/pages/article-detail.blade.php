{{-- Figma node 1:2380 — article detail (desktop) / 1:6261 — article detail full (mobile) --}}
@extends('layouts.app')

{{-- SEO fields come from the admin form; each falls back to the editorial copy when blank. --}}
@section('title', $article['seo_title'] ?? $article['title'] ?? 'Article')
@section('meta-description', $article['seo_description'] ?? $article['excerpt'] ?? '')
@if (! empty($article['meta_keywords']))
    @section('meta-keywords', implode(', ', $article['meta_keywords']))
@endif

@section('nav-active', 'artikel')

@section('hero')
    {{-- Mobile (< lg) gets its own layout from the mobile Figma frame. --}}
    @include('partials.article.mobile-detail', ['article' => $article])

    <header class="relative hidden h-[276px] w-full overflow-hidden lg:block">
        <img src="{{ asset('images/articles/hero-article.png') }}" alt=""
             class="absolute inset-0 size-full object-cover object-bottom">

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'artikel'])
        </div>
    </header>
@endsection

@section('content')
    <div class="container-page hidden pt-[41px] font-jakarta lg:block">
        {{-- Figma node 1:2387 — breadcrumb --}}
        <nav data-reveal aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-[10px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px]">
                <li><a href="{{ route('home') }}" class="text-editorial-body transition-colors hover:text-editorial">Home</a></li>
                <li aria-hidden="true"><img src="{{ asset('images/icons/article-detail/chevron.svg') }}" alt="" class="h-[10.3px] w-[6.4px]"></li>
                <li><a href="{{ route('articles.index') }}" class="text-editorial-body transition-colors hover:text-editorial">Blog</a></li>
                <li aria-hidden="true"><img src="{{ asset('images/icons/article-detail/chevron.svg') }}" alt="" class="h-[10.3px] w-[6.4px]"></li>
                <li><a href="{{ route('articles.index') }}" class="text-editorial-body transition-colors hover:text-editorial">{{ $article['category'] }}</a></li>
                <li aria-hidden="true"><img src="{{ asset('images/icons/article-detail/chevron.svg') }}" alt="" class="h-[10.3px] w-[6.4px]"></li>
                <li aria-current="page" class="font-medium text-editorial">{{ $article['title'] }}</li>
            </ol>
        </nav>

        {{-- Figma node 1:2402 — article header --}}
        <header class="mt-[41px] max-w-[1159px]">
            <div data-reveal class="flex flex-wrap items-center gap-[15.5px] text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body">
                <span class="rounded-full bg-[#d2e4ff] px-[18px] py-[5px] tracking-[0.45px] text-[#001d37]">{{ $article['category'] }}</span>
                <span class="flex items-center gap-[5px]">
                    <img src="{{ asset('images/icons/article-detail/clock.svg') }}" alt="" class="size-[19.4px]">
                    {{ $article['readTime'] }}
                </span>
                <span class="size-[7.8px] rounded-full bg-[#c0c7d3]" aria-hidden="true"></span>
                <span>{{ $article['date'] }}</span>
                <span class="size-[7.8px] rounded-full bg-[#c0c7d3]" aria-hidden="true"></span>
                <span class="flex items-center gap-[5px]">
                    <img src="{{ asset('images/icons/article-detail/eye.svg') }}" alt="" class="h-[14.5px] w-[21.3px]">
                    {{ $article['views_label'] }}
                </span>
            </div>

            <h1 data-reveal style="--reveal-delay: 90ms"
                class="mt-[20.7px] text-[26px] lg:text-[62px] font-bold leading-[35px] lg:leading-[77.6px] tracking-[-1.24px] text-editorial-ink">
                {{ $article['title'] }}
            </h1>

            <p data-reveal style="--reveal-delay: 150ms"
               class="mt-[20.7px] pb-[10px] text-[23.3px] leading-[37.8px] text-editorial-body">{{ $article['subtitle'] }}</p>

            {{-- Figma node 1:2422 — byline. The portrait comes from the author record;
                 without one the reader sees their initials. --}}
            <div data-reveal class="mt-[20.7px] flex flex-wrap items-center gap-6 border-t border-[#e5e9eb] pt-[32.3px]">
                <div class="flex items-center gap-[18px]">
                    <span class="flex size-[62px] shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#d5e2e9] text-[22px] font-bold text-[#58646a] ring-[2.6px] ring-[#d2e4ff]">
                        @if ($article->writer?->photo_url)
                            <img src="{{ $article->writer->photo_url }}" alt="{{ $article['author'] }}" class="size-full object-cover">
                        @else
                            {{ $article->writer?->initials ?? mb_strtoupper(mb_substr($article['author'], 0, 1)) }}
                        @endif
                    </span>

                    <span>
                        <span class="block text-[20.7px] font-semibold leading-[31px] text-editorial-ink">Written by {{ $article['author'] }}</span>
                        @if (filled($article['author_role']))
                            <span class="block text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-editorial-body">{{ $article['author_role'] }}</span>
                        @endif
                    </span>
                </div>
            </div>
        </header>

        {{-- Figma node 1:2453 — hero image with caption --}}
        <figure data-reveal class="relative mt-[41px] overflow-hidden rounded-editorial shadow-editorial">
            <button type="button" class="block w-full cursor-zoom-in"
                    data-lightbox="{{ $article['image_url'] }}" data-lightbox-alt="{{ $article['hero_alt'] ?: $article['title'] }}">
                <img src="{{ $article['image_url'] }}" alt="{{ $article['hero_alt'] ?: $article['title'] }}"
                     class="h-[260px] lg:h-[620px] w-full object-cover">
            </button>

            <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[rgba(24,28,30,0.6)] to-transparent" aria-hidden="true"></span>

            @if (filled($article['hero_caption']))
                <figcaption class="absolute inset-x-[31px] bottom-[20px]">
                    <span class="text-[18.1px] font-semibold leading-[26px] tracking-[0.9px] text-white opacity-90">{{ $article['hero_caption'] }}</span>
                </figcaption>
            @endif
        </figure>

        {{-- Figma node 1:2464 — 2 columns --}}
        <div class="mt-[41px] grid [&>*]:min-w-0 gap-[41px] lg:grid-cols-[minmax(0,952fr)_minmax(0,455fr)]">
            @include('partials.article.detail-body', ['article' => $article])

            @include('partials.article.detail-sidebar', ['article' => $article, 'popular' => $popular])
        </div>

        {{-- Figma node 1:2788 — related articles --}}
        <section class="pt-[43px] lg:pt-[103px]">
            <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="text-[18.1px] font-bold uppercase leading-[26px] tracking-[0.9px] text-editorial">More from the blog</p>
                    <h2 class="mt-[5px] text-[24px] lg:text-[46.5px] font-bold leading-[30px] lg:leading-[57px] tracking-[-0.47px] text-editorial-ink">Related Articles You Might Like</h2>
                </div>

                <a href="{{ route('articles.index') }}" class="flex items-center gap-[8px] text-[20.7px] font-semibold leading-[31px] text-editorial">
                    View all blog stories
                    <img src="{{ asset('images/icons/article/arrow-link.svg') }}" alt="" class="size-[15.4px]">
                </a>
            </div>

            <div class="mt-[41px] grid [&>*]:min-w-0 items-stretch gap-[31px] md:grid-cols-2 xl:grid-cols-3">
                @foreach ($related as $index => $item)
                    @include('components.article-card', [
                        'delay'    => $index * 90,
                        'title'    => $item['title'],
                        'excerpt'  => $item['excerpt'],
                        'category' => $item['category'],
                        'readTime' => $item['readTime'],
                        'date'     => $item['date'],
                        'author'   => $item['author'],
                        'image'    => $item['image_url'],
                        'href'     => route('articles.show', $item),
                    ])
                @endforeach
            </div>
        </section>

        {{-- Figma node 1:2857 — newsletter --}}
        <section class="pb-[44px] lg:pb-[104px] pt-[43px] lg:pt-[103px]">
            <div data-reveal class="relative overflow-hidden rounded-editorial-lg border border-editorial-rule bg-editorial-rule p-[63px]">
                <img src="{{ asset('images/icons/article-detail/newsletter-deco.svg') }}" alt=""
                     class="pointer-events-none absolute -bottom-[52px] -right-[52px] h-[284px] w-[259px]" aria-hidden="true">

                <div class="relative flex max-w-[869px] flex-col gap-[10px]">
                    <p class="text-[18.1px] font-bold uppercase leading-[26px] tracking-[0.9px] text-editorial">Maritime Dispatch</p>

                    <h2 class="text-[24px] lg:text-[46.5px] font-bold leading-[30px] lg:leading-[57px] tracking-[-0.47px] text-editorial-ink">
                        Get Island Route Updates &amp; Early Bird Ticket Discounts
                    </h2>

                    <p class="pt-[5px] text-[20.7px] leading-[31px] text-editorial-body">
                        Join 18,000+ island explorers who receive weekly crossing schedules, sea conditions forecast,
                        and exclusive voucher codes for Bali archipelago transfers.
                    </p>

                    <form action="{{ route('newsletter.store') }}" method="post" data-confirm="newsletter" class="flex flex-wrap gap-[15.5px] pt-[20.7px]">
                        @csrf
                        <input type="hidden" name="source" value="article-detail">
                        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                        <label for="dispatch-email" class="sr-only">Email address</label>
                        <input id="dispatch-email" name="email" type="email" required value="{{ old('email') }}" placeholder="Enter your email address"
                               class="min-w-0 flex-1 rounded-[15.5px] border border-[#c0c7d3] bg-surface px-[22px] pb-[19px] pt-[18px] text-[20.7px] text-editorial-ink placeholder:text-[#6b7280] focus:border-editorial focus:outline-none">
                        <button type="submit"
                                class="rounded-[15.5px] bg-editorial px-[31px] py-[16px] text-[20.7px] font-bold leading-[31px] text-white shadow-lg
                                       transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                            Subscribe Now
                        </button>
                        @if (session('newsletter'))
                            <p class="w-full basis-full text-[16px] text-editorial-body">{{ session('newsletter') }}</p>
                        @endif
                        @error('email')
                            <p class="w-full basis-full text-[16px] text-editorial-body !text-red-500">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
