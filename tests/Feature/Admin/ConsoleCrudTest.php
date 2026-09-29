<?php

namespace Tests\Feature\Admin;

use App\Enums\ListingStatus;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Author;
use App\Models\BoatOperator;
use App\Models\Hotel;
use App\Models\Port;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * End-to-end create → read → update → delete for each console module, checking that
 * a record created in the admin shows up where staff and travellers expect it:
 * its own listing, the dashboard, and the public catalogue.
 */
class ConsoleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_boat_created_in_the_console_reaches_the_listing_dashboard_and_public_page(): void
    {
        BoatOperator::factory()->create(['name' => 'Sanjaya Fastboat']);

        $this->actingAs($this->admin)->post(route('admin.boats.store'), [
            'name' => 'Sanjaya Ocean Queen',
            'type' => 'Catamaran Fast Ferry',
            'capacity' => 150,
            'status' => ListingStatus::Active->value,
            'publish' => 'publish',
        ])->assertRedirect(route('admin.boats'));

        $vessel = Vessel::query()->sole();

        // Read: the console listing and the dashboard fleet card both carry it.
        $this->actingAs($this->admin)->get(route('admin.boats'))->assertOk()->assertSee('Sanjaya Ocean Queen');
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sanjaya Ocean Queen')
            ->assertSee('1/1');

        $this->get(route('boats.vessel', $vessel))->assertOk()->assertSee('Sanjaya Ocean Queen');

        // Update: the rename follows through to both screens.
        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), [
            'name' => 'Sanjaya Ocean Queen II',
            'type' => 'Luxury Catamaran',
            'capacity' => 120,
            'status' => ListingStatus::Active->value,
        ])->assertRedirect(route('admin.boats'));

        $this->actingAs($this->admin)->get(route('admin.boats'))->assertSee('Sanjaya Ocean Queen II');
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertSee('Sanjaya Ocean Queen II');

        // Delete: gone from the listing and the dashboard fleet.
        $this->actingAs($this->admin)->delete(route('admin.boats.destroy', $vessel))->assertRedirect(route('admin.boats'));
        $this->assertModelMissing($vessel);
        $this->actingAs($this->admin)->get(route('admin.boats'))->assertDontSee('Luxury Catamaran');
        // The fleet card is gone and the Active Boat KPI falls back to an empty fleet.
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->assertSee('0/0')->assertDontSee('Luxury Catamaran');
    }

    public function test_schedule_created_in_the_console_reaches_the_listing_and_the_dashboard_fleet(): void
    {
        $operator = BoatOperator::factory()->create();
        $vessel = Vessel::factory()->for($operator, 'operator')->create(['name' => 'Sanjaya Explorer', 'status' => ListingStatus::Active]);
        $from = Port::factory()->create(['name' => 'Sanur Beach Port']);
        $to = Port::factory()->create(['name' => 'Banjar Nyuh Port']);

        $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'route' => $from->id.'-'.$to->id,
            'vessel_id' => $vessel->id,
            'departure_time' => '08:00',
            'arrival_time' => '08:45',
            'price_adult' => 180000,
            'price_child' => 135000,
            'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            'publish' => 'publish',
        ])->assertRedirect(route('admin.schedules'));

        $schedule = Schedule::query()->sole();
        $this->assertSame(ListingStatus::Active, $schedule->status);

        $this->actingAs($this->admin)->get(route('admin.schedules'))
            ->assertOk()
            ->assertSee('Sanur Beach Port - Banjar Nyuh Port');

        // The dashboard fleet card shows the route the new schedule gave the boat.
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Sanjaya Explorer')
            ->assertSee('Departure: 08:00 AM');

        $this->actingAs($this->admin)->put(route('admin.schedules.update', $schedule), [
            'route' => $from->id.'-'.$to->id,
            'vessel_id' => $vessel->id,
            'departure_time' => '09:30',
            'arrival_time' => '10:15',
            'price_adult' => 200000,
            'price_child' => 150000,
            'publish' => 'publish',
        ])->assertRedirect(route('admin.schedules'));

        $this->assertSame(200_000, $schedule->fresh()->price_adult);
        $this->actingAs($this->admin)->get(route('admin.schedules'))->assertSee('Rp. 200.000');

        $this->actingAs($this->admin)->delete(route('admin.schedules.destroy', $schedule))->assertRedirect(route('admin.schedules'));
        $this->assertModelMissing($schedule);
    }

    public function test_activity_created_in_the_console_reaches_the_listing_and_the_public_catalogue(): void
    {
        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Kecak Fire Dance',
            'category' => 'Cultural Show',
            'description' => 'Sunset chant on the Uluwatu cliff.',
            'place_label' => 'South Bali',
            'location' => 'Uluwatu Temple, Badung',
            'price_adult' => '180.000',
            'max_daily_capacity' => '50',
            'status' => ListingStatus::Active->value,
        ])->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->sole();

        $this->actingAs($this->admin)->get(route('admin.activities'))->assertOk()->assertSee('Kecak Fire Dance');
        $this->get(route('activities.index'))->assertOk()->assertSee('Kecak Fire Dance');
        $this->get(route('activities.show', $activity))->assertOk()->assertSee('Kecak Fire Dance');

        $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), [
            'name' => 'Kecak Fire Dance at Sunset',
            'category' => 'Cultural Show',
            'description' => 'Sunset chant on the Uluwatu cliff.',
            'price_adult' => '200.000',
            'max_daily_capacity' => '50',
            'status' => ListingStatus::Active->value,
        ])->assertRedirect(route('admin.activities'));

        $this->assertSame(200_000, $activity->fresh()->price_adult);
        $this->get(route('activities.index'))->assertSee('Kecak Fire Dance at Sunset');

        $this->actingAs($this->admin)->delete(route('admin.activities.destroy', $activity))->assertRedirect(route('admin.activities'));
        $this->assertModelMissing($activity);
        $this->get(route('activities.index'))->assertOk()->assertDontSee('Kecak Fire Dance at Sunset');
    }

    public function test_hotel_created_in_the_console_reaches_the_listing_and_the_public_catalogue(): void
    {
        $payload = [
            'name' => 'Cliff Edge Resort',
            'category' => 'Resort',
            'stars' => 5,
            'description' => 'Perched on the cliffs of Nusa Penida.',
            'address' => 'Toya Pakeh, Nusa Penida',
            'publish' => 'publish',
            'rooms' => [
                ['name' => 'Deluxe Ocean Room', 'guests' => 2, 'bed' => '1 King Bed', 'price_per_night' => '2.500.000', 'stock' => 4],
            ],
        ];

        $this->actingAs($this->admin)->post(route('admin.hotels.store'), $payload)->assertRedirect(route('admin.hotels'));

        $hotel = Hotel::query()->sole();
        $this->assertSame(ListingStatus::Active, $hotel->status);

        $this->actingAs($this->admin)->get(route('admin.hotels'))->assertOk()->assertSee('Cliff Edge Resort');
        $this->get(route('hotels.index'))->assertOk()->assertSee('Cliff Edge Resort');
        $this->get(route('hotels.show', $hotel))->assertOk()->assertSee('Deluxe Ocean Room');

        $this->actingAs($this->admin)->put(route('admin.hotels.update', $hotel), ['name' => 'Cliff Edge Resort & Spa'] + $payload)
            ->assertRedirect(route('admin.hotels'));

        $this->get(route('hotels.index'))->assertSee('Cliff Edge Resort &amp; Spa', false);

        $this->actingAs($this->admin)->delete(route('admin.hotels.destroy', $hotel))->assertRedirect(route('admin.hotels'));
        $this->assertModelMissing($hotel);
        $this->get(route('hotels.index'))->assertOk()->assertDontSee('Cliff Edge Resort');
    }

    public function test_article_created_in_the_console_reaches_the_listing_and_the_blog(): void
    {
        $author = Author::factory()->create(['name' => 'Capt. Wayan Sudira', 'role' => 'Master Mariner']);

        $base = [
            'title' => 'Crossing the Badung Strait',
            'excerpt' => 'Everything about the morning crossing.',
            'category' => 'Boat Tips',
            'body' => 'Morning departures give the calmest water.',
            'author_id' => $author->id,
            'status' => 'published',
        ];

        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base)->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();

        $this->actingAs($this->admin)->get(route('admin.articles'))->assertOk()->assertSee('Crossing the Badung Strait');
        $this->get(route('articles.index'))->assertOk()->assertSee('Crossing the Badung Strait');
        $this->get(route('articles.show', $article))->assertOk()->assertSee('Capt. Wayan Sudira');

        $this->actingAs($this->admin)->put(route('admin.articles.update', $article), ['title' => 'Crossing the Badung Strait Calmly'] + $base)
            ->assertRedirect(route('admin.articles'));

        $this->get(route('articles.index'))->assertSee('Crossing the Badung Strait Calmly');

        // Renaming re-slugs the article, so the delete link follows the fresh address.
        $this->actingAs($this->admin)->delete(route('admin.articles.destroy', $article->fresh()))->assertRedirect(route('admin.articles'));
        $this->assertModelMissing($article);
        $this->get(route('articles.index'))->assertOk()->assertDontSee('Crossing the Badung Strait Calmly');
    }
}
