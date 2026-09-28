{{-- Figma node 1:408 — Boat Operators (desktop) / 1:5045 — boat full (mobile) --}}
@extends('layouts.app')

@section('title', 'Our Boats')

@section('nav-active', 'boat')

@section('hero')
    {{-- Mobile (< lg): three cards, as drawn in Figma 1:5089 --}}
    @include('partials.mobile-page-hero', [
        'image'    => asset('images/boats/hero-boat.png'),
        'title'    => 'Our Boats',
        'subtitle' => 'Every fast boat in our fleet, with its routes and passenger capacity.',
    ])

    <div class="hidden lg:block">
        @include('partials.page-hero', [
            'image'    => asset('images/boats/hero-boat.png'),
            'title'    => 'Our Boats',
            'subtitle' => 'Every fast boat in our fleet, with its routes and passenger capacity.',
            'active'   => 'boat',
        ])
    </div>
@endsection

@section('content')
    {{-- Mobile (< lg): Figma 1:5095 — Operator Cards List --}}
    <section class="px-[20px] py-[32px] lg:hidden">
        <div class="flex flex-col gap-[32px]">
            @foreach ($boats->take(3) as $index => $boat)
                @include('components.boat-card-mobile', [
                    'delay'       => $index * 90,
                    'name'        => $boat['name'],
                    'description' => $boat['description'],
                    'rating'      => $boat['rating'],
                    'image'       => $boat['image_url'],
                    'routes'      => $boat['route_count'],
                    'vessels'     => $boat['capacity'],
                    'href'        => route('boats.vessel', $boat),
                ])
            @endforeach
        </div>

        <div class="mt-[64px]">
            @include('components.pagination-mobile', ['paginator' => $boats])
        </div>
    </section>

    <section class="container-page hidden pb-[107px] pt-[71px] lg:block">
        <div class="grid [&>*]:min-w-0 justify-items-center gap-x-[52px] gap-y-[71px] sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($boats as $index => $boat)
                @include('components.boat-card', [
                    'delay'       => ($index % 3) * 90,
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

        <div class="mt-[71px]">
            @include('components.pagination', ['paginator' => $boats])
        </div>
    </section>
@endsection
