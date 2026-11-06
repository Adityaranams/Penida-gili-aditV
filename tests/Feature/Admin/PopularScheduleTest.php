<?php

namespace Tests\Feature\Admin;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The Popular switch on a schedule, and the badge it puts on the boat page.
 *
 * The badge used to be drawn on whichever row happened to be last, so the
 * point of these tests is that it now follows the data and nothing else.
 */
class PopularScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_a_sailing_shows_no_badge_until_staff_mark_it_popular(): void
    {
        $schedule = Schedule::factory()->for(Vessel::factory())->create(['is_popular' => false]);

        $this->get(route('boats.vessel', $schedule->vessel))
            ->assertOk()
            ->assertDontSee('Popular');
    }

    public function test_marking_a_sailing_popular_puts_the_badge_on_that_row(): void
    {
        $schedule = Schedule::factory()->for(Vessel::factory())->create(['is_popular' => true]);

        $this->get(route('boats.vessel', $schedule->vessel))
            ->assertOk()
            ->assertSee('Popular')
            ->assertSee('popular-thumb.svg', false);
    }

    public function test_only_the_marked_sailing_of_a_boat_carries_the_badge(): void
    {
        $vessel = Vessel::factory()->create();
        Schedule::factory()->count(3)->create(['vessel_id' => $vessel->id, 'is_popular' => false]);
        Schedule::factory()->create(['vessel_id' => $vessel->id, 'is_popular' => true]);

        $html = $this->get(route('boats.vessel', $vessel))->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'popular-thumb.svg'));
    }

    public function test_the_switch_saves_and_can_be_turned_back_off(): void
    {
        $schedule = Schedule::factory()->for(Vessel::factory())->create(['is_popular' => false]);

        $payload = fn (array $extra = []) => array_merge([
            'route' => $schedule->from_port_id.'-'.$schedule->to_port_id,
            'vessel_id' => $schedule->vessel_id,
            'departure_time' => '08:00',
            'arrival_time' => '09:00',
            'price_adult' => '200000',
            'price_child' => '150000',
            'days' => ['Mon'],
            'publish' => 'publish',
        ], $extra);

        $this->actingAs($this->admin)
            ->put(route('admin.schedules.update', $schedule), $payload(['is_popular' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertTrue($schedule->fresh()->is_popular);

        // An unticked switch posts nothing at all, so this is the case that
        // would silently keep the badge on if the flag were not normalised.
        $this->actingAs($this->admin)
            ->put(route('admin.schedules.update', $schedule), $payload())
            ->assertSessionHasNoErrors();

        $this->assertFalse($schedule->fresh()->is_popular);
    }
}
