<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrivacyController extends Controller
{
    /**
     * The privacy policy, linked from the footer.
     *
     * The "last updated" date is the policy's own, not today's: a date that
     * moved on every page load would tell readers the policy had changed when
     * it had not. Change it by hand when the wording changes.
     */
    private const UPDATED_AT = '19 Oktober 2026';

    public function show(): View
    {
        return view('pages.privacy', ['updatedAt' => self::UPDATED_AT]);
    }
}
