{{-- Figma node 1:2102 — hero copy + category tabs + search --}}
@props(['categories' => [], 'category' => '', 'term' => ''])

<section class="container-page pt-[34px] lg:pt-[82px] font-jakarta">
    <div data-reveal class="flex max-w-[985px] flex-col items-start gap-[20.5px]">
        <h2 class="text-[26px] lg:text-[61.5px] font-bold leading-[35px] lg:leading-[77px] tracking-[-1.23px] text-editorial-ink">
            Travel Articles &amp; Island Guides
        </h2>

        <p class="text-[23px] leading-[37.5px] text-editorial-body">
            Inspiration, guides, and tips for your fast boat travel and island adventures across Bali, Nusa
            Penida, Lembongan, and Gili Islands.
        </p>
    </div>

    {{-- Search sits on its own row so the category pills get the full width below it. --}}
    <form data-reveal style="--reveal-delay: 100ms" action="{{ route('articles.index') }}" method="get"
          class="relative mt-[34px] w-full max-w-[520px]">
        {{-- Searching inside a category stays in that category. --}}
        @if ($category)
            <input type="hidden" name="category" value="{{ $category }}">
        @endif
        <img src="{{ asset('images/icons/article/search.svg') }}" alt=""
             class="pointer-events-none absolute left-[18px] top-1/2 size-[19px] -translate-y-1/2">
        <label for="article-search" class="sr-only">Search guides</label>
        <input id="article-search" name="q" type="search" value="{{ $term }}" data-article-search autocomplete="off" placeholder="Search guides, ports, tips..."
               class="w-full rounded-full border border-editorial-line bg-surface py-[15px] pl-[52px] pr-[22px] text-[17.95px] text-editorial-ink placeholder:text-editorial-meta focus:border-editorial focus:outline-none">
    </form>

    {{-- Figma 1:2113 "Scrollable Category Tabs" — the categories articles are actually filed
         under; picking one filters the list and keeps any search term. --}}
    <ul data-reveal style="--reveal-delay: 120ms"
        class="mt-[24px] flex flex-wrap items-center gap-[10px]">
        @foreach (array_merge([''], $categories) as $option)
            @php($isActive = $category === $option)

            <li>
                <a href="{{ route('articles.index', array_filter(['category' => $option, 'q' => $term])) }}" data-article-filter
                   @class([
                       'block rounded-full px-[27px] py-[14px] text-[17.95px] font-semibold leading-[25.6px] tracking-[0.9px] transition-colors duration-300',
                       'bg-editorial text-white shadow-sm' => $isActive,
                       'border border-editorial-line bg-surface text-editorial-body hover:border-editorial hover:text-editorial' => ! $isActive,
                   ])>
                    {{ $option ?: 'All Articles' }}
                </a>
            </li>
        @endforeach
    </ul>
</section>
