{{-- Thumbnails of the images already attached to a catalogue record.

     Pass `remove` (a field name) to let the admin drop individual photos: ticking one
     queues its path in that field, and the controller keeps every photo left alone. --}}
@props(['items' => [], 'folder', 'cover' => null, 'remove' => null])

@if ($cover || $items)
    <div class="mt-[20px] grid [&>*]:min-w-0 gap-[16px] sm:grid-cols-3">
        @if ($cover)
            <figure class="relative overflow-hidden rounded-[10px]">
                <img src="{{ \App\Support\ImagePath::url($cover, $folder) }}" alt="" class="h-[120px] w-full object-cover">
                <figcaption class="absolute left-[8px] top-[8px] rounded-[6px] bg-editorial px-[8px] py-[3px] font-jakarta text-[11px] font-semibold text-white">Cover</figcaption>
            </figure>
        @endif

        @foreach ($items as $photo)
            @php($path = $photo['image'] ?? $photo)

            <figure class="relative overflow-hidden rounded-[10px]">
                <img src="{{ \App\Support\ImagePath::url($path, $folder.'/detail') }}" alt="{{ $photo['alt'] ?? '' }}" class="h-[120px] w-full object-cover">

                @if ($remove)
                    <label class="absolute inset-0 cursor-pointer">
                        <input type="checkbox" name="{{ $remove }}[]" value="{{ $path }}" class="peer sr-only">

                        <span class="absolute right-[8px] top-[8px] flex size-[28px] items-center justify-center rounded-full bg-white/90 text-[16px] leading-none text-[#b91c1c] shadow-sm
                                     transition-colors peer-checked:bg-[#b91c1c] peer-checked:text-white">&times;</span>

                        <span class="absolute inset-0 hidden items-center justify-center bg-[rgba(185,28,28,0.65)] font-jakarta text-[12px] font-semibold text-white peer-checked:flex">
                            Removed on save
                        </span>
                    </label>
                @endif
            </figure>
        @endforeach
    </div>
@endif
