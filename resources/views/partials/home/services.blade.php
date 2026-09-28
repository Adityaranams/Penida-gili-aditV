{{-- Figma node 1:178 — "Why Booking with Us?" --}}
@php
    $services = [
        ['icon' => 'booking.svg', 'title' => 'Instant & Convenient Booking',  'body' => 'Select your route, pay, and receive an e-ticket straight to your inbox—no need to queue at the harbor.'],
        ['icon' => 'pricing.svg', 'title' => 'Honest & Transparent Pricing',  'body' => 'No hidden fees. The price you see is the final price you pay.'],
        ['icon' => 'safety.svg',  'title' => 'Guaranteed Safety',             'body' => 'All our vessels undergo regular maintenance, meet international maritime safety standards, and are helmed by licensed captains.'],
        ['icon' => 'support.svg', 'title' => '24/7 Customer Support',         'body' => 'Our dedicated team is always on standby to assist with schedule changes or answer your questions at any time.'],
    ];
@endphp

<section class="container-page pt-[74px] lg:pt-[175px]">
    @include('partials.section-heading', [
        'eyebrow' => 'Why Book With Us',
        'title'   => 'Why Booking with <br>Us?',
        'intro'   => "Complete fast boat schedules, reliable pricing, flexible reschedule, and secure payment so you can explore Indonesia's islands with ease.",
    ])

    {{-- Cards sit level; hovering lifts one out of the row (Figma shows card 2 in its hover state). --}}
    <div class="mt-[57px] lg:mt-[135px] grid [&>*]:min-w-0 items-stretch gap-[44px] sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($services as $index => $service)
            <article data-reveal style="--reveal-delay: {{ $index * 90 }}ms"
                     class="group flex w-full max-w-[347px] flex-col items-center rounded-card border-2 border-line-card bg-surface px-[44px] pb-[44px] pt-[39px] text-center shadow-card
                     transition-[transform,box-shadow,border-color] duration-500 ease-smooth
                     hover:-translate-y-[28px] hover:border-brand/20 hover:shadow-card-hover">
                <img src="{{ asset('images/icons/service/'.$service['icon']) }}" alt=""
                     class="size-[90px] transition-transform duration-500 ease-smooth group-hover:scale-110">
                <h3 class="mt-[32px] text-[24px] leading-[39px] text-ink">{{ $service['title'] }}</h3>
                <p class="mt-[33px] text-[16px] leading-[30px] text-ink-muted">{{ $service['body'] }}</p>
            </article>
        @endforeach
    </div>
</section>
