{{-- Console Setting screen: the contact details the public site shows.

     Everything here used to sit in .env, so changing a phone number needed a
     shell on the server. Saving writes a database row that overrides the
     config default on every page at once (App\Support\SiteSettings). --}}
@extends('layouts.admin')

@php
    $label = 'font-jakarta text-[12px] font-bold uppercase leading-[16px] tracking-[0.6px] text-editorial-body';
    $input = 'w-full rounded-[8px] border border-[#c0c7d3] bg-surface px-[13px] py-[11px] font-jakarta text-[14px] leading-[20px] text-editorial-ink placeholder:text-editorial-meta focus:outline-2 focus:outline-editorial';
    $hint = 'font-jakarta text-[12px] leading-[16px] text-editorial-body';
@endphp

@section('title', 'Setting')

@section('admin-active', 'setting')

@section('hide-search', 'yes')

@section('content')
    <x-admin.form-page
        heading="Setting"
        subtitle="Contact details the public site uses for Contact Us, Book With Email, and every WhatsApp button.">

        <x-slot:actions>
            <button type="submit" form="settings-form"
                    class="flex items-center gap-[8px] rounded-[8px] bg-editorial px-[24px] py-[10px] font-jakarta text-[14px] font-semibold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1)]
                           transition-transform duration-300 ease-smooth hover:-translate-y-0.5">
                <x-admin.icon name="form-save.svg" class="size-[13.5px] bg-white" />
                Save Changes
            </button>
        </x-slot:actions>

        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="post"
              class="max-w-[760px]">
            @csrf
            @method('PUT')

            <x-admin.form-section title="Contact Details" icon="nav-settings.svg">
                <div class="flex flex-col gap-[24px]">
                    <div class="flex flex-col gap-[8px]">
                        <label for="booking_whatsapp" class="{{ $label }}">WhatsApp Number <span class="text-[#ba1a1a]">*</span></label>
                        <input id="booking_whatsapp" name="booking_whatsapp" inputmode="tel" required
                               value="{{ old('booking_whatsapp', $settings['booking_whatsapp']) }}"
                               placeholder="628213148787"
                               class="{{ $input }}">
                        <p class="{{ $hint }}">
                            International form, country code first and no plus sign. A number starting with
                            <span class="font-semibold">0</span> is converted to <span class="font-semibold">62</span> when you save.
                        </p>
                        @error('booking_whatsapp') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-[8px]">
                        <label for="booking_email" class="{{ $label }}">Booking Email <span class="text-[#ba1a1a]">*</span></label>
                        <input id="booking_email" name="booking_email" type="email" required
                               value="{{ old('booking_email', $settings['booking_email']) }}"
                               placeholder="penidagili@gmail.com"
                               class="{{ $input }}">
                        <p class="{{ $hint }}">Where "Book With Email" sends the guest's message.</p>
                        @error('booking_email') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex flex-col gap-[8px]">
                        <label for="booking_notify_email" class="{{ $label }}">Notification Email</label>
                        <input id="booking_notify_email" name="booking_notify_email" type="email"
                               value="{{ old('booking_notify_email', $settings['booking_notify_email']) }}"
                               placeholder="penidagili@gmail.com"
                               class="{{ $input }}">
                        <p class="{{ $hint }}">Blind-copied on every booking confirmation so the team sees new reservations. Leave blank to send none.</p>
                        @error('booking_notify_email') <span class="font-jakarta text-[13px] text-[#dc2626]">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-admin.form-section>
        </form>
    </x-admin.form-page>
@endsection
