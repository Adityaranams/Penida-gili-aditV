{{-- Figma node 1:946 — hotel (desktop) / 1:3185 — hotels full (mobile) --}}
@extends('layouts.app')

@section('title', 'Hotel')

@section('nav-active', 'hotel')

@section('hero')
    {{-- Mobile (< lg): three cards, as drawn in Figma 1:3228 --}}
    @include('partials.mobile-page-hero', [
        'image'    => asset('images/hotels/hero-hotel.png'),
        'title'    => 'Hotels',
        'subtitle' => 'A selection of hotels to complete your unforgettable holiday experience.',
        'overlay'  => 'dark',
    ])

    <div class="hidden lg:block">
        @include('partials.page-hero', [
            'image'    => asset('images/hotels/hero-hotel.png'),
            'title'    => 'Hotel',
            'subtitle' => 'A selection of Hotels to complete your unforgettable holiday experience.',
            'active'   => 'hotel',
        ])
    </div>
@endsection

@section('content')
    {{-- Mobile (< lg): Figma 1:3237 — title + single-column hotel list --}}
    <section class="bg-[#f7fafc] pb-[80px] lg:hidden">
        <div data-reveal class="px-[20px] py-[32px]">
            <h2 class="text-[28px] font-bold leading-[36px] text-[#181c1e]">Hotels</h2>
            <p class="mt-[8px] text-[16px] leading-[24px] text-[#414751]">Find your perfect stay for an unforgettable island experience.</p>
        </div>

        <div class="flex flex-col gap-[32px] px-[20px]">
            @foreach ($hotels->take(3) as $index => $hotel)
                @include('components.hotel-card-mobile', [
                    'delay'       => $index * 90,
                    'name'        => $hotel['name'],
                    'description' => $hotel['description'],
                    'rating'      => $hotel['rating'],
                    'image'       => $hotel['image_url'],
                    'location'    => $hotel['meta'][0]['label'] ?? '',
                    'price'       => $hotel['price_from_label'],
                    'href'        => route('hotels.show', $hotel),
                ])
            @endforeach
        </div>

        @if ($hotels->lastPage() > 1)
            <div class="mt-[48px]">
                @include('components.pagination-mobile', ['paginator' => $hotels, 'simple' => true])
            </div>
        @endif
    </section>

    <section class="container-page hidden pb-[107px] pt-[39px] lg:block">
        <div class="grid [&>*]:min-w-0 justify-items-center gap-x-[52px] gap-y-[70px] sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($hotels as $index => $hotel)
                @include('components.place-card', [
                    'delay'       => ($index % 3) * 90,
                    'name'        => $hotel['name'],
                    'description' => $hotel['description'],
                    'rating'      => $hotel['rating'],
                    'image'       => $hotel['image_url'],
                    'meta'        => $hotel['meta'],
                    'price'       => $hotel['price_from_label'],
                    'href'        => route('hotels.show', $hotel),
                ])
            @endforeach
        </div>

        <div class="mt-[71px]">
            @include('components.pagination', ['paginator' => $hotels])
        </div>
    </section>
@endsection
