<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /** Keeps this form's errors apart, so the modal knows which tab to reopen. */
    protected $errorBag = 'password';

    public function rules(): array
    {
        return [
            // current_password checks against the signed-in user, so a stolen
            // session cannot quietly change the password it is riding on.
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'current password',
            'password' => 'new password',
        ];
    }
}
