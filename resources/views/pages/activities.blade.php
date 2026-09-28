{{-- Figma node 1:623 — Activity (desktop) / 1:3804 — Activity full (mobile) --}}
@extends('layouts.app')

@section('title', 'Activity')

@section('nav-active', 'activity')

@section('hero')
    {{-- Mobile (< lg): three cards, as drawn in Figma 1:3848 --}}
    @include('partials.mobile-page-hero', [
        'image'    => asset('images/activities/hero-activity.png'),
        'title'    => 'Activity',
        'subtitle' => 'A selection of exciting and unique activities to complete your unforgettable holiday experience.',
        'overlay'  => 'dark',
    ])

    <div class="hidden lg:block">
        @include('partials.page-hero', [
            'image'    => asset('images/activities/hero-activity.png'),
            'title'    => 'Activity',
            'subtitle' => 'A selection of exciting and unique activities to complete your unforgettable holiday experience.',
            'active'   => 'activity',
        ])
    </div>
@endsection

@section('content')
    {{-- Mobile (< lg): Figma 1:3857 — Activities Grid --}}
    <section class="bg-[#f7fafc] px-[20px] pb-[120px] pt-[48px] lg:hidden">
        <div class="flex flex-col gap-[32px]">
            @foreach ($activities->take(3) as $index => $activity)
                @include('components.place-card-mobile', [
                    'delay'       => $index * 90,
                    'name'        => $activity['name'],
                    'description' => $activity['plain_description'],
                    'rating'      => $activity['rating'],
                    'image'       => $activity['image_url'],
                    'meta'        => $activity['meta'],
                    'price'       => $activity['price_label'],
                    'href'        => route('activities.show', $activity),
                ])
            @endforeach
        </div>

        <div class="mt-[48px]">
            @include('components.pagination-mobile', ['paginator' => $activities, 'simple' => true])
        </div>
    </section>

    <section class="container-page hidden pb-[107px] pt-[39px] lg:block">
        <div class="grid [&>*]:min-w-0 justify-items-center gap-x-[52px] gap-y-[70px] sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($activities as $index => $activity)
                @include('components.place-card', [
                    'delay'       => ($index % 3) * 90,
                    'name'        => $activity['name'],
                    'description' => $activity['plain_description'],
                    'rating'      => $activity['rating'],
                    'image'       => $activity['image_url'],
                    'meta'        => $activity['meta'],
                    'price'       => $activity['price_label'],
                    'href'        => route('activities.show', $activity),
                ])
            @endforeach
        </div>

        <div class="mt-[71px]">
            @include('components.pagination', ['paginator' => $activities])
        </div>
    </section>
@endsection
