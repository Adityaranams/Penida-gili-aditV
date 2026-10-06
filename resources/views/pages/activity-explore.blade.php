{{-- "Things to do in …" — results of the home hero "Where To?" search --}}
@extends('layouts.app')

@section('title', $destination ? 'Things to do in '.$destination['name'] : 'Things to do')

@section('nav-active', 'activity')

@php
    $placeName = $destination['name'] ?? ($search !== '' ? str($search)->title() : null);
    $icon = fn (string $path, string $class = 'size-[14px]') => '<svg class="'.$class.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="'.$path.'"/></svg>';
    $pin = 'M12 21s-7-6.2-7-12a7 7 0 0 1 14 0c0 5.8-7 12-7 12Zm0-9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z';
@endphp

@section('hero')
    <header class="relative w-full overflow-hidden pb-[96px] lg:h-[480px] lg:pb-0">
        <img src="{{ $tabs->firstWhere('slug', $destination['slug'] ?? null)['image'] ?? asset('images/activities/hero-activity.png') }}" alt=""
             class="absolute inset-0 size-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/65 via-black/35 to-black/10"></div>
        <svg class="absolute inset-x-0 bottom-[-1px] h-[60px] w-full text-surface" viewBox="0 0 1440 60" preserveAspectRatio="none" fill="currentColor"><path d="M0 40c240-30 480-30 720 0s480 30 720 0v20H0z"/></svg>

        <div class="relative z-10">
            @include('partials.nav', ['active' => 'activity'])

            <div class="container-page mt-[24px] font-jakarta text-on-hero lg:mt-[36px]">
                <nav class="flex items-center gap-[6px] text-[13px] text-on-hero-muted" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-on-hero">Home</a> <span>›</span>
                    <a href="{{ route('activities.index') }}" class="hover:text-on-hero">Activity</a>
                    @if ($placeName) <span>›</span> <span class="text-on-hero">{{ $placeName }}</span> @endif
                </nav>

                <span class="mt-[14px] inline-flex items-center gap-[6px] rounded-full bg-glass-pill px-[12px] py-[5px] text-[11px] font-semibold uppercase tracking-wider">
                    {!! $icon('M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm3.5-12.5-2 5-5 2 2-5 5-2Z', 'size-[12px]') !!}
                    Where to?{{ $destination ? ' · '.$destination['tagline'] : '' }}
                </span>

                <h1 class="mt-[14px] text-[32px] font-bold leading-tight lg:text-[52px]">
                    Things to do @if ($placeName) in <span class="text-[#7CC8FF]">{{ $placeName }}</span> @endif
                </h1>

                <p class="mt-[10px] max-w-[560px] text-[15px] leading-[26px] text-on-hero-muted">
                    {{ $destination['description'] ?? 'Tours, shows and adventures across Bali and the islands — pick a destination below to narrow it down.' }}
                </p>

                <span class="mt-[16px] inline-flex items-center gap-[6px] rounded-full border border-white/25 bg-white/10 px-[12px] py-[6px] text-[13px] backdrop-blur">
                    {!! $icon('m9 12 2 2 4-4M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z') !!}
                    {{ $activities->total() }} {{ str('activity')->plural($activities->total()) }}
                </span>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <section class="container-page relative z-20 -mt-[56px] font-jakarta">
        {{-- Destination tabs --}}
        <div data-center-selected class="flex gap-[8px] overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden rounded-[22px] bg-surface p-[8px] shadow-[0_12px_40px_rgba(15,34,54,0.12)] ring-1 ring-black/[0.04] lg:justify-between">
            @php $isAll = ! $destination && $search === ''; @endphp
            <a href="{{ route('activities.explore') }}" @if ($isAll) aria-current="page" @endif
               class="flex min-w-[150px] flex-1 items-center gap-[10px] rounded-[16px] px-[10px] py-[8px] transition {{ $isAll ? 'bg-[#0F2236] text-on-hero' : 'hover:bg-surface-muted' }}">
                <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[12px] {{ $isAll ? 'bg-white/10 text-on-hero' : 'bg-brand/10 text-brand' }}">
                    {!! $icon('M4 4h7v7H4zM13 4h7v7h-7zM4 13h7v7H4zM13 13h7v7h-7z', 'size-[16px]') !!}
                </span>
                <span>
                    <span class="block text-[13px] font-semibold">All</span>
                    <span class="block text-[11px] {{ $isAll ? 'text-on-hero-muted' : 'text-ink-muted' }}">{{ $totalCount }} {{ str('activity')->plural($totalCount) }}</span>
                </span>
            </a>

            @foreach ($tabs as $tab)
                @php $isActive = ($destination['slug'] ?? null) === $tab['slug']; @endphp
                <a href="{{ route('activities.explore', ['q' => $tab['name']]) }}" @if ($isActive) aria-current="page" @endif
                   class="flex min-w-[170px] flex-1 items-center gap-[10px] rounded-[16px] px-[10px] py-[8px] transition {{ $isActive ? 'bg-[#0F2236] text-on-hero' : 'hover:bg-surface-muted' }}">
                    @if ($tab['image'])
                        <img src="{{ $tab['image'] }}" alt="" class="size-[40px] shrink-0 rounded-[12px] object-cover">
                    @else
                        <span class="flex size-[40px] shrink-0 items-center justify-center rounded-[12px] bg-gradient-to-br from-brand/20 to-[#7CC8FF]/30 text-brand">{!! $icon($pin, 'size-[16px]') !!}</span>
                    @endif
                    <span>
                        <span class="block text-[13px] font-semibold">{{ $tab['name'] }}</span>
                        <span class="block text-[11px] {{ $isActive ? 'text-on-hero-muted' : 'text-ink-muted' }}">{{ $tab['count'] }} {{ str('activity')->plural($tab['count']) }}</span>
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Results --}}
        <div class="mt-[40px] grid gap-[24px] pb-[80px] sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($activities as $index => $activity)
                <a href="{{ route('activities.show', $activity) }}" data-reveal style="--reveal-delay: {{ ($index % 3) * 90 }}ms"
                   class="group flex flex-col rounded-[22px] bg-surface p-[12px] shadow-[0_10px_40px_rgba(15,34,54,0.08)] ring-1 ring-black/[0.04] transition duration-500 hover:-translate-y-1.5 hover:shadow-[0_18px_50px_rgba(15,34,54,0.14)]">
                    <div class="relative h-[200px] overflow-hidden rounded-[16px]">
                        <img src="{{ $activity->image_url }}" alt="{{ $activity->name }}" class="size-full object-cover transition-transform duration-700 group-hover:scale-105">
                        <span class="absolute left-[10px] top-[10px] inline-flex items-center gap-[4px] rounded-full bg-surface/95 px-[9px] py-[4px] text-[10px] font-semibold text-ink shadow-sm">
                            {!! $icon($pin, 'size-[11px] text-brand') !!} {{ str($activity->location)->afterLast(',')->trim() }}
                        </span>
                    </div>

                    <div class="flex flex-1 flex-col px-[6px] pb-[4px] pt-[14px]">
                        @if ((float) $activity->rating > 0)
                            <div class="flex items-center gap-[8px]">
                                <x-rating-stars :rating="$activity->rating" :size="14" :gap="3" />
                                <span class="text-[12px] text-ink-muted">{{ $activity->rating }}</span>
                            </div>
                        @endif

                        <h2 class="mt-[8px] text-[17px] font-bold leading-snug text-ink transition-colors group-hover:text-brand">{{ $activity->name }}</h2>
                        <p class="mt-[6px] line-clamp-2 text-[13px] leading-[20px] text-ink-muted">{{ $activity->plain_description }}</p>

                        <div class="mt-[14px] flex flex-wrap gap-x-[14px] gap-y-[6px] text-[12px] text-ink-soft">
                            <span class="inline-flex items-center gap-[5px]">{!! $icon($pin, 'size-[13px] text-brand') !!} {{ $activity->place_label ?: str($activity->location)->afterLast(',')->trim() }}</span>
                            @if ($activity->hours_label)
                                <span class="inline-flex items-center gap-[5px]">{!! $icon('M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z', 'size-[13px] text-brand') !!} {{ $activity->hours_label }}</span>
                            @endif
                            @if ($activity->category)
                                <span class="inline-flex items-center gap-[5px]">{!! $icon('M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8ZM7.5 7.5h.01', 'size-[13px] text-brand') !!} {{ $activity->category }}</span>
                            @endif
                        </div>

                        <div class="mt-auto flex items-end justify-between pt-[16px]">
                            <div>
                                <p class="text-[11px] text-ink-muted">Start from</p>
                                <p class="text-[18px] font-bold text-ink">{{ $activity->price_label }}</p>
                            </div>
                            <span class="flex size-[38px] items-center justify-center rounded-full bg-brand/10 text-brand transition group-hover:bg-brand group-hover:text-on-brand">
                                {!! $icon('M5 12h14m-6-6 6 6-6 6', 'size-[16px]') !!}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full rounded-[22px] border border-dashed border-line p-[48px] text-center">
                    <p class="text-[17px] font-semibold text-ink">No activities{{ $placeName ? ' in '.$placeName : '' }} yet</p>
                    <p class="mt-[6px] text-[13px] text-ink-muted">Try another destination above, or browse <a href="{{ route('activities.explore') }}" class="font-semibold text-brand hover:underline">all activities</a>.</p>
                </div>
            @endforelse
        </div>

        @if ($activities->hasPages())
            <div class="pb-[80px]">
                @include('components.pagination', ['paginator' => $activities])
            </div>
        @endif
    </section>
@endsection
