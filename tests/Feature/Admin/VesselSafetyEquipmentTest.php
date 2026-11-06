<?php

namespace Tests\Feature\Admin;

use App\Models\BoatOperator;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** The Advanced Safety Equipment chips on a boat, and where they surface. */
class VesselSafetyEquipmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        BoatOperator::factory()->create();
    }

    public function test_equipment_entered_in_the_console_shows_on_the_boat_page(): void
    {
        $vessel = Vessel::factory()->create([
            'safety_equipment' => ['Life Rafts', 'EPIRB Beacon', 'Fire Suppression System'],
        ]);

        $this->get(route('boats.vessel', $vessel))
            ->assertOk()
            ->assertSee('Advanced Safety Equipment')
            ->assertSee('EPIRB Beacon')
            ->assertSee('Fire Suppression System');
    }

    public function test_a_boat_with_no_equipment_listed_shows_no_empty_heading(): void
    {
        $vessel = Vessel::factory()->create(['safety_equipment' => []]);

        $this->get(route('boats.vessel', $vessel))
            ->assertOk()
            ->assertDontSee('Advanced Safety Equipment');
    }

    public function test_the_chips_are_split_into_a_list_and_can_be_cleared(): void
    {
        $vessel = Vessel::factory()->create(['safety_equipment' => []]);

        $payload = fn (array $extra = []) => array_merge([
            'name' => $vessel->name,
            'type' => $vessel->type,
            'capacity' => $vessel->capacity,
            'status' => 'active',
            'publish' => 'publish',
        ], $extra);

        $this->actingAs($this->admin)
            ->put(route('admin.boats.update', $vessel), $payload([
                // Trailing blanks and stray spaces are what the chip input really posts.
                'safety_equipment' => 'Life Rafts, EPIRB Beacon ,  , Fire Suppression System,',
            ]))
            ->assertSessionHasNoErrors();

        $this->assertSame(
            ['Life Rafts', 'EPIRB Beacon', 'Fire Suppression System'],
            $vessel->fresh()->safety_equipment
        );

        $this->actingAs($this->admin)
            ->put(route('admin.boats.update', $vessel), $payload(['safety_equipment' => '']))
            ->assertSessionHasNoErrors();

        $this->assertSame([], $vessel->fresh()->safety_equipment);
    }

    public function test_the_console_form_offers_the_field(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.boats.create'))
            ->assertOk()
            ->assertSee('Advanced Safety Equipment')
            ->assertSee('+ Add equipment...');
    }
}
