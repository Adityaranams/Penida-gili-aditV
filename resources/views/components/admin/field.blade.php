{{-- Label + control pair used throughout the console forms (Figma 1:7166).
     Values fall back to old() so a failed submit keeps what the admin typed;
     `options` accepts a flat list or a value => label map. --}}
@props([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'options' => null,
    'required' => false,
    'prefix' => null,
    'help' => null,
    'step' => null,
    'min' => null,
    'max' => null,
])

@php
    // Repeating rows use bracket names (foo[0][bar]); old() and the error bag use dots.
    $key = str_replace(['[', ']'], ['.', ''], $name);
    $current = old($key, $value);

    $control = 'w-full rounded-[8px] border bg-[#f7fafc] px-[17px] py-[13px]'
        .' font-jakarta text-[16px] leading-[24px] text-editorial-ink placeholder:text-editorial-meta'
        .' focus:border-editorial focus:outline-none';
    $control .= $errors->has($key) ? ' border-[#dc2626]' : ' border-[rgba(192,199,211,0.5)]';
    $isList = is_array($options) && array_is_list($options);
@endphp

<label class="flex flex-col gap-[8px]">
    <span class="font-jakarta text-[14px] font-semibold leading-[20px] tracking-[0.7px] text-editorial-ink">
        {{ $label }}@if ($required) *@endif
    </span>

    @if ($options !== null)
        <span class="relative block">
            <select name="{{ $name }}" @required($required) class="{{ $control }} appearance-none pr-[44px]">
                @if ($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach ($options as $optionKey => $option)
                    @php $optionValue = $isList ? $option : $optionKey; @endphp
                    <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $option }}</option>
                @endforeach
            </select>
            <img src="{{ asset('images/icons/admin/form-chevron.svg') }}" alt=""
                 class="pointer-events-none absolute right-[12px] top-1/2 size-[24px] -translate-y-1/2">
        </span>
    @elseif ($type === 'textarea')
        <textarea name="{{ $name }}" rows="4" placeholder="{{ $placeholder }}" @required($required) class="{{ $control }}">{{ $current }}</textarea>
    @elseif ($prefix)
        <span class="flex items-center rounded-[8px] border {{ $errors->has($key) ? 'border-[#dc2626]' : 'border-[rgba(192,199,211,0.5)]' }} bg-[#f7fafc] pl-[17px]">
            <span class="font-jakarta text-[16px] leading-[24px] text-editorial-body">{{ $prefix }}</span>
            <input type="{{ $type }}" name="{{ $name }}" value="{{ $current }}" placeholder="{{ $placeholder }}" @required($required)
                   class="w-full bg-transparent px-[10px] py-[13px] font-jakarta text-[16px] leading-[24px] text-editorial-ink placeholder:text-editorial-meta focus:outline-none">
        </span>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $current }}" placeholder="{{ $placeholder }}" @required($required)
               @if ($step !== null) step="{{ $step }}" @endif
               @if ($min !== null) min="{{ $min }}" @endif
               @if ($max !== null) max="{{ $max }}" @endif
               class="{{ $control }}">
    @endif

    @if ($help)
        <span class="font-jakarta text-[13px] leading-[18px] text-editorial-body">{{ $help }}</span>
    @endif

    @error($key)
        <span class="font-jakarta text-[13px] leading-[18px] text-[#dc2626]">{{ $message }}</span>
    @enderror
</label>
