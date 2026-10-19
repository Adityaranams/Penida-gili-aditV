<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The console header's search box. */
class ConsoleSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_it_finds_records_across_modules_and_links_to_their_edit_screens(): void
    {
        $vessel = Vessel::factory()->create(['name' => 'Ocean Dolphin Express']);
        $hotel = Hotel::factory()->create(['name' => 'Ocean View Retreat']);

        $this->actingAs($this->admin)
            ->get(route('admin.search', ['q' => 'Ocean']))
            ->assertOk()
            ->assertSee('Ocean Dolphin Express')
            ->assertSee('Ocean View Retreat')
            ->assertSee(route('admin.boats.edit', $vessel), false)
            ->assertSee(route('admin.hotels.edit', $hotel), false);
    }

    public function test_a_booking_reference_read_off_the_phone_finds_the_reservation(): void
    {
        $booking = Booking::factory()->create(['customer_name' => 'Wayan Sudira']);

        $this->actingAs($this->admin)
            ->get(route('admin.search', ['q' => $booking->reference]))
            ->assertOk()
            ->assertSee('Wayan Sudira');
    }

    public function test_it_says_so_plainly_when_nothing_matches(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.search', ['q' => 'zzzznothing']))
            ->assertOk()
            ->assertSee('Nothing matched');
    }

    public function test_a_wildcard_is_searched_for_rather_than_matching_everything(): void
    {
        Vessel::factory()->create(['name' => 'Sea Breeze']);

        $this->actingAs($this->admin)
            ->get(route('admin.search', ['q' => '%']))
            ->assertOk()
            ->assertDontSee('Sea Breeze');
    }

    public function test_the_box_is_hidden_on_the_setting_screen_but_present_elsewhere(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertSee('console-search');
        $this->actingAs($this->admin)->get(route('admin.settings'))->assertDontSee('console-search');
    }

    public function test_search_is_closed_to_everyone_but_admins(): void
    {
        $this->get(route('admin.search'))->assertRedirect(route('admin.login'));
    }
}
