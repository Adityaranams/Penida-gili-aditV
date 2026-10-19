<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Numbers get pasted in every shape a phone shows them — "+62 821-3148-787",
        // "0821 3148 787". Keep the digits, and read a leading 0 as the Indonesian
        // country code, because wa.me only accepts the international form.
        $whatsapp = preg_replace('/\D+/', '', (string) $this->input('booking_whatsapp'));

        if (str_starts_with((string) $whatsapp, '0')) {
            $whatsapp = '62'.substr((string) $whatsapp, 1);
        }

        $this->merge([
            'booking_whatsapp' => $whatsapp,
            'booking_email' => trim((string) $this->input('booking_email')),
            'booking_notify_email' => trim((string) $this->input('booking_notify_email')),
        ]);
    }

    public function rules(): array
    {
        return [
            'booking_whatsapp' => ['required', 'string', 'regex:/^[1-9][0-9]{7,14}$/'],
            'booking_email' => ['required', 'email:rfc', 'max:255'],
            'booking_notify_email' => ['nullable', 'email:rfc', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_whatsapp.regex' => 'Enter the number in international form, for example 628213148787 (no +, no spaces).',
        ];
    }

    public function attributes(): array
    {
        return [
            'booking_whatsapp' => 'WhatsApp number',
            'booking_email' => 'booking email',
            'booking_notify_email' => 'notification email',
        ];
    }
}
