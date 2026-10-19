<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The console header: the notification bell and the account menu beside it.
 */
class HeaderMenusTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_the_account_menu_offers_profile_change_password_and_logout(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Change Password')
            ->assertSee(route('admin.profile'))
            ->assertSee(route('admin.logout'));
    }

    public function test_the_bell_counts_only_bookings_this_admin_has_not_seen(): void
    {
        Booking::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->getJson(route('admin.notifications'))
            ->assertOk()
            ->assertJsonPath('count', 3)
            ->assertJsonCount(3, 'items');

        $this->actingAs($this->admin)->postJson(route('admin.notifications.seen'))->assertOk();

        // Seen clears the count but keeps the list — the bell still shows history.
        $this->actingAs($this->admin->fresh())
            ->getJson(route('admin.notifications'))
            ->assertJsonPath('count', 0)
            ->assertJsonCount(3, 'items');

        // Both timestamps are second-precision, so a booking made in the same
        // second as the click would read as "already seen"; step past it.
        $this->travel(1)->second();
        Booking::factory()->create();

        $this->actingAs($this->admin->fresh())
            ->getJson(route('admin.notifications'))
            ->assertJsonPath('count', 1);
    }

    public function test_one_admin_clearing_the_bell_leaves_another_admins_count_alone(): void
    {
        Booking::factory()->count(2)->create();
        $other = User::factory()->create(['is_admin' => true]);

        $this->actingAs($this->admin)->postJson(route('admin.notifications.seen'))->assertOk();

        $this->actingAs($other)
            ->getJson(route('admin.notifications'))
            ->assertJsonPath('count', 2);
    }

    public function test_the_feed_is_closed_to_everyone_but_admins(): void
    {
        $this->getJson(route('admin.notifications'))->assertUnauthorized();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->getJson(route('admin.notifications'))
            ->assertForbidden();
    }

    public function test_an_admin_can_change_their_own_name_and_email(): void
    {
        $this->actingAs($this->admin)
            ->from(route('admin.dashboard'))
            ->put(route('admin.profile.update'), ['name' => 'Putu Ops', 'email' => 'putu@penidagili.com'])
            // Saved from the header dialog, so it returns to the page in hand.
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame('Putu Ops', $this->admin->fresh()->name);
    }

    public function test_changing_the_password_needs_the_current_one(): void
    {
        $this->admin->update(['password' => 'correct-horse']);

        $this->actingAs($this->admin)
            ->put(route('admin.profile.password'), [
                'current_password' => 'wrong-one',
                'password' => 'new-password-99',
                'password_confirmation' => 'new-password-99',
            ])
            ->assertSessionHasErrors(['current_password'], null, 'password');

        $this->assertTrue(Hash::check('correct-horse', $this->admin->fresh()->password));

        $this->actingAs($this->admin)
            ->put(route('admin.profile.password'), [
                'current_password' => 'correct-horse',
                'password' => 'new-password-99',
                'password_confirmation' => 'new-password-99',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('new-password-99', $this->admin->fresh()->password));
    }
}
