{{-- The console's delete confirmation, in place of the browser's own confirm()
     box. Driven by resources/js/confirm-delete.js; a form opts in with
     data-confirm-delete="<what is being deleted>". Without JavaScript the form
     posts straight through, exactly as it did before. --}}
<dialog id="delete-modal"
        class="m-auto w-[calc(100%-32px)] max-w-[420px] rounded-[16px] border border-[rgba(192,199,211,0.3)] bg-white p-0 text-editorial-ink shadow-[0px_20px_40px_-12px_rgba(0,0,0,0.25)]
               backdrop:bg-[rgba(24,28,30,0.55)] backdrop:backdrop-blur-[2px]"
        aria-labelledby="delete-modal-title">
    <form method="dialog" class="flex flex-col">
        <div class="flex items-start gap-[16px] px-[24px] pb-[8px] pt-[24px]">
            <span class="flex size-[44px] shrink-0 items-center justify-center rounded-full bg-[#fee2e2]" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" class="size-[22px]">
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/>
                </svg>
            </span>

            <div class="min-w-0">
                <h2 id="delete-modal-title" class="font-jakarta text-[18px] font-bold leading-[28px]">Delete this item?</h2>
                <p class="mt-[4px] break-words font-jakarta text-[14px] leading-[20px] text-editorial-body">
                    <span data-delete-label class="font-semibold text-editorial-ink"></span>
                    will be removed for good. This cannot be undone.
                </p>
            </div>
        </div>

        <div class="mt-[16px] flex items-center justify-end gap-[10px] border-t border-[rgba(192,199,211,0.3)] bg-[#f7fafc] px-[24px] py-[16px]">
            <button type="submit" value="cancel"
                    class="min-h-[40px] rounded-[8px] border border-[#c0c7d3] bg-white px-[18px] font-jakarta text-[14px] font-semibold leading-[20px] text-editorial-body transition-colors hover:bg-[#f1f4f6]">
                Cancel
            </button>
            <button type="submit" value="confirm" data-delete-accept
                    class="min-h-[40px] rounded-[8px] bg-[#dc2626] px-[18px] font-jakarta text-[14px] font-semibold leading-[20px] text-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1)]
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                Delete
            </button>
        </div>
    </form>
</dialog>
