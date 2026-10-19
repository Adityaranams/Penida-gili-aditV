{{-- The signed-in admin's own account. Reached from the account menu in the
     header; "Change Password" lands on the #password card below. --}}
@extends('layouts.admin')

@php
    $label = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-body';
    $input = 'w-full rounded-[8px] border border-[#c0c7d3] bg-surface px-[13px] py-[11px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial';
    $hint = 'font-jakarta text-[12px] leading-[16px] text-editorial-body';
    $error = 'font-jakarta text-[13px] text-[#dc2626]';
    $submit = 'flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[10px] font-jakarta text-[14px] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)] transition-transform duration-300 ease-smooth hover:-translate-y-0.5';
@endphp

@section('title', 'Profile')

{{-- Not a sidebar destination, so nothing in the menu should look selected. --}}
@section('admin-active', 'profile')

@section('content')
    <x-admin.form-page
        heading="Profile"
        subtitle="Your account details and password. The same forms open as a dialog from the account menu.">

        <div class="flex max-w-[760px] flex-col gap-[24px]">
            <x-admin.form-section title="Account" icon="crown.svg">
                <form action="{{ route('admin.profile.update') }}" method="post" class="flex flex-col gap-[24px]">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-[8px]">
                        <label for="name" class="{{ $label }}">Name <span class="text-[#ba1a1a]">*</span></label>
                        <input id="name" name="name" required value="{{ old('name', $user->name) }}" class="{{ $input }}">
                        <p class="{{ $hint }}">Shown in the console header and on the sidebar card.</p>
                        @error('name', 'profile') <span class="{{ $error }}">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-[8px]">
                        <label for="email" class="{{ $label }}">Email <span class="text-[#ba1a1a]">*</span></label>
                        <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}" class="{{ $input }}">
                        <p class="{{ $hint }}">You sign in with this address.</p>
                        @error('email', 'profile') <span class="{{ $error }}">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <button type="submit" class="{{ $submit }}">
                            <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-white" />
                            Save Profile
                        </button>
                    </div>
                </form>
            </x-admin.form-section>

            <x-admin.form-section title="Change Password" icon="nav-settings.svg">
                <form id="password" action="{{ route('admin.profile.password') }}" method="post" class="flex flex-col gap-[24px]">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-[8px]">
                        <label for="current_password" class="{{ $label }}">Current Password <span class="text-[#ba1a1a]">*</span></label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="{{ $input }}">
                        @error('current_password', 'password') <span class="{{ $error }}">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-[8px]">
                        <label for="password" class="{{ $label }}">New Password <span class="text-[#ba1a1a]">*</span></label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="{{ $input }}">
                        <p class="{{ $hint }}">At least 8 characters.</p>
                        @error('password', 'password') <span class="{{ $error }}">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-[8px]">
                        <label for="password_confirmation" class="{{ $label }}">Confirm New Password <span class="text-[#ba1a1a]">*</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $input }}">
                    </div>

                    <div>
                        <button type="submit" class="{{ $submit }}">
                            <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-white" />
                            Change Password
                        </button>
                    </div>
                </form>
            </x-admin.form-section>
        </div>
    </x-admin.form-page>
@endsection
