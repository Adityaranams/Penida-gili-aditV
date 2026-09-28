{{-- Five stars matching Figma's stars.svg, but filled to the actual rating:
     4.5 leaves the last star half gold. The gold row is clipped to the exact
     width of the score, so whole and half stars land where you expect. --}}
@props(['rating' => 0, 'size' => 22.4, 'gap' => 5.4])

@php
    $value = max(0, min(5, (float) $rating));
    $whole = (int) floor($value);
    $fraction = $value - $whole;

    $track = 5 * $size + 4 * $gap;
    $filled = min($track, $whole * ($size + $gap) + $fraction * $size);

    $glyph = 'M3.1875 15.8333L4.54167 9.97917L0 6.04167L6 5.52083L8.33333 0L10.6667 5.52083L16.6667 6.04167L12.125 9.97917L13.4792 15.8333L8.33333 12.7292L3.1875 15.8333V15.8333';
@endphp

<span {{ $attributes->merge(['class' => 'relative inline-block shrink-0']) }}
      style="width: {{ $track }}px; height: {{ $size }}px"
      role="img" aria-label="{{ rtrim(rtrim(number_format($value, 1), '0'), '.') }} out of 5 stars">
    @foreach (['#E3E3E3' => null, '#FFD24C' => $filled] as $colour => $clip)
        <span @class(['flex items-center', 'absolute inset-y-0 left-0 overflow-hidden' => $clip !== null])
              @style(["gap: {$gap}px", "width: {$clip}px" => $clip !== null])>
            <span class="flex items-center shrink-0" style="gap: {{ $gap }}px; width: {{ $track }}px">
                @for ($i = 0; $i < 5; $i++)
                    <svg viewBox="0 0 16.6667 15.8333" fill="none" xmlns="http://www.w3.org/2000/svg"
                         class="shrink-0" style="width: {{ $size }}px; height: auto" aria-hidden="true">
                        <path d="{{ $glyph }}" fill="{{ $colour }}"/>
                    </svg>
                @endfor
            </span>
        </span>
    @endforeach
</span>
