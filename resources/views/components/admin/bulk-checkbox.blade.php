{{-- Row ticker for bulk delete. Belongs to the #bulk-delete form, not the filter form. --}}
@props(['value', 'label'])

<td class="w-[52px] pl-[20px] pr-[4px]">
    <input type="checkbox" name="ids[]" value="{{ $value }}" form="bulk-delete" data-bulk-item
           aria-label="Select {{ $label }}"
           class="size-[18px] cursor-pointer rounded-[4px] accent-editorial">
</td>
