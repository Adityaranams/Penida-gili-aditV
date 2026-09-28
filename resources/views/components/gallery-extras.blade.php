{{-- Photos past what the collage has room for. They stay hidden but keep the same
     lightbox group, so the viewer can still page through every photo. --}}
@props(['photos', 'group'])

@foreach ($photos as $photo)
    <button type="button" hidden aria-hidden="true" tabindex="-1"
            data-lightbox-group="{{ $group }}" data-lightbox="{{ $photo['url'] }}" data-lightbox-alt="{{ $photo['alt'] }}"></button>
@endforeach
