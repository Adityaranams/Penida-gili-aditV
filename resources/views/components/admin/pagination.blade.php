{{-- Footer pagination for console tables (Figma 1:10660): summary on the left, Prev / page
     numbers / Next on the right. The buttons always show — they simply sit disabled while
     there is a single page — so every console screen carries the same bar.
     `entity` names what is counted ("schedules"), `anchor` keeps in-page tables in view. --}}
@props(['paginator', 'entity' => 'entries', 'anchor' => null])

@php
    $base = 'rounded-[6px] border border-editorial-line px-[14px] py-[6px] text-[14px] transition-colors';
    $enabled = $base.' text-editorial-ink hover:bg-[#f1f4f6]';
    $disabled = $base.' cursor-not-allowed text-admin-nav opacity-60';
    $url = fn (?string $link) => $link ? $link.$anchor : null;
@endphp

<p class="text-[14px] leading-[20px] text-editorial-body">
    @if ($paginator->total() > 0)
        Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ number_format($paginator->total()) }} {{ $entity }}
    @else
        No {{ $entity }} yet
    @endif
</p>

<nav class="flex items-center gap-[8px]" aria-label="Pagination">
    @if ($paginator->onFirstPage())
        <span class="{{ $disabled }}">Prev</span>
    @else
        <a href="{{ $url($paginator->previousPageUrl()) }}" rel="prev" class="{{ $enabled }}">Prev</a>
    @endif

    @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $link)
        @if ($page === $paginator->currentPage())
            <span class="rounded-[6px] bg-editorial px-[14px] py-[6px] text-[14px] text-white">{{ $page }}</span>
        @else
            <a href="{{ $url($link) }}" class="{{ $enabled }}">{{ $page }}</a>
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $url($paginator->nextPageUrl()) }}" rel="next" class="{{ $enabled }}">Next</a>
    @else
        <span class="{{ $disabled }}">Next</span>
    @endif
</nav>
