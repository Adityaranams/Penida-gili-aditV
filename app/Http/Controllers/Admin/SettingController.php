<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * The console's Setting screen: the contact details the public site shows.
 *
 * These used to live in .env, which meant a shell on the server to change a
 * phone number. Saving here writes a row that overrides the config default
 * for every page at once — see App\Support\SiteSettings.
 */
class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.settings', ['settings' => SiteSettings::current()]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        SiteSettings::put($request->validated());

        return redirect()->route('admin.settings')->with('flash', 'Contact details updated across the site.');
    }
}
