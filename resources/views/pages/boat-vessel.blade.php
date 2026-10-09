{{-- One boat from the fleet: its own photos, specs, facilities and sailings.
     Everything here comes from the vessel record, so a freshly added boat shows
     empty states instead of borrowing its operator's content. --}}
@extends('layouts.app')

@section('title', $vessel->name)

@section('nav-active', 'boat')

@section('hero')
    <header class="relative min-h-[300px] w-full overflow-hidden pb-[32px] lg:h-[560px] lg:min-h-0 lg:pb-0">
        <img src="{{ $vessel->image_url }}" alt="{{ $vessel->name }}" class="absolute inset-0 size-full object-cover">
        <span class="absolute inset-0 bg-gradient-to-t from-[rgba(9,32,52,0.85)] via-[rgba(9,32,52,0.35)] to-transparent" aria-hidden="true"></span>

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'boat'])
        </div>

        <div class="container-page relative z-10 mt-[40px] flex flex-col items-start gap-[12px] font-jakarta lg:absolute lg:inset-x-0 lg:bottom-[53px] lg:mt-0">
            @if ($vessel->rating)
                <span data-reveal
                      class="flex items-center gap-[8px] rounded-full bg-white/20 px-[16px] py-[8px] text-[14px] font-bold leading-[20px] tracking-[0.7px] text-white backdrop-blur-[6px]">
                    <img src="{{ asset('images/icons/vessel/star-badge.svg') }}" alt="" class="h-[19px] w-[20px]">
                    {{ $vessel->rating }}
                </span>
            @endif

            <h1 data-reveal style="--reveal-delay: 100ms"
                class="text-[24px] lg:text-[48px] font-bold leading-[30px] lg:leading-[48px] tracking-[-0.96px] text-white">
                {{ $vessel->name }}
            </h1>

            <p data-reveal style="--reveal-delay: 180ms" class="text-[18px] leading-[28px] text-[#e5e7eb]">
                {{ $vessel->description ?: $vessel->type }}
            </p>
        </div>
    </header>
@endsection

