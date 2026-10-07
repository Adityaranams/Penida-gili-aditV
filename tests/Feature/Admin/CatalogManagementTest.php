<?php

namespace Tests\Feature\Admin;

use App\Enums\ArticleStatus;
use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Author;
use App\Models\BoatOperator;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Port;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    public function test_boat_form_shows_publishing_settings_and_draft_overrides_operational_status(): void
    {
        $operator = BoatOperator::factory()->create();

        $this->actingAs($this->admin)->get(route('admin.boats.create'))
            ->assertOk()
            ->assertSeeInOrder(['Publishing Settings', 'Publish Immediately', 'Save as Draft', 'Boat Details', 'Initial Status', 'Cover Photo', 'Boat Gallery', 'Boat Facilities'])
            ->assertDontSee('Top Speed')
            ->assertDontSee('Vessel Code')
            ->assertDontSee('Last Inspection Date')
            ->assertDontSee('name="boat_operator_id"', false);

        // No operator field on the form: the vessel is attached to the operator on file.
        $base = ['name' => 'Sanjaya Explorer', 'type' => 'Luxury Catamaran', 'capacity' => 120];

        $this->actingAs($this->admin)->post(route('admin.boats.store'), $base + ['publish' => 'draft', 'status' => ListingStatus::Active->value])
            ->assertRedirect(route('admin.boats'));
        $this->assertSame(ListingStatus::Draft, Vessel::query()->sole()->status);
        $this->assertSame($operator->id, Vessel::query()->sole()->boat_operator_id);

        Vessel::query()->delete();

        $this->actingAs($this->admin)->post(route('admin.boats.store'), $base + ['publish' => 'publish', 'status' => ListingStatus::Inactive->value])
            ->assertRedirect(route('admin.boats'));
        $this->assertSame(ListingStatus::Inactive, Vessel::query()->sole()->status);

        $this->actingAs($this->admin)->get(route('admin.boats.edit', Vessel::query()->sole()))
            ->assertOk()
            ->assertSee('Non-Active (Maintenance)');
    }

    public function test_admin_can_create_edit_and_delete_a_vessel(): void
    {
        Storage::fake('public');
        $operator = BoatOperator::factory()->create();

        $this->actingAs($this->admin)->get(route('admin.boats.create'))->assertOk();

        $this->actingAs($this->admin)->post(route('admin.boats.store'), [
            'boat_operator_id' => $operator->id,
            'name' => 'Sanjaya Ocean Queen',
            'type' => 'Catamaran Fast Ferry',
            'capacity' => 150,
            'status' => ListingStatus::Active->value,
            'facilities' => ['Toilet', 'Life Jackets'],
            'cover' => $this->fakeImage('queen.jpg'),
        ])->assertRedirect(route('admin.boats'));

        $vessel = Vessel::query()->sole();
        $this->assertSame('SFB-001', $vessel->code);
        $this->assertSame(['Toilet', 'Life Jackets'], $vessel->facilities);
        Storage::disk('public')->assertExists($vessel->image);
        $this->actingAs($this->admin)->get(route('admin.boats.edit', $vessel))->assertOk()->assertSee('Sanjaya Ocean Queen');

        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), [
            'boat_operator_id' => $operator->id,
            'name' => 'Sanjaya Ocean Queen II',
            'type' => 'Luxury Catamaran',
            'capacity' => 120,
            'status' => ListingStatus::Inactive->value,
        ])->assertRedirect(route('admin.boats'));

        $this->assertSame(ListingStatus::Inactive, $vessel->fresh()->status);
        $this->assertSame('Sanjaya Ocean Queen II', $vessel->fresh()->name);

        $this->actingAs($this->admin)->delete(route('admin.boats.destroy', $vessel))->assertRedirect(route('admin.boats'));
        $this->assertModelMissing($vessel);
    }

    public function test_editing_a_vessel_adds_to_the_gallery_and_only_drops_ticked_photos(): void
    {
        Storage::fake('public');
        $vessel = Vessel::factory()->create(['gallery' => null]);

        $base = [
            'name' => $vessel->name,
            'type' => $vessel->type,
            'capacity' => $vessel->capacity,
            'status' => ListingStatus::Active->value,
        ];

        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'photos' => [$this->fakeImage('deck.jpg')],
        ])->assertRedirect(route('admin.boats'));

        $this->assertCount(1, $vessel->fresh()->gallery);

        // A second upload is added to the first, not swapped in for it.
        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'photos' => [$this->fakeImage('cabin.jpg')],
        ])->assertRedirect(route('admin.boats'));

        $gallery = $vessel->fresh()->gallery;
        $this->assertCount(2, $gallery);

        // Saving without uploads keeps everything; ticking a photo removes just that one.
        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base)->assertRedirect(route('admin.boats'));
        $this->assertCount(2, $vessel->fresh()->gallery);

        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'remove_photos' => [$gallery[0]['image']],
        ])->assertRedirect(route('admin.boats'));

        $this->assertSame([$gallery[1]['image']], array_column($vessel->fresh()->gallery, 'image'));
    }

    public function test_admin_can_add_edit_and_remove_guest_testimonials_on_a_boat(): void
    {
        $vessel = Vessel::factory()->create();

        $base = [
            'name' => $vessel->name,
            'type' => $vessel->type,
            'capacity' => $vessel->capacity,
            'status' => ListingStatus::Active->value,
        ];

        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'testimonials' => [
                ['name' => 'Sarah Jenkins', 'stars' => 5, 'quote' => 'Incredibly smooth ride.', 'experienced_at' => '2023-10'],
                ['name' => '', 'stars' => 5, 'quote' => ''],
            ],
        ])->assertRedirect(route('admin.boats'));

        $review = $vessel->reviews()->sole();
        $this->assertSame('Sarah Jenkins', $review->name);
        $this->assertSame('Traveled Oct 2023', $review->traveled);

        $this->actingAs($this->admin)->get(route('boats.vessel', $vessel))->assertOk()->assertSee('Incredibly smooth ride.');

        // Editing the row keeps the same review; ticking Remove deletes it.
        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'testimonials' => [['id' => $review->id, 'name' => 'Sarah J.', 'stars' => 4, 'quote' => 'Great crew.']],
        ])->assertRedirect(route('admin.boats'));

        $this->assertSame('Sarah J.', $vessel->reviews()->sole()->name);

        $this->actingAs($this->admin)->put(route('admin.boats.update', $vessel), $base + [
            'testimonials' => [['id' => $review->id, 'name' => 'Sarah J.', 'quote' => 'Great crew.', 'remove' => '1']],
        ])->assertRedirect(route('admin.boats'));

        $this->assertCount(0, $vessel->reviews()->get());
    }

    public function test_listings_can_delete_several_rows_at_once(): void
    {
        $keep = Vessel::factory()->create(['name' => 'Keeper']);
        $drop = Vessel::factory()->count(2)->create();

        $this->actingAs($this->admin)->get(route('admin.boats'))
            ->assertOk()
            ->assertSee('name="ids[]"', false)
            ->assertSee('form="bulk-delete"', false);

        $this->actingAs($this->admin)
            ->from(route('admin.boats'))
            ->delete(route('admin.boats.bulk-destroy'), ['ids' => $drop->pluck('id')->all()])
            ->assertRedirect(route('admin.boats'));

        $this->assertSame(['Keeper'], Vessel::query()->pluck('name')->all());

        // Nothing ticked deletes nothing.
        $this->actingAs($this->admin)->from(route('admin.boats'))->delete(route('admin.boats.bulk-destroy'), [])->assertRedirect();
        $this->assertCount(1, Vessel::query()->get());

        $schedules = Schedule::factory()->count(2)->create();
        $this->actingAs($this->admin)
            ->from(route('admin.schedules'))
            ->delete(route('admin.schedules.bulk-destroy'), ['ids' => $schedules->pluck('id')->all()])
            ->assertRedirect(route('admin.schedules'));
        $this->assertCount(0, Schedule::query()->get());

        $activities = Activity::factory()->count(2)->create();
        $this->actingAs($this->admin)
            ->from(route('admin.activities'))
            ->delete(route('admin.activities.bulk-destroy'), ['ids' => [$activities->first()->id]])
            ->assertRedirect(route('admin.activities'));
        $this->assertCount(1, Activity::query()->get());

        $articles = Article::factory()->count(3)->create();
        $this->actingAs($this->admin)->get(route('admin.articles'))->assertOk()->assertSee('form="bulk-delete"', false);
        $this->actingAs($this->admin)
            ->from(route('admin.articles'))
            ->delete(route('admin.articles.bulk-destroy'), ['ids' => $articles->take(2)->pluck('id')->all()])
            ->assertRedirect(route('admin.articles'));
        $this->assertCount(1, Article::query()->get());
    }

    public function test_vessel_listing_filters_by_status_and_search(): void
    {
        Vessel::factory()->create(['name' => 'Alpha Express', 'status' => ListingStatus::Active]);
        Vessel::factory()->create(['name' => 'Beta Voyager', 'status' => ListingStatus::Inactive]);

        $this->actingAs($this->admin)->get(route('admin.boats', ['status' => 'inactive']))
            ->assertOk()->assertSee('Beta Voyager')->assertDontSee('Alpha Express');

        $this->actingAs($this->admin)->get(route('admin.boats', ['q' => 'alpha']))
            ->assertOk()->assertSee('Alpha Express')->assertDontSee('Beta Voyager');
    }

    public function test_schedule_listing_filters_by_route_boat_and_date(): void
    {
        $sanur = Port::factory()->create(['name' => 'Sanur Beach Port', 'area' => 'Bali']);
        $penida = Port::factory()->create(['name' => 'Banjar Nyuh Nusa Penida', 'area' => 'Nusa Penida']);
        $gili = Port::factory()->create(['name' => 'Gili Trawangan', 'area' => 'Lombok']);
        $queen = Vessel::factory()->create(['name' => 'Sanjaya Ocean Queen']);
        $explorer = Vessel::factory()->create(['name' => 'Sanjaya Explorer']);

        $daily = Schedule::factory()->create(['from_port_id' => $sanur->id, 'to_port_id' => $penida->id, 'vessel_id' => $queen->id, 'days' => null, 'departure_time' => '08:00']);
        $weekend = Schedule::factory()->create(['from_port_id' => $sanur->id, 'to_port_id' => $gili->id, 'vessel_id' => $explorer->id, 'days' => ['Sat', 'Sun'], 'departure_time' => '10:00']);

        $this->actingAs($this->admin)->get(route('admin.schedules'))
            ->assertOk()
            ->assertSeeInOrder(['Search Route', 'Boat', 'Date', 'Filter'])
            ->assertDontSee('All Statuses');

        $this->actingAs($this->admin)->get(route('admin.schedules', ['q' => 'Sanur to Nusa Penida']))
            ->assertSee('Banjar Nyuh Nusa Penida')->assertDontSee('Gili Trawangan');

        $this->actingAs($this->admin)->get(route('admin.schedules', ['q' => 'Gili']))
            ->assertSee('Gili Trawangan')->assertDontSee('Banjar Nyuh Nusa Penida');

        $this->actingAs($this->admin)->get(route('admin.schedules', ['vessel' => $explorer->id]))
            ->assertSee('Gili Trawangan')->assertDontSee('Banjar Nyuh Nusa Penida');

        // 2030-05-08 is a Wednesday: only the daily run operates; the weekend run appears on the Saturday.
        $this->actingAs($this->admin)->get(route('admin.schedules', ['date' => '2030-05-08']))
            ->assertSee('Banjar Nyuh Nusa Penida')->assertDontSee('Gili Trawangan');
        $this->actingAs($this->admin)->get(route('admin.schedules', ['date' => '2030-05-11']))
            ->assertSee('Gili Trawangan');

        // The row spells out the date it sails: the filtered day, or the next one when no filter is set.
        $this->actingAs($this->admin)->get(route('admin.schedules', ['date' => '2030-05-11']))
            ->assertSee('Sat, 11 May 2030')
            ->assertSee('Sat, Sun');

        $this->actingAs($this->admin)->get(route('admin.schedules', ['q' => 'Gili']))
            ->assertSee($weekend->nextDate()->format('D, d M Y'));

        $this->actingAs($this->admin)->get(route('admin.schedules', ['q' => 'Nusa Penida']))
            ->assertSee(now()->format('D, d M Y'))
            ->assertSee('Daily');

        $this->assertSame('Daily', $daily->days_label);
    }

    public function test_schedule_form_matches_figma_and_derives_ports_operator_and_status(): void
    {
        $operator = BoatOperator::factory()->create();
        $vessel = Vessel::factory()->for($operator, 'operator')->create(['name' => 'Sanjaya Explorer I', 'capacity' => 60]);
        [$from, $to] = Port::factory()->count(2)->create();

        $this->actingAs($this->admin)->get(route('admin.schedules.create'))
            ->assertOk()
            ->assertSeeInOrder([
                'Schedule Details', 'Route Segment', 'Select an established route...', 'Assigned Vessel', 'Departure Time', 'Est. Arrival Time',
                'Operating Days (Frequency)', 'Pricing Configuration', 'Base Price (Local Pax)', 'Base Price (Foreign Pax)', 'Child Price',
                'Publishing Settings', 'Publish Immediately', 'Save as Draft', 'High Season Alert', 'View Analytics',
                'Schedule Summary', 'Total Capacity', 'Est. Duration', 'Publish Schedule', 'Cancel',
            ])
            ->assertDontSee('Departure Port')
            ->assertDontSee('name="boat_operator_id"', false);

        $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'route' => $from->id.'-'.$to->id,
            'vessel_id' => $vessel->id,
            'departure_time' => '08:00',
            'arrival_time' => '08:45',
            'price_adult' => 100000,
            'price_foreign' => 150000,
            'price_child' => 75000,
            'days' => ['Mon', 'Wed'],
            'publish' => 'draft',
        ])->assertRedirect(route('admin.schedules'));

        $schedule = Schedule::query()->sole();
        $this->assertSame($from->id, $schedule->from_port_id);
        $this->assertSame($to->id, $schedule->to_port_id);
        $this->assertSame($operator->id, $schedule->boat_operator_id);
        $this->assertSame(ListingStatus::Draft, $schedule->status);
        $this->assertSame(['Mon', 'Wed'], $schedule->days);

        $this->actingAs($this->admin)->get(route('admin.schedules.edit', $schedule))
            ->assertOk()
            ->assertSee('60 pax')
            ->assertSee('45 mins');
    }

    public function test_schedule_requires_distinct_ports_and_ordered_times(): void
    {
        $operator = BoatOperator::factory()->create();
        $port = Port::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'boat_operator_id' => $operator->id,
            'from_port_id' => $port->id,
            'to_port_id' => $port->id,
            'departure_time' => '09:00',
            'arrival_time' => '08:00',
            'price_adult' => 100000,
            'price_child' => 75000,
            'status' => 'active',
        ])->assertSessionHasErrors(['to_port_id', 'arrival_time']);

        $this->assertDatabaseCount('schedules', 0);
    }

    public function test_schedule_with_every_day_selected_is_stored_as_daily(): void
    {
        $operator = BoatOperator::factory()->create();
        $vessel = Vessel::factory()->for($operator, 'operator')->create();
        [$from, $to] = Port::factory()->count(2)->create();

        $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
            'boat_operator_id' => $operator->id,
            'vessel_id' => $vessel->id,
            'from_port_id' => $from->id,
            'to_port_id' => $to->id,
            'departure_time' => '08:00',
            'arrival_time' => '08:45',
            'price_adult' => 100000,
            'price_child' => 75000,
            'days' => Schedule::DAYS,
            'status' => 'active',
        ])->assertRedirect(route('admin.schedules'));

        $this->assertNull(Schedule::query()->sole()->days);
    }

    public function test_admin_can_create_an_activity_with_formatted_prices(): void
    {
        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Kecak Fire Dance',
            'category' => 'Cultural Show',
            'description' => 'Sunset chant on the Uluwatu cliff.',
            'place_label' => 'Uluwatu, Badung',
            'location' => 'Bali',
            'opens_at' => '17:45',
            'closes_at' => '19:00',
            'price_adult' => '180.000',
            'price_was' => 'Rp 250.000',
            'included' => "Entrance ticket\nSeat reservation",
            'max_daily_capacity' => '50',
            'status' => 'active',
        ])->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->sole();
        $this->assertSame(180_000, $activity->price_adult);
        $this->assertSame(250_000, $activity->price_was);
        $this->assertSame(['Entrance ticket', 'Seat reservation'], $activity->included);
        $this->assertSame('kecak-fire-dance', $activity->slug);
    }

    public function test_activity_added_in_the_console_renders_without_seeded_content(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Sunrise Paddle',
            'category' => 'Water Sports',
            'description' => 'Paddle out at first light.',
            'place_label' => 'Sanur',
            'rating' => '4.6',
            'price_adult' => '120.000',
            'max_daily_capacity' => '20',
            'cancellation_policy' => 'free_24h',
            'instant_confirmation' => 'on',
            'status' => ListingStatus::Active->value,
            'cover' => $this->fakeImage('paddle.jpg'),
        ])->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->where('name', 'Sunrise Paddle')->sole();
        $this->assertSame('4.6', (string) $activity->rating);
        Storage::disk('public')->assertExists($activity->image);
        $this->assertNull($activity->highlights);

        // The detail page used to fatal on the seeded-only columns.
        $this->get(route('activities.show', $activity))
            ->assertOk()
            ->assertSee('Sunrise Paddle')
            ->assertSee('Free cancellation')
            ->assertSee('Instant confirmation');

        $this->get(route('activities.index'))->assertOk()->assertSee('Sunrise Paddle');

        // A second upload is added to the gallery rather than swapped in for the first.
        $base = ['name' => $activity->name, 'category' => $activity->category, 'description' => $activity->description,
            'price_adult' => '120.000', 'max_daily_capacity' => '20', 'status' => ListingStatus::Active->value];

        $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), $base + ['gallery' => [$this->fakeImage('a.jpg')]]);
        $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), $base + ['gallery' => [$this->fakeImage('b.jpg')]]);

        $this->assertCount(2, $activity->fresh()->gallery);
    }

    public function test_activity_description_keeps_basic_formatting_and_experiences_are_editable(): void
    {
        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Cliff Walk',
            'category' => 'Wildlife & Nature',
            'description' => '<p>Walk the <strong>cliff path</strong>.</p><ul><li>Sunset view</li></ul><script>alert(1)</script>',
            'price_adult' => '90.000',
            'max_daily_capacity' => '15',
            'status' => ListingStatus::Active->value,
            'experiences' => [
                ['title' => 'Sunset over Kelingking', 'body' => 'The path opens onto the headland just before dusk.'],
                ['title' => '', 'body' => ''],
            ],
        ])->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->where('name', 'Cliff Walk')->sole();

        // Bold and lists survive; anything else is stripped before it is stored.
        $this->assertStringContainsString('<strong>cliff path</strong>', $activity->description);
        $this->assertStringContainsString('<li>Sunset view</li>', $activity->description);
        $this->assertStringNotContainsString('<script>', $activity->description);
        $this->assertSame('Walk the cliff path. Sunset view', $activity->plain_description);

        // Blank rows are dropped; the rest become the "Experiences Awaiting You" entries.
        $this->assertCount(1, $activity->experiences);
        $this->assertSame('Sunset over Kelingking', $activity->experiences[0]['title']);

        $this->get(route('activities.show', $activity))
            ->assertOk()
            ->assertSee('<strong>cliff path</strong>', false)
            ->assertSee('Sunset over Kelingking');

        // The card shows the copy without markup.
        $this->get(route('activities.index'))->assertOk()->assertDontSee('<strong>cliff path</strong>', false);

        // A fresh form leaves the operating days for the admin to pick.
        $this->actingAs($this->admin)->get(route('admin.activities.create'))
            ->assertOk()
            ->assertSee('Add Experience')
            ->assertDontSee('value="Mon" checked', false)
            ->assertDontSee('value="Sun" checked', false);
    }

    public function test_activity_editor_follows_figma_and_stores_the_new_fields(): void
    {
        $this->actingAs($this->admin)->get(route('admin.activities.create'))
            ->assertOk()
            ->assertSeeInOrder([
                'Save Draft', 'Publish Activity',
                'Basic Information', 'Short Catchy Tagline / Badge', 'Full Description',
                'Schedule & Operational Hours', 'Select All Days', 'Instant Confirmation', 'Cancellation Policy',
                'Inclusions &amp; Important Info', '+ Add inclusion...', '+ Add exclusion...', 'Important Info',
                'Cover Photo', 'Media & Gallery Upload', '/ 8 Photos',
                'Publishing Status', 'Public Visibility',
                'Pricing & Quota Capacity', 'Original / Strikethrough Price', 'Max Daily Capacity / Quota', 'pax / day',
            ])
            ->assertDontSee('Location Details')
            // Scheduled publishing and dual-tier pricing were dropped from the editor.
            ->assertDontSee('Go live at specific timestamp')
            ->assertDontSee('Domestic vs Foreign Price');

        // Chips arrive comma separated; the header "Save Draft" button overrides the radio.
        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Manta Point Snorkel',
            'category' => 'Water Sports',
            'description' => "Swim with mantas.\nBoat departs at dawn.",
            'price_adult' => '350.000',
            'max_daily_capacity' => '24',
            'included' => 'Snorkel gear, Lunch box',
            'excluded' => 'Hotel transfer',
            'instant_confirmation' => 'on',
            'cancellation_policy' => 'free_48h',
            'important_notes' => 'Bring reef-safe sunscreen.',
            'status' => ListingStatus::Draft->value,
            'is_public' => 'on',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->sole();
        $this->assertSame(ListingStatus::Draft, $activity->status);
        $this->assertNull($activity->publish_at);
        $this->assertSame(['Snorkel gear', 'Lunch box'], $activity->included);
        $this->assertSame(['Hotel transfer'], $activity->excluded);
        $this->assertFalse($activity->dual_pricing);
        $this->assertSame(24, $activity->max_daily_capacity);
        $this->assertTrue($activity->is_public);
        $this->assertSame('free_48h', $activity->cancellation_policy);
        $this->assertSame('Swim with mantas.', $activity->intro);

        // Publish Activity wins over the radios.
        $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), [
            'name' => 'Manta Point Snorkel',
            'category' => 'Water Sports',
            'description' => 'Swim with mantas.',
            'price_adult' => '350.000',
            'max_daily_capacity' => '24',
            'status' => 'draft',
            'submit_as' => 'publish',
        ])->assertSessionHasNoErrors();

        $activity->refresh();
        $this->assertSame(ListingStatus::Active, $activity->status);
        $this->assertNull($activity->publish_at);
        $this->assertFalse($activity->dual_pricing);
        $this->assertNull($activity->price_foreign);

        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'No quota', 'category' => 'Adventure', 'description' => 'x', 'price_adult' => '1', 'status' => 'active',
        ])->assertSessionHasErrors('max_daily_capacity');
    }

    public function test_activity_toolbar_filters_by_search_category_status_and_sort(): void
    {
        $kecak = Activity::factory()->create(['name' => 'Kecak Dance', 'category' => 'Cultural Show', 'location' => 'Uluwatu', 'status' => ListingStatus::Active, 'rating' => 4.9]);
        $manta = Activity::factory()->create(['name' => 'Manta Snorkel', 'category' => 'Water Sports', 'location' => 'Manta Bay', 'status' => ListingStatus::Draft, 'rating' => 4.1]);

        // "Most Booked" counts confirmed passengers, so give Manta the bigger party.
        Booking::factory()->confirmed()->for($kecak, 'bookable')->create(['adults' => 1, 'children' => 1]);
        Booking::factory()->confirmed()->for($manta, 'bookable')->create(['adults' => 4, 'children' => 2]);

        $this->actingAs($this->admin)->get(route('admin.activities'))
            ->assertOk()
            ->assertSee('Search by title, location or vendor...')
            ->assertSeeInOrder(['All Categories', 'Status: All', 'Most Booked', 'Reset filters'])
            ->assertSeeInOrder(['Manta Snorkel', 'Kecak Dance']);

        $this->actingAs($this->admin)->get(route('admin.activities', ['q' => 'manta bay']))
            ->assertSee('Manta Snorkel')->assertDontSee('Kecak Dance');

        $this->actingAs($this->admin)->get(route('admin.activities', ['category' => 'Cultural Show']))
            ->assertSee('Kecak Dance')->assertDontSee('Manta Snorkel');

        $this->actingAs($this->admin)->get(route('admin.activities', ['status' => 'draft']))
            ->assertSee('Manta Snorkel')->assertDontSee('Kecak Dance');

        $this->actingAs($this->admin)->get(route('admin.activities', ['sort' => 'top_rated']))
            ->assertSeeInOrder(['Kecak Dance', 'Manta Snorkel']);
    }

    public function test_activity_listing_shows_figma_columns_with_edit_and_delete_actions(): void
    {
        $activity = Activity::factory()->create([
            'name' => 'Kecak Fire Dance', 'category' => 'Cultural Show', 'location' => 'Uluwatu, Badung',
            'price_adult' => 180_000, 'price_was' => 250_000, 'place_label' => 'Uluwatu', 'status' => ListingStatus::Active,
        ]);

        // Total Sold adds up the passengers on confirmed bookings; pending ones do not count.
        Booking::factory()->confirmed()->for($activity, 'bookable')->create(['adults' => 3, 'children' => 2]);
        Booking::factory()->for($activity, 'bookable')->create(['adults' => 9, 'children' => 0, 'status' => BookingStatus::Pending]);

        $this->actingAs($this->admin)->get(route('admin.activities'))
            ->assertOk()
            ->assertSeeInOrder(['Activity Details', 'Category', 'Location', 'Price / Pax', 'Status', 'Total Sold', 'Actions'])
            ->assertSee('cat-cultural-show.svg')
            ->assertSee('IDR 180.000')
            ->assertSee('Rp. 250.000')
            ->assertSee('Uluwatu')
            ->assertSee('>5<', false)
            // Only edit and delete remain in the Actions column.
            ->assertSee(route('admin.activities.edit', $activity))
            ->assertSee(route('admin.activities.destroy', $activity))
            ->assertDontSee('action-view.svg')
            ->assertDontSee('action-duplicate.svg');
    }

    public function test_admin_can_create_a_hotel_with_rooms_and_sync_them_on_update(): void
    {
        $payload = [
            'name' => 'Cliff Edge Resort',
            'category' => 'Resort',
            'stars' => 5,
            'description' => 'Perched on the cliffs.',
            'address' => 'Nusa Penida, Bali',
            'status' => 'active',
            'amenities' => ['Oceanfront Infinity Pool'],
            'rooms' => [
                ['name' => 'Deluxe', 'guests' => 2, 'bed' => '1 King Bed', 'price_per_night' => '2.500.000', 'stock' => 4],
                ['name' => 'Villa', 'guests' => 2, 'bed' => '1 King Bed', 'price_per_night' => '5.800.000', 'stock' => 2],
                ['name' => '', 'guests' => 2, 'price_per_night' => '', 'stock' => 1],
            ],
        ];

        $this->actingAs($this->admin)->post(route('admin.hotels.store'), $payload)->assertRedirect(route('admin.hotels'));

        $hotel = Hotel::query()->sole();
        $this->assertCount(2, $hotel->rooms);
        $this->assertSame(2_500_000, $hotel->price_from);
        $this->assertSame('Oceanfront Infinity Pool', $hotel->amenities[0]['label']);

        $deluxe = $hotel->rooms->firstWhere('name', 'Deluxe');
        $villa = $hotel->rooms->firstWhere('name', 'Villa');

        $this->actingAs($this->admin)->put(route('admin.hotels.update', $hotel), array_merge($payload, ['rooms' => [
            ['id' => $deluxe->id, 'name' => 'Deluxe Ocean', 'guests' => 3, 'price_per_night' => '2.700.000', 'stock' => 4],
        ]]))->assertRedirect(route('admin.hotels'));

        $this->assertSame('Deluxe Ocean', $deluxe->fresh()->name);
        $this->assertModelMissing($villa);
    }

    public function test_hotel_editor_follows_figma_and_stores_partner_fields(): void
    {
        $this->actingAs($this->admin)->get(route('admin.hotels.create'))
            ->assertOk()
            ->assertSeeInOrder([
                'Save Draft', 'Publish Hotel Listing',
                'Property Overview', 'Property Name', 'Accommodation Type', 'Star Rating', 'Property Description',
                'Room Categories & Inventory Manager', 'Categories Active', '+ Add Another Room Category',
                'Premium Hotel Amenities', 'Starlink Mesh', '24/7 Butler Service', 'Air Conditioning',
                'Photo Gallery & Room Images', 'images uploaded', 'Browse Files', 'Featured Hero',
                'Publishing Settings', 'Publish Immediately', 'Save as Draft',
                'Location & Harbor Proximity', 'Island / Region', 'Specific Coastal Area', 'Harbor Transfer Distance', 'Map Pin Coordinates',
            ])
            ->assertDontSee('Fastboat Transfer Bundle')
            ->assertDontSee('Partner Commission Rate');

        $payload = [
            'name' => 'Toya Pakeh Cliff Resort',
            'category' => 'Luxury Resort',
            'stars' => 5,
            'description' => 'Cliffside sanctuary.',
            'region' => 'Nusa Penida',
            'address' => 'Toya Pakeh, Crystal Bay Road',
            'harbor_distance' => '8 minutes from Banjar Nyuh Harbor',
            'coordinates' => '-8.6792° S, 115.4851° E',
            'publish' => 'draft',
            'rooms' => [['name' => 'Deluxe', 'guests' => 2, 'price_per_night' => '2.500.000', 'stock' => 8]],
        ];

        $this->actingAs($this->admin)->post(route('admin.hotels.store'), $payload)
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.hotels'));

        $hotel = Hotel::query()->sole();
        // "Save as Draft" parks the listing in review.
        $this->assertSame(ListingStatus::Draft, $hotel->status);
        $this->assertSame('Nusa Penida', $hotel->region);
        $this->assertSame('8 minutes from Banjar Nyuh Harbor', $hotel->harbor_distance);
        $this->assertSame('-8.6792° S, 115.4851° E', $hotel->coordinates);

        $this->actingAs($this->admin)->get(route('admin.hotels.edit', $hotel))
            ->assertOk()
            ->assertSee('Toya Pakeh Cliff Resort')
            ->assertSee('8 Units Left')
            ->assertSee('IDR 2.500.000');

        // "Publish Immediately" (or the header button) flips it live.
        $this->actingAs($this->admin)->put(route('admin.hotels.update', $hotel), ['publish' => 'publish'] + $payload)->assertSessionHasNoErrors();
        $this->assertSame(ListingStatus::Active, $hotel->fresh()->status);
    }

    public function test_hotel_description_keeps_basic_formatting_and_shows_it_on_the_detail_page(): void
    {
        $this->actingAs($this->admin)->post(route('admin.hotels.store'), [
            'name' => 'Cliff Edge Resort',
            'category' => 'Resort',
            'stars' => 5,
            'description' => '<p>Perched on the <strong>cliff edge</strong>.</p><ul><li>Infinity pool</li></ul><script>alert(1)</script>',
            'address' => 'Nusa Penida, Bali',
            'publish' => 'publish',
            'rooms' => [['name' => 'Deluxe', 'guests' => 2, 'price_per_night' => '2.500.000', 'stock' => 2]],
        ])->assertRedirect(route('admin.hotels'));

        $hotel = Hotel::query()->sole();

        // The toolbar's own tags survive; anything else is stripped before it is stored.
        $this->assertStringContainsString('<strong>cliff edge</strong>', $hotel->description);
        $this->assertStringContainsString('<li>Infinity pool</li>', $hotel->description);
        $this->assertStringNotContainsString('<script>', $hotel->description);

        $this->get(route('hotels.show', $hotel))
            ->assertOk()
            ->assertSee('<strong>cliff edge</strong>', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_hotel_requires_at_least_one_room(): void
    {
        $this->actingAs($this->admin)->post(route('admin.hotels.store'), [
            'name' => 'Empty Inn', 'category' => 'Hotel', 'stars' => 3, 'description' => 'x', 'address' => 'y', 'status' => 'active',
            'rooms' => [['name' => '', 'price_per_night' => '']],
        ])->assertSessionHasErrors('rooms');

        $this->assertDatabaseCount('hotels', 0);
    }

    public function test_room_capacity_cannot_exceed_the_online_booking_cap(): void
    {
        $this->actingAs($this->admin)->post(route('admin.hotels.store'), [
            'name' => 'Big Rooms Inn', 'category' => 'Hotel', 'stars' => 3, 'description' => 'x', 'address' => 'y', 'status' => 'active',
            'rooms' => [['name' => 'Family', 'guests' => 6, 'bed' => '2 Queen Beds', 'price_per_night' => '1.000.000', 'stock' => 1]],
        ])->assertSessionHasErrors('rooms.0.guests');

        $this->assertDatabaseCount('hotels', 0);
    }

    public function test_article_listing_matches_figma_and_filters_by_author_category_and_status(): void
    {
        Article::factory()->create(['title' => 'Complete Guide to Nusa Penida', 'category' => 'Travel Guides', 'author_name' => 'Capt. Wayan Sudira', 'author_role' => 'Master Mariner', 'views' => 42_500, 'status' => ArticleStatus::Published]);
        Article::factory()->create(['title' => 'Top 7 Snorkeling Spots', 'category' => 'Activities', 'author_name' => 'Dewa Krisna', 'views' => 980, 'status' => ArticleStatus::Draft]);

        // An author with no article yet is still offered as a filter.
        Author::factory()->create(['name' => 'Ayu Pradnya']);

        $this->actingAs($this->admin)->get(route('admin.articles'))
            ->assertOk()
            ->assertSeeInOrder(['Search by title, keyword, or author...', 'Author:', 'All Authors', 'Ayu Pradnya', 'Category:', 'All Categories', 'Status:', 'All Statuses'])
            ->assertSeeInOrder(['Article Details', 'Category', 'Author & Role', 'Views', 'Published Date', 'Status', 'Quick Actions'])
            ->assertSee('42.5K')->assertSee('Master Mariner')
            // The chip row is gone and scheduling is not a status the console offers.
            ->assertDontSee('Active Filters:')
            ->assertDontSee('Clear all')
            ->assertDontSee('Scheduled');

        $this->actingAs($this->admin)->get(route('admin.articles', ['author' => 'Dewa Krisna']))
            ->assertSee('Top 7 Snorkeling Spots')->assertDontSee('Complete Guide to Nusa Penida');

        $this->actingAs($this->admin)->get(route('admin.articles', ['category' => 'Travel Guides']))
            ->assertSee('Complete Guide to Nusa Penida')->assertDontSee('Top 7 Snorkeling Spots');

        $this->actingAs($this->admin)->get(route('admin.articles', ['status' => 'draft']))
            ->assertSee('Top 7 Snorkeling Spots')->assertDontSee('Complete Guide to Nusa Penida');
    }

    public function test_changing_an_author_updates_the_byline_on_every_article_they_wrote(): void
    {
        $author = Author::factory()->create(['name' => 'Adityarana', 'role' => 'Junior Writer']);
        $first = Article::factory()->create(['title' => 'First Piece', 'author_id' => $author->id, 'author_name' => 'Adityarana', 'author_role' => 'Junior Writer', 'status' => ArticleStatus::Published, 'published_at' => now()->subDay()]);
        $second = Article::factory()->create(['title' => 'Second Piece', 'author_id' => $author->id, 'author_name' => 'Adityarana', 'author_role' => 'aca', 'status' => ArticleStatus::Published, 'published_at' => now()->subDay()]);

        $author->update(['role' => 'Senior Travel Writer & Island Specialist']);

        // Both articles follow the author record, whatever role was stored when they were saved.
        $this->assertSame('Senior Travel Writer & Island Specialist', $first->fresh()->author_role);
        $this->assertSame('Senior Travel Writer & Island Specialist', $second->fresh()->author_role);

        $this->actingAs($this->admin)->get(route('admin.articles'))
            ->assertOk()
            ->assertDontSee('Junior Writer')
            ->assertDontSee('>aca<', false);

        // Renaming the author carries through too, and the filter still finds their work.
        $author->update(['name' => 'Adit Mahayana']);

        $this->assertSame('Adit Mahayana', $first->fresh()->author_name);
        $this->actingAs($this->admin)->get(route('admin.articles', ['author' => 'Adit Mahayana']))
            ->assertSee('First Piece')
            ->assertSee('Second Piece');
    }

    public function test_article_scheduling_requires_a_date_and_drafts_stay_unpublished(): void
    {
        $base = ['title' => 'Crossing Tips', 'excerpt' => 'Short', 'category' => 'Boat Tips', 'body' => str_repeat('word ', 400), 'author_name' => 'Capt. Wayan'];

        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + ['status' => 'scheduled'])
            ->assertSessionHasErrors('published_at');

        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + ['status' => 'draft', 'tags' => 'nusapenida, #tips'])
            ->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();
        $this->assertSame(ArticleStatus::Draft, $article->status);
        $this->assertNull($article->published_at);
        $this->assertSame(['#nusapenida', '#tips'], $article->tags);
        $this->assertSame(2, $article->read_time_minutes);
    }

    public function test_article_body_keeps_headings_quotes_and_safe_links(): void
    {
        $body = '<h2>Getting there</h2><p class="drop-cap" style="color:red">Book <strong>early</strong>, <u>always</u>.</p>'
            .'<blockquote>Rough seas in January.</blockquote>'
            .'<p><a href="https://example.com" onclick="steal()">Timetable</a> and <a href="javascript:alert(1)">bad</a></p>'
            .'<script>alert(1)</script>';

        $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'title' => 'Crossing Tips', 'excerpt' => 'Short summary', 'category' => 'Boat Tips',
            'body' => $body, 'author_id' => 'new', 'author_name' => 'Capt. Wayan', 'status' => 'published',
        ])->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();

        $this->assertStringContainsString('<h2>Getting there</h2>', $article->body);
        $this->assertStringContainsString('<p class="drop-cap">', $article->body);
        $this->assertStringContainsString('<u>always</u>', $article->body);
        $this->assertStringContainsString('<blockquote>Rough seas in January.</blockquote>', $article->body);

        // A safe link keeps its href; the handler attribute, the javascript: URL and the script all go.
        $this->assertStringContainsString('<a href="https://example.com"', $article->body);
        $this->assertStringNotContainsString('onclick', $article->body);
        $this->assertStringNotContainsString('style=', $article->body);
        $this->assertStringNotContainsString('javascript:', $article->body);
        $this->assertStringNotContainsString('<script>', $article->body);

        // Headings are rendered with an id so the table of contents can link to them.
        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('<h2 id="getting-there">Getting there</h2>', false)
            ->assertSee('<blockquote>Rough seas in January.</blockquote>', false)
            ->assertSee('href="#getting-there"', false)
            ->assertSee('Table of Contents');
    }

    public function test_sidebar_lists_the_article_headings_and_the_most_read_articles(): void
    {
        $author = Author::factory()->create();
        $article = Article::factory()->create([
            'status' => ArticleStatus::Published, 'published_at' => now()->subDay(), 'author_id' => $author->id,
            'content' => null, 'views' => 1,
            'body' => '<h2>Harbours</h2><p>Text</p><h3>Sanur</h3><p>More</p><h2>Harbours</h2>',
        ]);

        $quiet = Article::factory()->create(['title' => 'Quiet Read', 'status' => ArticleStatus::Published, 'published_at' => now()->subDay(), 'views' => 2]);
        $busy = Article::factory()->create(['title' => 'Most Read', 'status' => ArticleStatus::Published, 'published_at' => now()->subDay(), 'views' => 900]);
        Article::factory()->create(['title' => 'Hidden Draft', 'status' => ArticleStatus::Draft, 'views' => 5000]);

        // Headings become the table of contents; a repeated heading still gets its own anchor.
        $this->assertSame(
            [['label' => 'Harbours', 'anchor' => 'harbours', 'level' => 2],
                ['label' => 'Sanur', 'anchor' => 'sanur', 'level' => 3],
                ['label' => 'Harbours', 'anchor' => 'harbours-2', 'level' => 2]],
            $article->toc_items,
        );

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('href="#harbours-2"', false)
            // Popular Articles is ordered by views and skips drafts.
            ->assertSeeInOrder(['Popular Articles', 'Most Read', 'Quiet Read'])
            ->assertDontSee('Hidden Draft')
            ->assertSee(route('articles.show', $busy));
    }

    public function test_article_body_images_are_uploaded_and_only_safe_sources_are_kept(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.articles.image'), [
            'image' => $this->fakeImage('deck.jpg'),
        ])->assertOk();

        $url = $response->json('url');
        $this->assertStringContainsString('uploads/articles/', $url);

        $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'title' => 'Photo Story', 'excerpt' => 'Short summary', 'category' => 'Boat Tips',
            'body' => '<p>Deck</p><img src="'.$url.'" alt="Deck" width="900" onerror="x()"><img src="javascript:alert(1)">',
            'author_id' => 'new', 'author_name' => 'Capt. Wayan', 'status' => 'published',
        ])->assertRedirect(route('admin.articles'));

        $body = Article::query()->sole()->body;

        // The uploaded photo keeps src and alt only; the javascript: one is dropped entirely.
        $this->assertStringContainsString('<img src="'.$url.'" alt="Deck">', $body);
        $this->assertStringNotContainsString('onerror', $body);
        $this->assertStringNotContainsString('width=', $body);
        $this->assertStringNotContainsString('javascript:', $body);
    }

    public function test_article_address_always_follows_the_title(): void
    {
        $base = ['excerpt' => 'Short summary', 'category' => 'Boat Tips', 'body' => 'Body text',
            'author_id' => 'new', 'author_name' => 'Capt. Wayan', 'status' => 'published'];

        // A slug posted by hand is ignored: the address comes from the title.
        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + [
            'title' => 'Crossing Tips', 'slug' => 'something-else',
        ])->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();
        $this->assertSame('crossing-tips', $article->slug);
        $this->get(route('articles.show', $article))->assertOk();

        // Renaming moves the address with it.
        $this->actingAs($this->admin)->put(route('admin.articles.update', $article), $base + ['title' => 'Calm Water Crossings'])
            ->assertRedirect(route('admin.articles'));

        $this->assertSame('calm-water-crossings', $article->fresh()->slug);

        // Saving without renaming keeps the same address.
        $this->actingAs($this->admin)->put(route('admin.articles.update', $article->fresh()), $base + ['title' => 'Calm Water Crossings']);
        $this->assertSame('calm-water-crossings', $article->fresh()->slug);

        // A second article with the same title gets its own address rather than clashing.
        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + ['title' => 'Calm Water Crossings']);
        $this->assertSame('calm-water-crossings-2', Article::query()->latest('id')->first()->slug);

        // Every card on the listing points at a page that exists.
        $this->get(route('articles.index'))->assertOk()->assertSee(route('articles.show', $article->fresh()));
    }

    public function test_author_profile_is_editable_and_drives_the_byline_and_card(): void
    {
        Storage::fake('public');

        $base = ['title' => 'Crossing Tips', 'excerpt' => 'Short summary', 'category' => 'Boat Tips',
            'body' => 'Body text', 'status' => 'published'];

        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + [
            'author_id' => 'new',
            'author_name' => 'Capt. Wayan Sudira',
            'author_role' => 'Master Mariner',
            'author_credential' => 'ANT-IV Certified',
            'author_bio' => 'Twelve years on the Badung Strait.',
            'author_photo' => $this->fakeImage('wayan.jpg'),
        ])->assertRedirect(route('admin.articles'));

        $author = Author::query()->sole();
        $this->assertSame('Master Mariner', $author->role);
        $this->assertSame('ANT-IV Certified', $author->credential);
        Storage::disk('public')->assertExists($author->photo);

        $this->get(route('articles.show', Article::query()->sole()))
            ->assertOk()
            ->assertSee('Written by Capt. Wayan Sudira')
            ->assertSee('Twelve years on the Badung Strait.')
            ->assertSee('ANT-IV Certified')
            ->assertSee($author->photo_url)
            // The share row is gone.
            ->assertDontSee('Share:');

        // No portrait and no bio: initials stand in and the card stays hidden.
        $author->forceFill(['photo' => null, 'bio' => null])->save();

        $this->get(route('articles.show', Article::query()->sole()))
            ->assertOk()
            ->assertSee('CW')
            ->assertDontSee('Twelve years on the Badung Strait.');
    }

    public function test_article_form_renders_and_stores_seo_fields_with_fallbacks(): void
    {
        // Figma 1:8059: editorial card, formatting toolbar, hero image, and the sidebar cards incl. the author form.
        $this->actingAs($this->admin)->get(route('admin.articles.create'))
            ->assertOk()
            ->assertSeeInOrder([
                'Save Draft', 'Publish Article',
                'Article Core Editorial', 'Article Title', 'Subtitle / Summary Hook', 'Primary Category', 'Target Reader Segment', 'Author', '+ Add new author…',
                'H2', 'H3', 'Word Count:',
                'Featured Hero Image', 'Replace Photo', 'Image Caption', 'Descriptive Alt Text (Accessibility & SEO)',
                'Publishing Settings', 'Publish Immediately', 'Save as Draft',

                'SEO Optimization', 'Score:', 'URL Permalink', 'Generated from the title.', 'Meta Title', '/60 chars', 'Meta Description', '/160 chars', 'Live Google SERP Preview',
                'Tags & Taxonomy', 'Type tag and hit Enter...',
            ])
            ->assertDontSee('Schedule for Later')
            ->assertDontSee('Contextual Fast Ticket Desk')
            ->assertDontSee('Keywords')
            ->assertDontSee('min read')
            // The toolbar buttons are real controls, not decoration.
            ->assertSee('data-editor-command="bold"', false)
            ->assertSee('data-editor-command="underline"', false)
            ->assertSee('data-editor-command="createLink"', false)
            ->assertSee('data-editor-command="undo"', false)
            ->assertSee('data-editor-dropcap', false)
            ->assertDontSee('data-editor-command="removeFormat"', false)
            ->assertSee('data-editor-value="blockquote"', false)
            ->assertSee(route('admin.articles.image'));

        $base = ['title' => 'Crossing Tips', 'excerpt' => 'Short summary', 'category' => 'Boat Tips', 'body' => 'Body text', 'author_id' => 'new', 'author_name' => 'Capt. Wayan', 'status' => 'published'];

        $this->actingAs($this->admin)->post(route('admin.articles.store'), $base + [
            'meta_title' => 'Crossing Tips | Penida Gili',
            'meta_description' => 'Everything about the crossing.',
            'meta_keywords' => 'nusa penida, fast boat, , fast boat',
            'author_role' => 'Master Mariner',
            'reader_segment' => 'First-time Island Travelers',
            'hero_alt' => 'Fast boat at sea',
            'submit_as' => 'draft',
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();
        $this->assertSame(['nusa penida', 'fast boat'], $article->meta_keywords);
        $this->assertSame('Crossing Tips | Penida Gili', $article->seo_title);
        $this->assertSame('Master Mariner', $article->author_role);
        $this->assertSame('First-time Island Travelers', $article->reader_segment);
        $this->assertSame('Fast boat at sea', $article->hero_alt);
        // The header "Save Draft" button beats the Publish Immediately radio.
        $this->assertSame(ArticleStatus::Draft, $article->status);

        $author = Author::query()->sole();
        $this->assertSame($author->id, $article->author_id);
        $this->actingAs($this->admin)->get(route('admin.articles.edit', $article))->assertOk()->assertSee('Capt. Wayan - Master Mariner');

        // Picking an existing author from the bar fills the byline from that record.
        $other = Author::factory()->create(['name' => 'Putri Pratiwi', 'role' => 'Travel Concierge']);
        $this->actingAs($this->admin)->put(route('admin.articles.update', $article), ['author_id' => $other->id, 'submit_as' => 'publish'] + $base)->assertSessionHasNoErrors();
        $this->assertSame('Putri Pratiwi', $article->fresh()->author_name);
        $this->assertSame('Travel Concierge', $article->fresh()->author_role);

        $this->actingAs($this->admin)->put(route('admin.articles.update', $article), $base + [
            'submit_as' => 'publish',
            'meta_title' => 'Crossing Tips | Penida Gili',
            'meta_description' => 'Everything about the crossing.',
            'meta_keywords' => 'nusa penida, fast boat',
        ])->assertSessionHasNoErrors();
        $article->refresh();
        $this->assertSame(ArticleStatus::Published, $article->status);

        $this->get(route('articles.show', $article))
            ->assertOk()
            ->assertSee('<title>Crossing Tips | Penida Gili — Penida Gili</title>', false)
            ->assertSee('<meta name="description" content="Everything about the crossing.">', false)
            ->assertSee('<meta name="keywords" content="nusa penida, fast boat">', false);

        $article->update(['meta_title' => null, 'meta_description' => null, 'meta_keywords' => null]);

        $this->get(route('articles.show', $article))
            ->assertSee('<title>Crossing Tips — Penida Gili</title>', false)
            ->assertSee('<meta name="description" content="Short summary">', false)
            ->assertDontSee('name="keywords"', false);
    }

    public function test_every_console_listing_carries_the_pagination_bar(): void
    {
        Activity::factory()->create();
        Article::factory()->create();
        Vessel::factory()->create();
        Hotel::factory()->create();
        Schedule::factory()->create();
        Booking::factory()->create();

        // One page of rows still shows Prev / 1 / Next, so every screen has the same footer.
        $pages = [
            'admin.activities' => 'activities',
            'admin.articles' => 'articles',
            'admin.boats' => 'boats',
            'admin.hotels' => 'hotels',
            'admin.schedules' => 'schedules',
            'admin.report' => 'bookings',
        ];

        foreach ($pages as $route => $entity) {
            $this->actingAs($this->admin)->get(route($route))
                ->assertOk()
                ->assertSeeInOrder(['Showing 1 to', $entity, 'Prev', '>1<', 'Next'], false);
        }
    }

    public function test_report_lists_bookings_and_changes_status(): void
    {
        $booking = Booking::factory()->create(['customer_name' => 'Report Person', 'status' => BookingStatus::Pending]);

        $this->actingAs($this->admin)->get(route('admin.report'))
            ->assertOk()
            ->assertSeeInOrder(['Booking Report', 'Download Report', 'Search Route', 'Boat', 'Date', 'Filter'])
            ->assertSeeInOrder(['Passenger', 'Route', 'Date & Time', 'Amount', 'Status', 'Action'])
            ->assertSee('Report Person')
            ->assertSee('Mark as Confirmed');
        $this->actingAs($this->admin)->get(route('admin.report', ['q' => 'nobody-here']))->assertOk()->assertDontSee('Report Person');

        // Search Route matches the ports of the booked schedule; the Boat filter matches its vessel.
        $sanur = Port::factory()->create(['name' => 'Sanur Beach Port', 'area' => 'Bali']);
        $penida = Port::factory()->create(['name' => 'Banjar Nyuh Nusa Penida', 'area' => 'Nusa Penida']);
        $queen = Vessel::factory()->create(['name' => 'Sanjaya Ocean Queen']);
        $schedule = Schedule::factory()->create(['from_port_id' => $sanur->id, 'to_port_id' => $penida->id, 'vessel_id' => $queen->id]);
        Booking::factory()->create(['customer_name' => 'Route Person', 'bookable_type' => $schedule->getMorphClass(), 'bookable_id' => $schedule->id]);

        $this->actingAs($this->admin)->get(route('admin.report', ['q' => 'Sanur to Nusa Penida']))
            ->assertSee('Route Person')->assertDontSee('Report Person');
        $this->actingAs($this->admin)->get(route('admin.report', ['vessel' => $queen->id]))
            ->assertSee('Route Person')->assertDontSee('Report Person');

        $this->actingAs($this->admin)->patch(route('admin.report.update', $booking), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->confirmed_at);
    }

    public function test_report_export_streams_csv(): void
    {
        Booking::factory()->create(['customer_email' => 'csv@example.com']);

        $response = $this->actingAs($this->admin)->get(route('admin.report.export'));

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('csv@example.com', $response->streamedContent());
    }

    public function test_dashboard_renders_with_seeded_style_data(): void
    {
        $vessel = Vessel::factory()->create();
        Schedule::factory()->for($vessel->operator, 'operator')->create(['vessel_id' => $vessel->id]);
        Booking::factory()->confirmed()->create();

        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->assertSee('Total Revenue');
    }

    public function test_dashboard_transactions_mirror_the_booking_report_and_are_read_only(): void
    {
        $schedule = Schedule::factory()->create();
        $hotelRoom = HotelRoom::factory()->create();

        Booking::factory()->count(12)->for($schedule, 'bookable')->create(['customer_name' => 'Boat Guest']);
        $stay = Booking::factory()->for($hotelRoom, 'bookable')->create(['customer_name' => 'Hotel Guest', 'status' => BookingStatus::Pending]);

        // Ten rows per page, the rest on page two.
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Showing 1 to 10 of 13 bookings')
            ->assertSee(route('admin.dashboard', ['page' => 2]), false);

        $this->actingAs($this->admin)->get(route('admin.dashboard', ['page' => 2]))
            ->assertOk()
            ->assertSee('Showing 11 to 13 of 13 bookings');

        // Filtering narrows to one product.
        $this->actingAs($this->admin)->get(route('admin.dashboard', ['type' => 'hotel']))
            ->assertOk()
            ->assertSee('Hotel Guest')
            ->assertDontSee('Boat Guest');

        // The panel is read-only: status changes belong to the Booking Report.
        $this->actingAs($this->admin)->get(route('admin.dashboard'))
            ->assertDontSee('Approve booking')
            ->assertDontSee(route('admin.report.update', $stay->reference));

        // Both pages list the same reservations under the same filter.
        $dashboard = $this->actingAs($this->admin)->get(route('admin.dashboard', ['q' => 'Hotel Guest']));
        $report = $this->actingAs($this->admin)->get(route('admin.report', ['q' => 'Hotel Guest']));

        $dashboard->assertOk()->assertSee('Hotel Guest')->assertDontSee('Boat Guest');
        $report->assertOk()->assertSee('Hotel Guest')->assertDontSee('Boat Guest');
    }

    public function test_hotel_toolbar_filters_by_search_destination_stars_and_status(): void
    {
        Hotel::factory()->create(['name' => 'Semabu Hills', 'address' => 'Ped, Nusa Penida, Bali', 'stars' => 5, 'status' => ListingStatus::Active, 'partner_label' => 'Hilltop Panorama']);
        Hotel::factory()->create(['name' => 'Batu Karang', 'address' => 'Jungutbatu, Nusa Lembongan', 'stars' => 4, 'status' => ListingStatus::Draft]);

        $this->actingAs($this->admin)->get(route('admin.hotels'))
            ->assertOk()
            ->assertSee('Search by hotel name, beach or area...')
            ->assertSeeInOrder(['All Destinations', 'Nusa Penida', 'All Star Ratings', '5 Stars', 'Status: All', 'Status: Active'])
            ->assertSeeInOrder(['Registered Partner Accommodations', '1 of 2 Active Listed', 'Refresh Rates'])
            ->assertSeeInOrder(['Hotel / Resort', 'Location', 'Rating', 'Room Types', 'Starting Price / Night', 'Status', 'Bookings (Mo)', 'Actions'])
            ->assertSee('Excl. taxes')->assertSee('% full')
            ->assertSee('Semabu Hills')->assertSee('Batu Karang');

        $this->actingAs($this->admin)->get(route('admin.hotels', ['q' => 'hilltop']))
            ->assertSee('Semabu Hills')->assertDontSee('Batu Karang');

        $this->actingAs($this->admin)->get(route('admin.hotels', ['destination' => 'Nusa Lembongan']))
            ->assertSee('Batu Karang')->assertDontSee('Semabu Hills');

        $this->actingAs($this->admin)->get(route('admin.hotels', ['stars' => 5]))
            ->assertSee('Semabu Hills')->assertDontSee('Batu Karang');

        $this->actingAs($this->admin)->get(route('admin.hotels', ['status' => 'draft']))
            ->assertSee('Batu Karang')->assertDontSee('Semabu Hills');
    }

    public function test_hotel_room_rates_show_in_public_listing_after_admin_creates_them(): void
    {
        $hotel = Hotel::factory()->create(['name' => 'Rate Check Resort']);
        HotelRoom::factory()->for($hotel)->create(['price_per_night' => 1_234_000]);

        $this->get(route('hotels.index'))->assertOk()->assertSee('Rate Check Resort')->assertSee('IDR 1.234.000');
    }
}
