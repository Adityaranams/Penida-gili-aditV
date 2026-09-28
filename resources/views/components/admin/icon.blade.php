{{-- Console glyphs ship with their colour baked into the SVG, so recolouring means
     masking rather than <img> (the same trick the sidebar uses for its nav icons).
     Pass the size and the colour as classes: <x-admin.icon name="form-save.svg" class="size-[16px] bg-editorial" /> --}}
@props(['name'])

<span aria-hidden="true"
      style="-webkit-mask: url('{{ asset('images/icons/admin/'.$name) }}') no-repeat center / contain; mask: url('{{ asset('images/icons/admin/'.$name) }}') no-repeat center / contain;"
      {{ $attributes->merge(['class' => 'block shrink-0']) }}></span>
