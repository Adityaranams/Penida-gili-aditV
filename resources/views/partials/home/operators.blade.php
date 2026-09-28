{{-- Figma nodes 1:86 / 1:87 — the three best-rated boats in the fleet. --}}
<section class="container-page pt-[67px] lg:pt-[160px]">
    @include('partials.section-heading', [
        'eyebrow' => 'Top Rated Boats',
        'title'   => 'Top Rated <br> Boats',
        'intro'   => "Our best-reviewed fast boats, ranked by the ratings guests give them. Every boat lists the routes it sails and how many passengers it carries, so you can pick the one that fits your crossing.",
    ])

    <div class="mt-[28px] lg:mt-[66px] grid [&>*]:min-w-0 justify-items-center gap-x-[52px] gap-y-[71px] sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($topBoats as $index => $boat)
            @include('components.boat-card', [
                'delay'       => $index * 90,
                'name'        => $boat['name'],
                'description' => $boat['description'],
                'rating'      => $boat['rating'],
                'image'       => $boat['image_url'],
                'routes'      => $boat['route_count'],
                'capacity'    => $boat['capacity'],
                'href'        => route('boats.vessel', $boat),
            ])
        @endforeach
    </div>
</section>
