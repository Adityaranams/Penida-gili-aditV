{{-- Stacked radio cards — Publishing Status / Publishing Settings
     (Figma 1:8502, 1:8059). Each option carries a `value`; `selected` is the current value.

     The highlight is rendered here for the saved value and moved by
     resources/js/radio-cards.js as soon as the admin picks another card, so the
     blue outline follows the dot instead of waiting for the next page load. --}}
@props(['name', 'options', 'selected' => null])

@php $current = old($name, $selected); @endphp

<div class="flex flex-col gap-[10px]" data-radio-cards>
    @foreach ($options as $option)
        @php $checked = (string) $current === (string) $option['value']; @endphp
        <label data-radio-card @class([
            'flex cursor-pointer items-start gap-[12px] rounded-[10px] border p-[14px] transition-colors duration-300',
            'border-editorial bg-editorial/5' => $checked,
            'border-[rgba(192,199,211,0.5)] hover:bg-[#f7fafc]' => ! $checked,
        ])>
            <input type="radio" name="{{ $name }}" value="{{ $option['value'] }}" @checked($checked)
                   class="mt-[3px] size-[16px] accent-[#005ea1]">
            <span>
                <span class="block font-jakarta text-[15px] font-semibold text-editorial-ink">{{ $option['label'] }}</span>
                <span class="block font-jakarta text-[13px] leading-[18px] text-editorial-body">{{ $option['description'] }}</span>
            </span>
        </label>
    @endforeach

    @error($name)
        <span class="font-jakarta text-[13px] leading-[18px] text-[#dc2626]">{{ $message }}</span>
    @enderror
</div>
