{{-- Figma node 1:1283 — sticky "Popular Routes" booking aside --}}
@props(['routes', 'slug'])

<aside class="lg:sticky lg:top-8">
    <div data-reveal class="rounded-detail border border-editorial-rule bg-surface p-[34px] shadow-editorial">
        <h2 class="text-[32px] font-bold leading-[44px] text-editorial-ink">Popular Routes</h2>

        <ul class="mt-[22px] space-y-[32px]">
            @foreach ($routes as $route)
                <li class="rounded-[11px] border border-editorial-rule p-[23px]">
                    <div class="flex items-center gap-[8px]">
                        <div class="min-w-0 shrink">
                            <p class="truncate text-[17.5px] font-bold leading-[26px] tracking-[0.5px] text-editorial-ink">{{ $route['from'] }}</p>
                            <p class="text-[16.5px] leading-[24px] text-editorial-meta">{{ $route['departure'] }}</p>
                        </div>

                        {{-- The connector takes the leftover width but never collapses. --}}
                        <span class="relative flex h-[27px] min-w-[28px] flex-1 items-center justify-center" aria-hidden="true">
                            <span class="h-px w-full bg-[#c0c7d3]"></span>
                            <img src="{{ asset('images/icons/vessel/route-arrow.svg') }}" alt=""
                                 class="absolute size-[12.5px] box-content bg-surface px-[4px]">
                        </span>

                        <div class="min-w-0 shrink text-right">
                            <p class="truncate text-[17.5px] font-bold leading-[26px] tracking-[0.5px] text-editorial-ink">{{ $route['to'] }}</p>
                            <p class="text-[16.5px] leading-[24px] text-editorial-meta">{{ $route['arrival'] }}</p>
                        </div>
                    </div>

                    {{-- Fare and unit read as one phrase; the button holds the right edge. --}}
                    <div class="mt-[22px] flex flex-wrap items-center justify-between gap-x-[12px] gap-y-[10px]">
                        <p class="flex items-baseline gap-[4px]">
                            <span class="whitespace-nowrap text-[21px] font-semibold leading-[30px] text-brand">{{ $route['price'] }}</span>
                            <span class="text-[16px] leading-[24px] text-editorial-meta">/pax</span>
                        </p>

                        <a href="{{ route('boats.order', [$slug, 'schedule' => $route['id']]) }}"
                           class="shrink-0 whitespace-nowrap rounded-[8px] bg-editorial/10 px-[16px] py-[10px] text-[17px] font-semibold leading-[25px] tracking-[0.85px] text-brand
                                  transition-colors duration-300 hover:bg-editorial/20">
                            Book Now
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Figma node 1:1425 --}}
        <div class="mt-[32px] flex flex-col gap-[22px] border-t border-editorial-rule pt-[44px]">
            <p class="text-center text-[21.5px] leading-[32px] text-editorial-body">
                Need a custom route or private charter?
            </p>

            <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
               class="flex items-center justify-center bg-[#d5e2e9] px-[21px] py-[16px] text-[18.8px] font-bold leading-[27px] tracking-[0.94px] text-[#58646a]
                      transition-colors duration-300 hover:bg-[#c3d6e0]">
                Contact Operator
            </a>
        </div>
    </div>
</aside>
