{{-- Figma node 1:2386 — "Dashboard full" (mobile home, 390px). Rendered below lg only; the desktop sections are hidden there. --}}
@php
    $mFeatures = [
        ['icon' => 'feature-booking.svg', 'w' => 20, 'h' => 16, 'title' => 'Instant & Convenient Booking', 'body' => 'Select your route, pay, and receive an e-ticket straight to your inbox—no need to queue at the harbor.'],
        ['icon' => 'feature-pricing.svg', 'w' => 22, 'h' => 16, 'title' => 'Honest & Transparent Pricing', 'body' => 'No hidden fees. The price you see is the final price you pay.'],
        ['icon' => 'feature-safety.svg',  'w' => 16, 'h' => 20, 'title' => 'Guaranteed Safety',            'body' => 'All our vessels undergo regular maintenance and meet international maritime safety standards.'],
        ['icon' => 'feature-support.svg', 'w' => 20, 'h' => 18, 'title' => '24/7 Customer Support',        'body' => 'Our dedicated team is always on standby to assist with schedule changes or answer your questions.'],
    ];
@endphp

<div class="lg:hidden">
    {{-- Hero (1:2636) --}}
    <header class="relative overflow-hidden">
        <img src="{{ asset('images/home/hero-home.png') }}" alt="" class="absolute inset-0 size-full object-cover object-bottom">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative flex flex-col items-center px-[16px] pb-[128px] pt-[64px]">
            <span data-reveal class="rounded-full bg-white/20 px-[12px] py-[4px] text-[14px] font-medium leading-[20px] text-white backdrop-blur-[2px]">
                BOAT BOOK
            </span>

            <h1 data-reveal style="--reveal-delay: 100ms" class="mt-[24px] px-[30px] text-center text-[36px] font-bold leading-[45px] tracking-[-0.9px] text-white">
                Explore Tropical<br>Island<br>Beauty Without<br>Limits
            </h1>

            <p data-reveal style="--reveal-delay: 200ms" class="mt-[40px] px-[10px] text-center text-[18px] leading-[28px] text-[#e5e7eb]">
                Book fast boat tickets and private charters to your dream destinations in minutes. Safe, comfortable, and hassle-free journeys.
            </p>

            @include('partials.home.mobile-search')
        </div>
    </header>

    {{-- About (1:2387) --}}
    <section class="bg-white px-[16px] py-[64px]">
        <div data-reveal>
            <p class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#2563eb]">About Us</p>
            <h2 class="mt-[7px] text-[30px] font-bold leading-[37.5px] text-[#0f172a]">Your Bridge to<br>Paradise</h2>
            <p class="mt-[16px] text-[16px] leading-[24px] text-[#64748b]">
                Born out of a deep love for Indonesia's breathtaking archipelagos, we started this platform with a simple realization: exploring tropical islands should be as relaxing as the vacation itself.
            </p>
            <div class="mt-[24px] flex gap-[16px]">
                <a href="https://wa.me/6281236300562" target="_blank" rel="noopener"
                   class="rounded-[8px] border border-[#e5e7eb] bg-white px-[24px] py-[10px] text-[16px] font-medium leading-[24px] text-[#0f172a]">Contact Us</a>
            </div>
        </div>

        <div class="mt-[40px] flex flex-col gap-[24px]">
            <div data-reveal class="h-[256px] w-full overflow-hidden rounded-[16px] shadow-[0px_10px_15px_-3px_rgba(0,0,0,0.1),0px_4px_6px_-4px_rgba(0,0,0,0.1)]">
                <img src="{{ asset('images/home/about-wake-mobile.png') }}" alt="Fast boat wake" class="size-full object-cover">
            </div>

            <div class="flex flex-col gap-[16px]">
                <article data-reveal class="flex items-start gap-[16px] rounded-[16px] border border-[#f3f4f6] bg-white p-[21px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                    <span class="flex size-[48px] shrink-0 items-center justify-center rounded-[12px] bg-[#eff6ff]">
                        <img src="{{ asset('images/icons/mobile/mission.svg') }}" alt="" class="size-[24px]">
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-[16px] font-bold leading-[24px] text-[#0f172a]">Our Mission</h3>
                        <p class="mt-[4px] text-[14px] leading-[20px] text-[#64748b]">To provide a seamless, secure, and transparent bridge between you and your dream island destinations.</p>
                    </div>
                </article>

                <article data-reveal style="--reveal-delay: 90ms" class="flex items-start gap-[16px] rounded-[16px] border border-[#f3f4f6] bg-white p-[21px] drop-shadow-[0px_1px_1px_rgba(0,0,0,0.05)]">
                    <span class="flex size-[48px] shrink-0 items-center justify-center rounded-[12px] bg-[#eff6ff]">
                        <img src="{{ asset('images/icons/mobile/values.svg') }}" alt="" class="size-[24px]">
                    </span>
                    <div class="min-w-0">
                        <h3 class="text-[16px] font-bold leading-[24px] text-[#0f172a]">Our Core Values</h3>
                        {{-- No marker is drawn here, so the old 13px indent only pushed the
                             values out of line with the heading above them. --}}
                        <ul class="mt-[4px] text-[14px] leading-[20px] text-[#64748b]">
                            <li>Safety Without Compromise</li>
                            <li>Radical Transparency</li>
                            <li>Seamless Innovation</li>
                            <li>Local Expertise</li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- Why Booking with Us (1:2430) --}}
    <section class="bg-white px-[16px] pb-[64px]">
        <div data-reveal>
            <p class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#2563eb]">Why Book With Us</p>
            <h2 class="mt-[7px] text-[30px] font-bold leading-[37.5px] text-[#0f172a]">Why Booking with Us?</h2>
            <p class="mt-[16px] text-[14px] leading-[20px] text-[#64748b]">
                Complete fast boat schedules, reliable pricing, flexible reschedule, and secure payment so you can explore Indonesia's islands with ease.
            </p>
        </div>

        <div class="mt-[48px] flex flex-col gap-[24px]">
            @foreach ($mFeatures as $index => $feature)
                <article data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                         class="flex flex-col items-center gap-[8px] rounded-[16px] border border-[#f3f4f6] bg-[#f8fafc] p-[25px] text-center">
                    <span class="flex size-[48px] items-center justify-center rounded-full bg-[#eff6ff]">
                        <img src="{{ asset('images/icons/mobile/'.$feature['icon']) }}" alt=""
                             style="width: {{ $feature['w'] }}px; height: {{ $feature['h'] }}px">
                    </span>
                    <h3 class="pt-[8px] text-[16px] font-bold leading-[24px] text-[#0f172a]">{{ $feature['title'] }}</h3>
                    <p class="text-[14px] leading-[20px] text-[#64748b]">{{ $feature['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Top rated boats (1:2472) --}}
    <section class="bg-white px-[16px] pb-[64px] pt-[2px]">
        <div data-reveal>
            <p class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#2563eb]">Top Rated Boats</p>
            <h2 class="mt-[7px] text-[30px] font-bold leading-[37.5px] text-[#0f172a]">Top Rated Boats</h2>
            <p class="mt-[16px] text-[14px] leading-[20px] text-[#64748b]">Our best-reviewed fast boats, ranked by the ratings guests give them.</p>
        </div>

        <div class="mt-[40px] flex flex-col gap-[32px]">
            @foreach ($topBoats->take(2) as $index => $boat)
                <a href="{{ route('boats.vessel', $boat) }}" data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                   class="block overflow-hidden rounded-[16px] border border-[#f3f4f6] bg-white p-px shadow-[0px_1px_2px_0px_rgba(0,0,0,0.05)]">
                    <div class="h-[192px] w-full overflow-hidden rounded-t-[15px]">
                        <img src="{{ $boat['image_url'] }}" alt="{{ $boat['name'] }}" class="size-full object-cover">
                    </div>
                    <div class="p-[20px]">
                        @if ($boat['rating'])
                            <div class="flex items-center gap-[4px]">
                                <img src="{{ asset('images/icons/mobile/star.svg') }}" alt="" class="h-[11px] w-[12px]">
                                <span class="text-[14px] font-bold leading-[20px] text-[#0f172a]">{{ $boat['rating'] }}</span>
                            </div>
                        @endif
                        <h3 class="mt-[8px] pb-[8px] text-[18px] font-bold leading-[28px] text-[#0f172a]">{{ $boat['name'] }}</h3>
                        <div class="mt-[8px] flex items-center justify-between border-t border-[#e5e7eb] pt-[17px] text-[12px] leading-[16px] text-[#64748b]">
                            <span class="flex items-center gap-[4px]">
                                <img src="{{ asset('images/icons/mobile/routes.svg') }}" alt="" class="size-[10.5px]">
                                {{ $boat['route_count'] }} {{ \Illuminate\Support\Str::plural('Route', $boat['route_count']) }}
                            </span>
                            <span class="flex items-center gap-[4px]">
                                <img src="{{ asset('images/icons/mobile/boat.svg') }}" alt="" class="h-[11.7px] w-[10.8px]">
                                {{ $boat['capacity'] }} Pax
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Testimonials (1:2520) --}}
    <section class="bg-white px-[16px] pb-[64px]">
        <div data-reveal>
            <p class="text-[14px] font-semibold uppercase leading-[20px] tracking-[0.7px] text-[#2563eb]">Testimonials</p>
            <h2 class="mt-[7px] text-[30px] font-bold leading-[37.5px] text-[#0f172a]">Testimonials from Our<br>Guests</h2>
        </div>

        <div class="mt-[40px] flex flex-col gap-[24px]">
            @foreach ($testimonials as $index => $t)
                <figure data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                        class="flex flex-col gap-[16px] rounded-[16px] border border-[#f3f4f6] bg-[#f8fafc] p-[25px]">
                    <div class="flex gap-[4px]">
                        @for ($i = 0; $i < 5; $i++)
                            <img src="{{ asset('images/icons/mobile/star-testimonial.svg') }}" alt="" class="h-[11px] w-[12px]">
                        @endfor
                    </div>
                    <blockquote class="font-jakarta text-[14px] italic leading-[20px] text-[#64748b]">{{ $t['quote'] }}</blockquote>
                    <figcaption class="pt-[8px]">
                        <p class="text-[14px] font-bold leading-[20px] text-[#0f172a]">{{ $t['name'] }}</p>
                        <p class="text-[12px] leading-[16px] text-[#94a3b8]">{{ $t->experienceLabel('Traveled') ?? 'Verified guest' }}</p>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>
</div>
