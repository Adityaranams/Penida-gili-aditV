{{-- Figma node 1:622 / 1:277 — Footer --}}
@php
    $bookingLinks = [
        'Our Story'     => '#',
        'How We Help'   => '#',
        'Boat  Pricing' => '#',
        'Privacy Policy'=> '#',
    ];
    $socials = [
        'Dribbble'  => 'social-dribbble.svg',
        'Behance'   => 'social-behance.svg',
        'Instagram' => 'social-instagram.svg',
        'Twitter'   => 'social-twitter.svg',
    ];
@endphp

{{-- Mobile footer — Figma node 1:2566 --}}
<footer class="border-t border-[#e5e7eb] bg-white px-[16px] pb-[32px] pt-[26px] lg:hidden">
    <div class="flex flex-col gap-[16px] pb-[24px]">
        <div class="flex items-center gap-[5px]">
            <img src="{{ asset('images/logo/logo-mark-dark.svg') }}" alt="" class="h-[37px] w-[74px]">
            <img src="{{ asset('images/logo/logo-word-dark.svg') }}" alt="Penida Gili" class="h-[35px] w-[159px]">
        </div>
        <p class="text-[16px] leading-[30px] text-ink-muted">Penida Gili is your premier Boat. We provide boat rental services.</p>
    </div>

    <div class="pt-[10px]">
        <h2 class="text-[18px] font-semibold leading-[27px] text-ink-muted">Join a Newsletter</h2>
        <form action="{{ route('newsletter.store') }}" method="post" data-confirm="newsletter" class="mt-[24px] flex gap-[8px]">
            @csrf
            <input type="hidden" name="source" value="footer-mobile">
            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
            <input name="email" type="email" required value="{{ old('email') }}" placeholder="Your Email" aria-label="Your Email"
                   class="min-w-0 flex-1 rounded-[8px] bg-[#ebeef0] px-[16px] py-[10px] text-[16px] text-ink placeholder:text-[#6b7280] focus:outline-2 focus:outline-brand">
            <button type="submit" class="shrink-0 rounded-[8px] bg-brand px-[16px] py-[8px] text-[16px] font-semibold leading-[24px] text-on-brand">Submit</button>
            @if (session('newsletter'))
                <p class="w-full basis-full text-[13px] leading-[18px] text-ink-muted">{{ session('newsletter') }}</p>
            @endif
            @error('email')
                <p class="w-full basis-full text-[13px] leading-[18px] text-ink-muted !text-red-500">{{ $message }}</p>
            @enderror
        </form>
    </div>

    <ul class="mt-[10px] flex h-[54px] items-start gap-[16px]">
        @foreach (['Website' => 'social-1.svg', 'Link' => 'social-2.svg', 'Share' => 'social-3.svg'] as $name => $icon)
            <li>
                <a href="#" aria-label="{{ $name }}" class="flex size-[40px] items-center justify-center rounded-full bg-brand">
                    <img src="{{ asset('images/icons/mobile/'.$icon) }}" alt="" class="size-[12px] object-contain">
                </a>
            </li>
        @endforeach
    </ul>

    <p class="border-t border-[#f3f4f6] pt-[17px] text-center text-[14px] leading-[20px] text-[#64748b]">
        &copy; Copyright Penida Gili {{ date('Y') }} | Design &amp; Develop MaiHarta
    </p>
</footer>

{{-- Desktop footer --}}
<footer class="hidden border-t border-line-card pt-[36px] lg:pt-[86px] pb-[36px] lg:block">
    <div class="container-page grid gap-16 lg:grid-cols-[1fr_1fr_1fr]">
        {{-- Logo + blurb --}}
        <div>
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/logo/logo-mark-dark.svg') }}" alt="" class="h-[61px] w-[112px]">
                <img src="{{ asset('images/logo/logo-word-dark.svg') }}" alt="Penida Gili" class="h-[61px] w-[231px]">
            </div>
            <p class="mt-[24px] max-w-[310px] text-[16px] leading-[30px] text-ink-muted">
                Penida Gili is your premier Boat. We provide boat rental services.
            </p>
            <p class="mt-[74px] lg:mt-[176px] text-[16px] leading-[30px] text-ink-muted">Penida Gili</p>
        </div>

        {{-- Section 02 --}}
        <div>
            <h2 class="text-[20px] leading-[30px] text-ink">Boat Booking</h2>
            <ul class="mt-[19px]">
                @foreach ($bookingLinks as $label => $href)
                    <li>
                        <a href="{{ $href }}"
                           class="inline-block text-[16px] leading-[30px] lg:leading-[49px] text-ink-muted transition-[color,transform] duration-300 ease-smooth hover:translate-x-1 hover:text-ink">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Section 04 --}}
        <div>
            <h2 class="text-[20px] leading-[30px] text-ink">Join a Newsletter</h2>

            <form action="{{ route('newsletter.store') }}" method="post" data-confirm="newsletter" class="mt-[28px] lg:mt-[36px]">
                @csrf
                <input type="hidden" name="source" value="footer">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <label for="newsletter-email" class="block text-[16px] leading-[30px] text-ink-muted">Your Email</label>
                <div class="mt-[14px] flex items-center gap-[32px]">
                    <input id="newsletter-email" name="email" type="email" required value="{{ old('email') }}" placeholder="Enter Your Email"
                           class="h-[58px] w-full max-w-[326px] rounded-field bg-surface-muted px-[26px] text-[16px] leading-[30px] text-ink placeholder:text-ink-muted focus:outline-2 focus:outline-brand">
                    <button type="submit"
                            class="h-[58px] w-[116px] shrink-0 rounded-field bg-brand text-[16px] font-medium leading-[30px] text-on-brand backdrop-blur-[4.7px]
                                   transition-[transform,box-shadow] duration-300 ease-smooth hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand/30">
                        Submit
                    </button>
                </div>
                @if (session('newsletter'))
                    <p class="mt-[10px] text-[14px] leading-[20px] text-ink-muted">{{ session('newsletter') }}</p>
                @endif
                @error('email')
                    <p class="mt-[10px] text-[14px] leading-[20px] text-ink-muted !text-red-500">{{ $message }}</p>
                @enderror
            </form>

            <ul class="mt-[24px] lg:mt-[50px] flex items-center gap-[20px]">
                @foreach ($socials as $name => $icon)
                    <li>
                        <a href="#" aria-label="{{ $name }}"
                           class="block size-[54px] transition-transform duration-300 ease-smooth hover:-translate-y-1">
                            <img src="{{ asset('images/icons/'.$icon) }}" alt="" class="size-full">
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="container-page mt-[50px] border-t border-line-card pt-[20px]">
        <p class="text-center text-[16px] leading-[30px] text-ink-muted">
            &copy; Copyright Penida Gili  {{ date('Y') }} | Design &amp; Develop 
            <a href="https://www.maiharta.com/" target="_blank" rel="noopener"class="text-black hover:text-blue-600 hover:underline transition">CV Maiharta</a>
            <span class="mx-[5px]">|</span>
            <a href="http://localhost:8000/admin" target="_blank" rel="noopener"class="text-black hover:text-blue-600 hover:underline transition">Admin</a>
        </p>
    </div>
</footer>
