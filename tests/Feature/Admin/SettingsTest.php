<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The console's Setting screen, which replaced editing .env on the server.
 *
 * What matters is not that a row is written but that the public site follows:
 * a number saved here has to reach the WhatsApp buttons on the next request.
 */
class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_the_screen_shows_the_config_defaults_until_something_is_saved(): void
    {
        config(['penida.booking.whatsapp' => '628000000001']);

        $this->actingAs($this->admin)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('628000000001');
    }

    public function test_saving_a_number_changes_every_whatsapp_button_on_the_public_site(): void
    {
        // Deliberately not the config default, so seeing it proves the override
        // reached the page rather than the default happening to match.
        $default = config('penida.booking.whatsapp');

        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'booking_whatsapp' => '628999000111',
                'booking_email' => 'halo@penidagili.com',
                'booking_notify_email' => 'ops@penidagili.com',
            ])
            ->assertRedirect(route('admin.settings'));

        $this->assertSame('628999000111', Setting::find('booking_whatsapp')->value);

        // A fresh request: the provider has to put the saved value back on top
        // of the config default, or the site keeps showing the old number.
        $this->get('/')
            ->assertOk()
            ->assertSee('wa.me/628999000111')
            ->assertDontSee('wa.me/'.$default);
    }

    public function test_a_local_number_is_stored_in_the_international_form_wa_me_needs(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'booking_whatsapp' => '0821-3148-787',
                'booking_email' => 'halo@penidagili.com',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('628213148787', Setting::find('booking_whatsapp')->value);
    }

    public function test_a_number_that_would_break_the_whatsapp_link_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'booking_whatsapp' => 'not a number',
                'booking_email' => 'halo@penidagili.com',
            ])
            ->assertSessionHasErrors('booking_whatsapp');

        $this->assertNull(Setting::find('booking_whatsapp'));
    }

    public function test_clearing_the_notification_email_falls_back_to_the_config_default(): void
    {
        SiteSettings::put(['booking_notify_email' => 'ops@penidagili.com']);

        $this->actingAs($this->admin)
            ->put(route('admin.settings.update'), [
                'booking_whatsapp' => '628213148787',
                'booking_email' => 'halo@penidagili.com',
                'booking_notify_email' => '',
            ])
            ->assertSessionHasNoErrors();

        $this->assertNull(Setting::find('booking_notify_email'));
    }

    public function test_the_screen_is_closed_to_everyone_but_admins(): void
    {
        $this->get(route('admin.settings'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.settings'))
            ->assertForbidden();
    }
}
