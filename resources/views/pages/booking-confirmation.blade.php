{{-- Post-checkout confirmation. Reached by redirect after POST /…/order and by the link in the email. --}}
@extends('layouts.app')

@section('title', 'Booking '.$booking->reference)

@php
    // Keep the nav highlight and hero on the product the guest just booked.
    [$section, $heroImage] = match (true) {
        $booking->bookable instanceof \App\Models\HotelRoom => ['hotel', 'images/hotels/hero-hotel.png'],
        $booking->bookable instanceof \App\Models\Activity => ['activity', 'images/activities/hero-activity.png'],
        default => ['boat', 'images/boats/hero-order.png'],
    };
@endphp

@section('nav-active', $section)

@section('hero')
    <header class="relative hidden h-[276px] w-full overflow-hidden lg:block">
        <img src="{{ asset($heroImage) }}" alt="" class="absolute inset-0 size-full object-cover object-bottom">
        <div class="relative z-10">
            @include('partials.nav', ['active' => $section])
            <div class="container-page mt-[27px] text-center">
                <h1 data-reveal class="text-[48px] font-bold leading-[60px] text-on-hero">Booking Received</h1>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <section class="container-page px-[20px] pb-[80px] pt-[32px] font-jakarta lg:px-0 lg:pt-[48px]">
        <div class="mx-auto max-w-[760px] rounded-detail border border-[#e0e3e5] bg-surface p-[28px] shadow-detail lg:p-[44px]">
            <div class="flex items-start gap-[16px]">
                <div>
                    <p class="text-[14px] font-semibold uppercase tracking-[0.7px] text-brand">Reference {{ $booking->reference }}</p>
                    <h2 class="mt-[4px] text-[26px] font-bold leading-[34px] text-editorial-ink lg:text-[34px] lg:leading-[44px]">
                        Thanks, {{ \Illuminate\Support\Str::before($booking->customer_name, ' ') }} — your booking is in.
                    </h2>
                    <p class="mt-[8px] text-[16px] leading-[26px] text-editorial-body">
                        @if ($booking->customer_email)
                            We have emailed the details to <strong class="text-editorial-ink">{{ $booking->customer_email }}</strong>.
                        @else
                            Keep this reference handy — we will reach you on <strong class="text-editorial-ink">{{ $booking->dial_code }} {{ $booking->phone }}</strong>.
                        @endif
                        Our team will confirm availability and send payment instructions shortly.
                    </p>
                </div>
            </div>

            <dl class="mt-[28px] grid gap-[16px] rounded-[12px] bg-[#f7fafc] p-[24px] sm:grid-cols-2">
                <div>
                    <dt class="text-[13px] font-semibold uppercase tracking-[0.65px] text-editorial-body">Product</dt>
                    <dd class="mt-[4px] text-[17px] font-semibold text-editorial-ink">{{ $booking->product_label }}</dd>
                </div>
                <div>
                    <dt class="text-[13px] font-semibold uppercase tracking-[0.65px] text-editorial-body">Date</dt>
                    <dd class="mt-[4px] text-[17px] font-semibold text-editorial-ink">
                        {{ $booking->travel_date->format('D, d M Y') }}
                        @if ($booking->check_out) → {{ $booking->check_out->format('D, d M Y') }} @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-[13px] font-semibold uppercase tracking-[0.65px] text-editorial-body">Guests</dt>
                    <dd class="mt-[4px] text-[17px] font-semibold text-editorial-ink">{{ $booking->guests_label }}@if ($booking->check_out) · {{ $booking->rooms }} room{{ $booking->rooms > 1 ? 's' : '' }} · {{ $booking->nights }} night{{ $booking->nights > 1 ? 's' : '' }}@endif</dd>
                </div>
                <div>
                    <dt class="text-[13px] font-semibold uppercase tracking-[0.65px] text-editorial-body">Status</dt>
                    <dd class="mt-[4px] text-[17px] font-semibold text-editorial-ink">{{ $booking->status->label() }} · {{ $booking->payment_status->label() }}</dd>
                </div>
                <div class="sm:col-span-2 border-t border-[#e0e3e5] pt-[16px]">
                    <dt class="text-[13px] font-semibold uppercase tracking-[0.65px] text-editorial-body">Total</dt>
                    <dd class="mt-[4px] text-[28px] font-bold text-brand">{{ $booking->total_label }}</dd>
                </div>
            </dl>

            <div class="mt-[28px] flex flex-col gap-[12px] sm:flex-row">
                <a href="https://wa.me/{{ config('penida.booking.whatsapp') }}?text={{ rawurlencode($booking->whatsappMessage()) }}" target="_blank" rel="noopener"
                   class="flex flex-1 items-center justify-center rounded-[10px] bg-brand py-[14px] text-center text-[16px] font-semibold text-white transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                    Chat with us on WhatsApp
                </a>
                <a href="{{ route('home') }}"
                   class="flex flex-1 items-center justify-center rounded-[10px] border border-[#c0c7d3] py-[14px] text-[16px] font-semibold text-editorial-ink transition-colors hover:bg-[#f1f4f6]">
                    Back to home
                </a>
            </div>
        </div>
    </section>
@endsection
