{{-- The signed-in admin's account, as a dialog over whatever page they are on.

     It lives in the layout rather than on a page of its own because changing
     your own name or password is never the task you came to the console for —
     sending someone to a separate screen would lose their place. Opened from
     the header's account menu; resources/js/profile-modal.js does the rest.
     Without JavaScript the <dialog> stays shut, so both forms also remain
     reachable at /admin/profile. --}}
@php
    $user = auth()->user();
    $label = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-body';
    $input = 'w-full rounded-[8px] border border-[#c0c7d3] bg-surface px-[13px] py-[11px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial';
    $hint = 'font-jakarta text-[12px] leading-[16px] text-editorial-body';
    $err = 'font-jakarta text-[13px] text-[#dc2626]';
    $submit = 'flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[10px] font-jakarta text-[14px] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)] transition-transform duration-300 ease-smooth hover:-translate-y-0.5';
    $tab = 'flex-1 rounded-[8px] px-[14px] py-[9px] font-jakarta text-[14px] font-semibold leading-[20px] transition-colors duration-300';

    // A failed save bounces back here; reopen on the tab that failed.
    $openTab = $errors->getBag('password')->any() ? 'password' : ($errors->getBag('profile')->any() ? 'account' : null);
@endphp

<dialog id="profile-modal" data-open-tab="{{ $openTab }}"
        class="m-auto w-[calc(100%-32px)] max-w-[520px] rounded-[16px] border border-admin-line bg-surface p-0 text-editorial-ink shadow-[0px_20px_40px_-12px_rgba(0,0,0,0.25)]
               backdrop:bg-[rgba(24,28,30,0.55)] backdrop:backdrop-blur-[2px]"
        aria-labelledby="profile-modal-title">

    <div class="flex items-center gap-[14px] border-b border-admin-line px-[24px] py-[20px]">
        <span aria-hidden="true"
              class="flex size-[48px] shrink-0 items-center justify-center rounded-full bg-editorial font-jakarta text-[17px] font-bold leading-[24px] text-white">
            {{ $user?->initials() }}
        </span>
        <div class="min-w-0 flex-1">
            <h2 id="profile-modal-title" class="truncate font-jakarta text-[18px] font-bold leading-[26px]">{{ $user?->name }}</h2>
            <p class="truncate font-jakarta text-[13px] leading-[18px] text-editorial-body">{{ $user?->email }}</p>
        </div>
        <button type="button" data-profile-close aria-label="Close"
                class="flex size-[32px] shrink-0 items-center justify-center rounded-full border border-admin-line text-editorial-body transition-colors duration-300 hover:bg-[#f1f4f6]">&times;</button>
    </div>

    <div class="flex gap-[6px] border-b border-admin-line bg-[#f7fafc] px-[24px] py-[12px]">
        <button type="button" data-profile-tab="account" class="{{ $tab }}">Account</button>
        <button type="button" data-profile-tab="password" class="{{ $tab }}">Password</button>
    </div>

    <div data-profile-panel="account" class="px-[24px] py-[22px]">
        <form action="{{ route('admin.profile.update') }}" method="post" class="flex flex-col gap-[18px]">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-[8px]">
                <label for="profile-name" class="{{ $label }}">Name <span class="text-[#ba1a1a]">*</span></label>
                <input id="profile-name" name="name" required value="{{ old('name', $user?->name) }}" class="{{ $input }}">
                <p class="{{ $hint }}">Shown in the console header and on the sidebar card.</p>
                @error('name', 'profile') <span class="{{ $err }}">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-[8px]">
                <label for="profile-email" class="{{ $label }}">Email <span class="text-[#ba1a1a]">*</span></label>
                <input id="profile-email" name="email" type="email" required value="{{ old('email', $user?->email) }}" class="{{ $input }}">
                <p class="{{ $hint }}">You sign in with this address.</p>
                @error('email', 'profile') <span class="{{ $err }}">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="{{ $submit }}">
                    <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-white" />
                    Save Profile
                </button>
            </div>
        </form>
    </div>

    <div data-profile-panel="password" class="px-[24px] py-[22px]">
        <form action="{{ route('admin.profile.password') }}" method="post" class="flex flex-col gap-[18px]">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-[8px]">
                <label for="profile-current" class="{{ $label }}">Current Password <span class="text-[#ba1a1a]">*</span></label>
                <input id="profile-current" name="current_password" type="password" required autocomplete="current-password" class="{{ $input }}">
                @error('current_password', 'password') <span class="{{ $err }}">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-[8px]">
                <label for="profile-password" class="{{ $label }}">New Password <span class="text-[#ba1a1a]">*</span></label>
                <input id="profile-password" name="password" type="password" required autocomplete="new-password" class="{{ $input }}">
                <p class="{{ $hint }}">At least 8 characters.</p>
                @error('password', 'password') <span class="{{ $err }}">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col gap-[8px]">
                <label for="profile-confirm" class="{{ $label }}">Confirm New Password <span class="text-[#ba1a1a]">*</span></label>
                <input id="profile-confirm" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $input }}">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="{{ $submit }}">
                    <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-white" />
                    Change Password
                </button>
            </div>
        </form>
    </div>
</dialog>
