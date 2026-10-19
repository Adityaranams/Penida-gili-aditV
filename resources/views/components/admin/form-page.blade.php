{{-- Shell for the console "Add new …" screens: back button, title, subtitle and
     optional header actions (Figma 1:7148, 1:7271, 1:8502). --}}
@props(['heading', 'subtitle', 'backHref' => null])

<div class="flex flex-wrap items-start justify-between gap-4 pt-[24px]">
    <div class="flex items-start gap-[16px]">
        {{-- Screens reached from the header menus rather than a listing have
             nowhere obvious to go back to, so they leave backHref off. --}}
        @if ($backHref)
            <a href="{{ $backHref }}" aria-label="Back"
               class="mt-[8px] flex size-[32px] shrink-0 items-center justify-center rounded-full border border-editorial-line text-editorial-ink
                      transition-colors duration-300 hover:bg-[#f1f4f6]">&larr;</a>
        @endif

        <div data-reveal>
            <h1 class="font-jakarta text-[36px] font-bold tracking-[-0.96px] text-admin-ink">{{ $heading }}</h1>
            <p class="mt-[4px] font-jakarta text-[16px] text-admin-muted">{{ $subtitle }}</p>
        </div>
    </div>

    @isset($actions)
        <div class="flex items-center gap-[12px]">
            {{ $actions }}
        </div>
    @endisset
</div>

<div class="mt-[24px]">
    {{ $slot }}
</div>
