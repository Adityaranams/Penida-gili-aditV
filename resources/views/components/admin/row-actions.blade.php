{{-- Edit / delete controls at the end of each console row (Figma 1:6977).
     Delete is a real form so it works without JavaScript; the dialog in
     partials/admin/delete-modal.blade.php is the courtesy on top. --}}
@props(['label', 'editHref' => null, 'deleteAction' => null, 'align' => 'end'])

<span class="flex items-center gap-[4px] {{ $align === 'center' ? 'justify-center' : 'justify-end' }}">
    @if ($editHref)
        <a href="{{ $editHref }}" aria-label="Edit {{ $label }}"
           class="flex size-[32px] items-center justify-center rounded-full transition-colors duration-300 hover:bg-[#f1f4f6]">
            <img src="{{ asset('images/icons/admin/action-edit.svg') }}" alt="" class="size-[15px]">
        </a>
    @endif

    @if ($deleteAction)
        <form action="{{ $deleteAction }}" method="post" data-confirm-delete="{{ $label }}">
            @csrf
            @method('DELETE')
            <button type="submit" aria-label="Delete {{ $label }}"
                    class="flex size-[32px] items-center justify-center rounded-full transition-colors duration-300 hover:bg-[#fee2e2]">
                <img src="{{ asset('images/icons/admin/action-delete.svg') }}" alt="" class="h-[15px] w-[13.3px]">
            </button>
        </form>
    @endif
</span>
