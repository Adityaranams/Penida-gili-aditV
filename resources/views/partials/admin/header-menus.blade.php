{{-- The console header's two dropdowns: the notification bell and the account
     menu beside it. Both are plain <details> elements, so they open and close
     without JavaScript; resources/js/admin-header.js only fills the bell with
     live bookings and keeps one open at a time. --}}
<div class="flex items-center gap-[12px]">
    <details class="relative" data-admin-bell
             data-feed="{{ route('admin.notifications') }}"
             data-seen="{{ route('admin.notifications.seen') }}">
        <summary class="relative flex size-[58.7px] cursor-pointer list-none items-center justify-center rounded-full bg-admin-field transition-colors duration-300 hover:bg-editorial-rule [&::-webkit-details-marker]:hidden"
                 aria-label="Notifications" role="button">
            <img src="{{ asset('images/icons/admin/bell.svg') }}" alt="" class="size-[22px]">
            <span data-bell-dot hidden
                  class="absolute right-[16px] top-[14px] flex min-w-[18px] items-center justify-center rounded-full bg-[#e11d48] px-[5px] font-jakarta text-[11px] font-bold leading-[18px] text-white"></span>
        </summary>

        <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-[340px] overflow-hidden rounded-[16px] border border-admin-line bg-surface shadow-[0_20px_40px_-12px_rgba(0,0,0,0.25)]">
            <p class="border-b border-admin-line px-[18px] py-[14px] font-jakarta text-[14px] font-bold leading-[20px] text-editorial-ink">
                Latest Bookings
            </p>

            <div data-bell-list class="max-h-[360px] overflow-y-auto">
                <p class="px-[18px] py-[22px] font-jakarta text-[13px] leading-[18px] text-editorial-body">Loading…</p>
            </div>

            <a href="{{ route('admin.report') }}"
               class="block border-t border-admin-line px-[18px] py-[13px] text-center font-jakarta text-[13px] font-semibold text-editorial transition-colors hover:bg-[#f1f4f6]">
                View all in Booking Report
            </a>
        </div>
    </details>

    <details class="group relative" data-admin-account>
        {{-- An initials avatar rather than a bare caret, so the control says
             whose account it opens — the sidebar card is the only other place
             the signed-in name appears. --}}
        <summary class="flex h-[58.7px] cursor-pointer list-none items-center gap-[10px] rounded-admin-pill bg-admin-field pl-[8px] pr-[16px] transition-colors duration-300 hover:bg-editorial-rule [&::-webkit-details-marker]:hidden"
                 aria-label="Account menu" role="button">
            <span aria-hidden="true"
                  class="flex size-[42px] shrink-0 items-center justify-center rounded-full bg-editorial font-jakarta text-[15px] font-bold leading-[20px] text-white">
                {{ auth()->user()?->initials() }}
            </span>
            <span class="hidden min-w-0 flex-col items-start xl:flex">
                <span class="max-w-[140px] truncate font-jakarta text-[14px] font-semibold leading-[18px] text-admin-ink">{{ auth()->user()?->name }}</span>
                <span class="font-jakarta text-[12px] leading-[16px] text-admin-muted">Administrator</span>
            </span>
            <x-admin.icon name="chevron-down.svg" class="size-[14px] bg-admin-nav transition-transform duration-300 group-open:rotate-180" />
        </summary>

        <div class="absolute right-0 top-[calc(100%+10px)] z-50 w-[248px] overflow-hidden rounded-[16px] border border-admin-line bg-surface shadow-[0_20px_40px_-12px_rgba(0,0,0,0.25)]">
            <div class="border-b border-admin-line px-[18px] py-[14px]">
                <p class="truncate font-jakarta text-[14px] font-bold leading-[20px] text-editorial-ink">{{ auth()->user()?->name }}</p>
                <p class="truncate font-jakarta text-[12px] leading-[16px] text-editorial-body">{{ auth()->user()?->email }}</p>
            </div>

            {{-- Hrefs, not buttons: without JavaScript these still reach the
                 standalone page. profile-modal.js intercepts the click. --}}
            <a href="{{ route('admin.profile') }}" data-profile-open="account"
               class="flex items-center gap-[10px] px-[18px] py-[12px] font-jakarta text-[14px] leading-[20px] text-editorial-ink transition-colors hover:bg-[#f1f4f6]">
                <x-admin.icon name="crown.svg" class="size-[16px] bg-editorial" />
                Profile
            </a>

            <a href="{{ route('admin.profile') }}#password" data-profile-open="password"
               class="flex items-center gap-[10px] px-[18px] py-[12px] font-jakarta text-[14px] leading-[20px] text-editorial-ink transition-colors hover:bg-[#f1f4f6]">
                <x-admin.icon name="nav-settings.svg" class="size-[16px] bg-editorial" />
                Change Password
            </a>

            <form action="{{ route('admin.logout') }}" method="post" class="border-t border-admin-line">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-[10px] px-[18px] py-[12px] font-jakarta text-[14px] leading-[20px] text-[#dc2626] transition-colors hover:bg-[#fef2f2]">
                    <x-admin.icon name="logout.svg" class="size-[16px] -scale-x-100 bg-[#dc2626]" />
                    Logout
                </button>
            </form>
        </div>
    </details>
</div>
