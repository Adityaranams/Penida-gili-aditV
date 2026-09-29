<?php

namespace App\Http\Requests;

use App\Support\BookingOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Traveller details + party size shared by the boat, hotel and activity order forms.
 * Product-specific fields (schedule/room/date) are validated by the subclass.
 */
class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        $maxParty = (int) config('penida.booking.max_party', 20);

        return [
            // Letters (any alphabet), spaces, apostrophes and hyphens; at least three letters.
            'full_name' => ['required', 'string', 'max:120', 'regex:/^[\p{L}\s\'\-.]+$/u', 'regex:/(?:\p{L}.*){3}/u'],
            // The order form no longer collects an email; kept optional for the admin console and API callers.
            'email' => ['nullable', 'email:rfc', 'max:190'],
            'nationality' => ['required', Rule::in(BookingOptions::nationalities())],
            'dial_code' => ['nullable', Rule::in(BookingOptions::dialCodes())],
            // Local number without the dialling code: 6–15 digits, spaces/dashes allowed.
            'phone' => ['required', 'string', 'regex:/^(?=(?:\D*\d){6,15}\D*$)[0-9 \-()]+$/'],
            'notes' => ['nullable', 'string', 'max:1000', 'regex:/^[\p{L}\p{N}\s.,!?\'"()\-\/:;&+%]*$/u'],
            'adults' => ['required', 'integer', 'min:1', "max:{$maxParty}"],
            'children' => ['nullable', 'integer', 'min:0', "max:{$maxParty}"],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.regex' => 'Full name must contain at least 3 letters and use letters only.',
            'email.email' => 'Enter a valid email address, e.g. name@example.com.',
            'nationality.in' => 'Choose a nationality from the list.',
            'phone.regex' => 'Enter a valid phone number (6–15 digits) without the country code.',
            'notes.regex' => 'Order notes may only contain letters, numbers and basic punctuation.',
            'travel_date.after_or_equal' => 'Travel date cannot be in the past.',
        ];
    }

    /** @return array{full_name: string, email: string|null, dial_code: string, phone: string, nationality: string, notes: string|null} */
    public function traveller(): array
    {
        $traveller = $this->safe()->only(['full_name', 'email', 'dial_code', 'phone', 'nationality', 'notes'])
            + ['dial_code' => '+62', 'notes' => null, 'email' => null];
        $traveller['full_name'] = trim(preg_replace('/\s+/u', ' ', $traveller['full_name']));
        $traveller['phone'] = preg_replace('/\D+/', '', $traveller['phone']);

        return $traveller;
    }
}
