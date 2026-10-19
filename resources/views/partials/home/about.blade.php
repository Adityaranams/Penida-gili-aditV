{{-- Figma node 1:212 — About / "Your Bridge to Paradise" --}}
<section id="about" class="container-page scroll-mt-[120px] pt-[74px] lg:pt-[175px]">
    <div class="grid [&>*]:min-w-0 gap-[52px] lg:gap-[44px] lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1.1fr)_minmax(0,1.1fr)]">
        {{-- Copy --}}
        <div data-reveal>
            <p class="text-[20px] font-semibold uppercase leading-[30px] text-brand">About Us</p>

            <h2 class="mt-[50px] max-w-[727px] text-[30px] lg:text-[71px] leading-[53px] lg:leading-[118px] text-ink">Your Bridge to Paradise</h2>

            <p class="mt-[39px] max-w-[760px] text-[16px] leading-[30px] text-ink-muted lg:text-[19px] lg:leading-[36px]">
                Born out of a deep love for Indonesia&rsquo;s breathtaking archipelagos, we started this platform with a
                simple realization: exploring tropical islands should be as relaxing as the vacation itself. For years,
                travelers faced confusing schedules, hidden harbor fees, and unreliable ticketing processes just to cross
                the sea. We decided to change that.
            </p>

            <div class="mt-[29px] lg:mt-[70px] flex flex-wrap gap-[24px]">
                <a href="https://wa.me/{{ config('penida.booking.whatsapp') }}?text={{ rawurlencode('Hi Penida Gili, I would like to ask about your fast boat services.') }}" target="_blank" rel="noopener"
                   class="flex h-[58px] w-[182px] items-center justify-center rounded-field border border-black/33 text-[16px] leading-[30px] text-ink
                          transition-[transform,background-color,border-color] duration-300 ease-smooth hover:-translate-y-0.5 hover:border-ink hover:bg-black/5">
                    Contact Us
                </a>
            </div>
        </div>

        {{-- Portrait image --}}
        <div data-reveal style="--reveal-delay: 120ms"
             class="group h-[251px] w-full overflow-hidden rounded-card lg:h-auto lg:self-stretch">
            <img src="{{ asset('images/home/about-aerial.png') }}" alt="Aerial view of a fast boat"
                 class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">
        </div>

        {{-- Mission + values --}}
        <div data-reveal style="--reveal-delay: 220ms" class="flex flex-col gap-[37px]">
            <article class="rounded-card border-2 border-line-card bg-surface p-[42px] shadow-card
                            transition-[transform,box-shadow] duration-500 ease-smooth hover:-translate-y-1 hover:shadow-card-hover">
                <div class="flex items-start gap-[44px]">
                    <span class="relative flex size-[79px] shrink-0 items-center justify-center">
                        <img src="{{ asset('images/icons/service/circle.svg') }}" alt="" class="absolute inset-0 size-full">
                        <img src="{{ asset('images/icons/service/mission.svg') }}" alt="" class="relative h-[44px] w-[41px]">
                    </span>
                    <h3 class="pt-[18px] text-[20px] leading-[33px] text-ink">Our Mission</h3>
                </div>
                <p class="mt-[19px] text-[16px] leading-[30px] text-ink-muted">
                    To provide a seamless, secure, and transparent bridge between you and your dream island destinations,
                    ensuring your ocean journey is safe, comfortable, and entirely stress-free.
                </p>
            </article>

            <article class="rounded-card border-2 border-line-card bg-surface p-[42px] shadow-card
                            transition-[transform,box-shadow] duration-500 ease-smooth hover:-translate-y-1 hover:shadow-card-hover">
                <div class="flex items-start gap-[44px]">
                    <span class="relative flex size-[79px] shrink-0 items-center justify-center">
                        <img src="{{ asset('images/icons/service/circle.svg') }}" alt="" class="absolute inset-0 size-full">
                        <img src="{{ asset('images/icons/service/values.svg') }}" alt="" class="relative h-[40px] w-[49px]">
                    </span>
                    <h3 class="pt-[10px] text-[20px] leading-[33px] text-ink">Our Core <br>Values</h3>
                </div>
                <ul class="mt-[53px] list-disc ps-[24px] text-[16px] leading-[30px] text-ink-muted">
                    <li>Safety Without Compromise</li>
                    <li>Radical Transparency</li>
                    <li>Seamless Innovation</li>
                    <li>Local Expertise</li>
                </ul>
            </article>
        </div>
    </div>
</section>
