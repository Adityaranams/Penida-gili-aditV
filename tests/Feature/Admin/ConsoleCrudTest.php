<?php

namespace Tests\Feature\Admin;

use App\Enums\ListingStatus;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\StoreHotelRequest;
use App\Http\Requests\Admin\StoreScheduleRequest;
use App\Http\Requests\Admin\StoreVesselRequest;
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
use Illuminate\Support\Facades\Storage;
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

    /**
     * Guard against dead UI: every control on a console create screen must be wired to
     * something — a form field the request validates, or a button the editor JS listens for.
     */
    public function test_console_create_screens_have_no_dead_controls(): void
    {
        BoatOperator::factory()->create();
        Port::factory()->count(2)->create();
        Vessel::factory()->create();

        $screens = [
            'admin.boats.create' => StoreVesselRequest::class,
            'admin.schedules.create' => StoreScheduleRequest::class,
            'admin.activities.create' => StoreActivityRequest::class,
            'admin.hotels.create' => StoreHotelRequest::class,
            'admin.articles.create' => StoreArticleRequest::class,
        ];

        foreach ($screens as $route => $requestClass) {
            $html = $this->actingAs($this->admin)->get(route($route))->assertOk()->getContent();

            // Every posted field is covered by the form request. The rule keys are read from the
            // source: resolving a FormRequest out of the container would validate this GET request.
            preg_match_all('/<(?:input|select|textarea)[^>]*\bname="([a-z_]+)(\[\])?"/', $html, $matches);
            preg_match_all("/'([a-z_]+)(?:\.\*)?' =>/", (string) file_get_contents((new \ReflectionClass($requestClass))->getFileName()), $ruleMatches);
            $rules = array_unique($ruleMatches[1]);

            $posted = array_diff(array_unique($matches[1]), ['_token', '_method', 'q', 'ids']);

            foreach ($posted as $field) {
                $this->assertContains($field, $rules, "{$route}: the {$field} field has no validation rule.");
            }

            // No toolbar is left as a decorative, unclickable strip.
            $this->assertStringNotContainsString('aria-hidden="true">
                                <span class="rounded', $html, "{$route}: a toolbar is still decorative.");

            foreach (['data-editor-command', 'data-stepper', 'data-room-add', 'data-select-all-days'] as $hook) {
                if (str_contains($html, $hook)) {
                    $this->assertStringContainsString('type="button"', $html, "{$route}: {$hook} is not on a real button.");
                }
            }
        }
    }

    /**
     * Uploads have to survive the whole round trip: stored on the public disk, kept on the
     * record, and served from /storage on the page the traveller sees.
     */
    public function test_uploaded_images_are_stored_and_served_for_every_module(): void
    {
        Storage::fake('public');
        BoatOperator::factory()->create();

        // Activity: cover plus a gallery photo.
        $this->actingAs($this->admin)->post(route('admin.activities.store'), [
            'name' => 'Cliff Walk',
            'category' => 'Wildlife & Nature',
            'description' => 'Walk the cliff path.',
            'price_adult' => '90.000',
            'max_daily_capacity' => '15',
            'status' => ListingStatus::Active->value,
            'cover' => $this->fakeImage('cliff.jpg'),
            'gallery' => [$this->fakeImage('view.jpg')],
        ])->assertRedirect(route('admin.activities'));

        $activity = Activity::query()->sole();
        $this->assertStringStartsWith('uploads/activities/', $activity->image);
        Storage::disk('public')->assertExists($activity->image);
        Storage::disk('public')->assertExists($activity->gallery[0]['image']);

        $this->get(route('activities.show', $activity))->assertOk()->assertSee('/storage/'.$activity->image, false);

        // Editing adds to the gallery and drops only the photo that was ticked.
        $first = $activity->gallery[0]['image'];
        $this->actingAs($this->admin)->put(route('admin.activities.update', $activity), [
            'name' => 'Cliff Walk',
            'category' => 'Wildlife & Nature',
            'description' => 'Walk the cliff path.',
            'price_adult' => '90.000',
            'max_daily_capacity' => '15',
            'status' => ListingStatus::Active->value,
            'gallery' => [$this->fakeImage('second.jpg')],
            'remove_photos' => [$first],
        ])->assertRedirect(route('admin.activities'));

        $gallery = $activity->fresh()->gallery;
        $this->assertCount(1, $gallery);
        $this->assertNotSame($first, $gallery[0]['image']);

        // Hotel: cover plus gallery, with the same add / remove behaviour.
        $hotelPayload = [
            'name' => 'Cliff Edge Resort',
            'category' => 'Resort',
            'stars' => 5,
            'description' => 'Perched on the cliffs.',
            'address' => 'Nusa Penida, Bali',
            'publish' => 'publish',
            'rooms' => [['name' => 'Deluxe', 'guests' => 2, 'price_per_night' => '2.500.000', 'stock' => 2]],
        ];

        $this->actingAs($this->admin)->post(route('admin.hotels.store'), $hotelPayload + [
            'cover' => $this->fakeImage('resort.jpg'),
            'gallery' => [$this->fakeImage('pool.jpg')],
        ])->assertRedirect(route('admin.hotels'));

        $hotel = Hotel::query()->sole();
        Storage::disk('public')->assertExists($hotel->image);
        $poolPhoto = $hotel->gallery[0]['image'];

        // The card carries the cover; the detail hero shows the gallery.
        $this->get(route('hotels.index'))->assertOk()->assertSee('/storage/'.$hotel->image, false);
        $this->get(route('hotels.show', $hotel))->assertOk()->assertSee('/storage/'.$poolPhoto, false);

        $this->actingAs($this->admin)->put(route('admin.hotels.update', $hotel), $hotelPayload + [
            'gallery' => [$this->fakeImage('spa.jpg')],
            'remove_photos' => [$poolPhoto],
        ])->assertRedirect(route('admin.hotels'));

        $hotelGallery = $hotel->fresh()->gallery;
        $this->assertCount(1, $hotelGallery);
        $this->assertNotSame($poolPhoto, $hotelGallery[0]['image']);
        // The cover is kept when no new one is uploaded.
        $this->assertSame($hotel->image, $hotel->fresh()->image);

        // Boat: cover photo reaches the public vessel page.
        $this->actingAs($this->admin)->post(route('admin.boats.store'), [
            'name' => 'Sanjaya Ocean Queen',
            'type' => 'Catamaran',
            'capacity' => 150,
            'status' => ListingStatus::Active->value,
            'cover' => $this->fakeImage('queen.jpg'),
        ])->assertRedirect(route('admin.boats'));

        $vessel = Vessel::query()->sole();
        Storage::disk('public')->assertExists($vessel->image);
        $this->get(route('boats.vessel', $vessel))->assertOk()->assertSee('/storage/'.$vessel->image, false);

        // Article: cover reaches the blog.
        $author = Author::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'title' => 'Crossing the Badung Strait',
            'excerpt' => 'Morning crossings are calmest.',
            'category' => 'Boat Tips',
            'body' => 'Morning departures give the calmest water.',
            'author_id' => $author->id,
            'status' => 'published',
            'cover' => $this->fakeImage('strait.jpg'),
        ])->assertRedirect(route('admin.articles'));

        $article = Article::query()->sole();
        Storage::disk('public')->assertExists($article->image);
        $this->get(route('articles.show', $article))->assertOk()->assertSee('/storage/'.$article->image, false);
    }
}
