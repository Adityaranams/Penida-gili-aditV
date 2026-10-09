{{-- Figma node 1:2874 — activity order (desktop) / 1:4274 — activity order full (mobile) --}}
@extends('layouts.app')

@section('title', 'Order Summary')

@section('nav-active', 'activity')

{{-- The order screens carry their own pinned Book Now bar. --}}
@section('hide-whatsapp', 'yes')

@section('hero')
    {{-- Mobile (< lg) gets its own hero + form from the mobile Figma frame. --}}
    @include('partials.order.activity-mobile', ['order' => $order])

    <header class="relative hidden h-[276px] w-full overflow-hidden lg:block">
        <img src="{{ asset('images/boats/hero-order.png') }}" alt=""
             class="absolute inset-0 size-full object-cover object-bottom">

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'activity'])

            <div class="container-page mt-[27px] text-center">
                <h1 data-reveal class="text-[24px] lg:text-[48px] font-bold leading-[30px] lg:leading-[60px] text-on-hero">Order Summary</h1>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <form action="{{ $order['action'] }}" method="post" data-quote="{{ json_encode($order['quote']) }}" data-confirm="booking" data-email="{{ config('penida.booking.email') }}" data-confirm-product="{{ $order['rows'][0]['value'] ?? '' }}"
          class="container-page hidden [&>*]:min-w-0 pt-[43px] pb-[74px] lg:grid lg:grid-cols-[minmax(0,988fr)_minmax(0,494fr)] lg:gap-[32px]">
        @csrf
        @include('partials.order.hidden-fields', ['order' => $order])

        {{-- The traveler + party fields are identical to the boat order form. --}}
        @include('partials.order.booking-form', ['order' => $order])

        @include('partials.order.activity-summary', ['order' => $order])
    </form>
@endsection
