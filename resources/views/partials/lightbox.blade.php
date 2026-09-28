{{-- Full-size photo viewer. Opened by resources/js/lightbox.js from any [data-lightbox] control;
     controls with a shared data-lightbox-group can be paged through with the arrows. --}}
<dialog id="photo-lightbox" class="m-auto max-h-[96vh] max-w-[96vw] rounded-[16px] border-0 bg-transparent p-0 backdrop:bg-black/80">
    <div class="relative">
        {{-- h-[88vh] rather than max-h: a small photo is scaled up to fill the viewer too. --}}
        <img data-lightbox-image src="" alt="" class="h-[88vh] w-auto max-w-[94vw] rounded-[16px] object-contain">

        <button type="button" data-lightbox-close aria-label="Close photo"
                class="absolute right-[12px] top-[12px] flex size-[38px] items-center justify-center rounded-full bg-black/60 text-[22px] leading-none text-white transition-colors hover:bg-black/80">
            &times;
        </button>

        <button type="button" data-lightbox-prev aria-label="Previous photo"
                class="absolute left-[12px] top-1/2 flex size-[44px] -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition-colors hover:bg-black/80">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[22px]" aria-hidden="true">
                <path d="M15 5l-7 7 7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <button type="button" data-lightbox-next aria-label="Next photo"
                class="absolute right-[12px] top-1/2 flex size-[44px] -translate-y-1/2 items-center justify-center rounded-full bg-black/55 text-white transition-colors hover:bg-black/80">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-[22px]" aria-hidden="true">
                <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <p data-lightbox-counter class="absolute inset-x-0 bottom-[12px] text-center text-[14px] font-semibold text-white/90 [text-shadow:0_1px_3px_rgba(0,0,0,0.6)]"></p>
    </div>
</dialog>
