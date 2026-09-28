{{-- Sits in the filter bar and submits the <x-admin.bulk-delete> form that lives
     outside it. Hidden until at least one row is ticked. --}}
<button type="submit" form="bulk-delete" data-bulk-submit hidden
        {{ $attributes->merge(['class' => 'h-[46px] rounded-[8px] border border-[#fecaca] bg-[#fee2e2] px-[24px] text-[16px] font-semibold text-[#b91c1c] transition-colors duration-300 hover:bg-[#fecaca]']) }}>
    Delete<span data-bulk-count></span>
</button>