@section('content')
    <div class="container-page grid [&>*]:min-w-0 gap-[32px] pb-[80px] pt-[40px] lg:grid-cols-[minmax(0,994fr)_minmax(0,481fr)] lg:gap-[80px] lg:pt-[64px]">
        <div class="flex flex-col gap-[48px]">
            {{-- Specs + facilities --}}
            <section>
                <h2 data-reveal class="text-[24px] lg:text-[40px] font-bold leading-[30px] lg:leading-[52px] tracking-[-0.48px] text-brand">Boat Information</h2>

                <div class="mt-[28px] grid [&>*]:min-w-0 gap-[20px] sm:grid-cols-4">
                    <div data-reveal class="flex flex-col items-center justify-center gap-[12px] rounded-detail bg-surface px-[24px] py-[36px] shadow-detail">
                        <span class="flex size-[56px] items-center justify-center rounded-full bg-[#d5e2e9]">
                            <img src="{{ asset('images/icons/vessel/capacity.svg') }}" alt="" class="h-[19px] w-[24px] object-contain">
                        </span>
                        <span class="text-center">
                            <span class="block text-[16px] font-semibold leading-[24px] tracking-[0.8px] text-editorial-body">Capacity</span>
                            <span class="block text-[28px] font-semibold leading-[38px] text-editorial-ink">{{ $vessel->capacity }} Pax</span>
                        </span>
                    </div>

                    <div data-reveal style="--reveal-delay: 180ms" class="rounded-detail bg-surface p-[28px] shadow-detail sm:col-span-3">
                        <h3 class="pb-[10px] text-[28px] font-semibold leading-[38px] text-editorial-ink">Facilities</h3>

                        @if ($vessel->facilities)
                            <ul class="grid grid-cols-2 gap-[16px]">
                                @foreach ($vessel->facilities as $facility)
                                    <li class="flex items-center gap-[10px] text-[19px] leading-[30px] text-editorial-body">
                                        <img src="{{ asset('images/icons/vessel/'.\Illuminate\Support\Str::slug($facility).'.svg') }}" alt=""
                                             class="size-[24px] shrink-0 object-contain" onerror="this.remove()">
                                        {{ $facility }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-[19px] leading-[30px] text-editorial-meta">No facilities listed for this boat yet.</p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Gallery: only what was uploaded for this boat --}}
            @if ($vessel->gallery)
                <section>
                    <h2 data-reveal class="text-[24px] lg:text-[40px] font-bold leading-[30px] lg:leading-[52px] tracking-[-0.48px] text-brand">Boat Gallery</h2>

                    <div class="mt-[28px] grid grid-cols-2 gap-[20px]">
                        @php($frames = array_slice($vessel->gallery_photos, 0, 3))
                        @php($extra = count($vessel->gallery_photos) - count($frames))

                        @foreach ($frames as $photo)
                            <button type="button" data-lightbox-group="vessel" data-lightbox="{{ $photo['url'] }}" data-lightbox-alt="{{ $photo['alt'] }}"
                                    @class([
                                        'group relative block cursor-zoom-in overflow-hidden rounded-detail shadow-editorial',
                                        'col-span-2 h-[240px] lg:h-[344px]' => $loop->first,
                                        'h-[200px] lg:h-[258px]' => ! $loop->first,
                                    ])>
                                <img src="{{ $photo['url'] }}" alt="{{ $photo['alt'] }}"
                                     class="size-full object-cover transition-transform duration-700 ease-smooth group-hover:scale-105">

                                {{-- Anything that does not fit is counted on the last frame. --}}
                                @if ($loop->last && $extra > 0)
                                    <span class="absolute inset-0 flex items-center justify-center bg-black/45 text-[30px] font-semibold text-white transition-colors duration-300 group-hover:bg-black/55">
                                        +{{ $extra }}
                                    </span>
                                @endif
                            </button>
                        @endforeach

                        <x-gallery-extras :photos="array_slice($vessel->gallery_photos, 3)" group="vessel" />
                    </div>
                </section>
            @endif

            {{-- Testimonials written for this boat --}}
            @if ($vessel->reviews->isNotEmpty())
                <section>
                    <h2 data-reveal class="text-[24px] lg:text-[40px] font-bold leading-[30px] lg:leading-[52px] tracking-[-0.48px] text-brand">Guest Testimonials</h2>

                    <div class="mt-[28px] grid [&>*]:min-w-0 gap-[32px] sm:grid-cols-2">
                        @foreach ($vessel->reviews as $review)
                            <figure data-reveal style="--reveal-delay: {{ $loop->index * 90 }}ms"
                                    class="flex flex-col gap-[21.5px] rounded-detail border border-editorial-rule bg-surface p-[44px] shadow-detail">
                                <div class="flex gap-[5.4px]" role="img" aria-label="{{ $review->stars }} out of 5 stars">
                                    @for ($star = 1; $star <= 5; $star++)
                                        <img src="{{ asset('images/icons/vessel/'.($star <= $review->stars ? 'star-full.svg' : 'star-empty.svg')) }}"
                                             alt="" class="h-[21.3px] w-[22.4px]">
                                    @endfor
                                </div>

                                <blockquote class="text-[21.5px] leading-[32px] text-editorial-body">{{ $review->quote }}</blockquote>

                                <figcaption class="pt-[11px]">
                                    <span class="block text-[18.8px] font-bold leading-[27px] tracking-[0.94px] text-editorial-ink">{{ $review->name }}</span>
                                    <span class="block text-[18.8px] leading-[27px] text-editorial-meta">{{ $review->traveled }}</span>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        {{-- Sailings for this boat only --}}
        {{-- The card stays where it sits in the page; it used to follow the scroll. --}}
        <aside class="lg:self-start">
            <div data-reveal class="rounded-detail border border-editorial-rule bg-surface p-[28px] shadow-editorial">
                <h2 class="text-[28px] font-bold leading-[38px] text-editorial-ink">Routes &amp; Schedule</h2>

                @if ($vessel->schedules->isEmpty())
                    <p class="mt-[16px] text-[18px] leading-[28px] text-editorial-body">
                        No sailings are assigned to this boat yet.
                    </p>

                    <a href="{{ route('boats.index') }}"
                       class="mt-[20px] flex items-center justify-center rounded-detail border border-[#c0c7d3] bg-surface py-[16px] text-[17px] font-semibold text-brand
                              transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                        Browse other boats
                    </a>
                @else
                    <ul class="mt-[20px] space-y-[20px]">
                        @foreach ($vessel->schedules as $schedule)
                            <li class="rounded-[11px] border border-editorial-rule p-[20px]">
                                <div class="flex items-center gap-[8px]">
                                    <div class="min-w-0 shrink">
                                        <p class="truncate text-[17px] font-bold leading-[26px] text-editorial-ink">{{ $schedule->fromPort->name }}</p>
                                        <p class="text-[16px] leading-[24px] text-editorial-meta">{{ $schedule->departure_label }}</p>
                                    </div>

                                    <span class="relative flex h-[26px] min-w-[28px] flex-1 items-center justify-center" aria-hidden="true">
                                        <span class="h-px w-full bg-[#c0c7d3]"></span>
                                        <img src="{{ asset('images/icons/vessel/route-arrow.svg') }}" alt=""
                                             class="absolute size-[12.5px] box-content bg-surface px-[4px]">
                                    </span>

                                    <div class="min-w-0 shrink text-right">
                                        <p class="truncate text-[17px] font-bold leading-[26px] text-editorial-ink">{{ $schedule->toPort->name }}</p>
                                        <p class="text-[16px] leading-[24px] text-editorial-meta">{{ $schedule->arrival_label }}</p>
                                    </div>
                                </div>

                                <div class="mt-[18px] flex flex-wrap items-center justify-between gap-x-[12px] gap-y-[10px]">
                                    <p class="flex items-baseline gap-[4px]">
                                        <span class="whitespace-nowrap text-[20px] font-semibold leading-[28px] text-brand">{{ $schedule->price_label }}</span>
                                        <span class="text-[15px] leading-[22px] text-editorial-meta">/pax</span>
                                    </p>

                                    <a href="{{ route('boats.order', [$vessel->operator->slug, 'schedule' => $schedule->id]) }}"
                                       class="shrink-0 whitespace-nowrap rounded-[8px] bg-editorial/10 px-[16px] py-[9px] text-[16px] font-semibold leading-[24px] text-brand
                                              transition-colors duration-300 hover:bg-editorial/20">
                                        Book Now
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Charter enquiry (Figma 1:1425). Sailings cover the published routes;
                     anything else goes straight to the operator on WhatsApp, with the
                     boat already named so they do not have to retype it. --}}
                <div class="mt-[28px] flex flex-col gap-[16px] border-t border-editorial-rule pt-[24px]">
                    <p class="text-center text-[15px] leading-[24px] text-editorial-body">
                        Need a custom route or private charter?
                    </p>

                    <a href="https://wa.me/{{ config('penida.booking.whatsapp') }}?text={{ rawurlencode('Hi Penida Gili, I would like to ask about a custom route or private charter with '.$vessel->name.'.') }}"
                       target="_blank" rel="noopener"
                       class="flex min-h-[48px] items-center justify-center rounded-[8px] bg-[#d5e2e9] px-[21px] text-[16px] font-bold leading-[24px] tracking-[0.5px] text-[#41505a]
                              transition-colors duration-300 hover:bg-[#c3d6e0]">
                        Contact Operator
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection
