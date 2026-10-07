<?php

namespace Tests\Feature;

use App\Enums\ArticleStatus;
use App\Enums\ListingStatus;
use App\Models\Activity;
use App\Models\Article;
use App\Models\BoatOperator;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Port;
use App\Models\Schedule;
use App\Models\Vessel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_lists_the_best_rated_bookable_boats_only(): void
    {
        $operator = BoatOperator::factory()->create();
        Vessel::factory()->for($operator, 'operator')->create(['name' => 'Island Runner', 'rating' => 4.9]);
        Vessel::factory()->for($operator, 'operator')->create(['name' => 'Retired Runner', 'rating' => 5.0, 'status' => ListingStatus::Draft]);
        Vessel::factory()->for(BoatOperator::factory()->inactive(), 'operator')->create(['name' => 'Hidden Runner', 'rating' => 5.0]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Island Runner')
            ->assertDontSee('Retired Runner')
            ->assertDontSee('Hidden Runner');
    }

    public function test_boat_listing_shows_active_boats_only(): void
    {
        $operator = BoatOperator::factory()->create();
        $live = Vessel::factory()->for($operator, 'operator')->create(['name' => 'Island Runner']);
        Vessel::factory()->for($operator, 'operator')->create(['name' => 'Dry Docked', 'status' => ListingStatus::Inactive]);
        Vessel::factory()->for(BoatOperator::factory()->inactive(), 'operator')->create(['name' => 'Retired Operator Boat']);

        $this->get(route('boats.index'))
            ->assertOk()
            ->assertSee($live->name)
            ->assertDontSee('Dry Docked')
            ->assertDontSee('Retired Operator Boat');
    }

    public function test_boat_search_narrows_to_boats_sailing_the_route(): void
    {
        $sanur = Port::factory()->create(['name' => 'Sanur', 'area' => 'Bali']);
        $penida = Port::factory()->create(['name' => 'Nusa Penida', 'area' => 'Nusa Penida']);
        $gili = Port::factory()->create(['name' => 'Gili Trawangan', 'area' => 'Gili']);

        $match = BoatOperator::factory()->create(['name' => 'Penida Express']);
        Vessel::factory()->for($match, 'operator')->create(['name' => 'Penida Runner']);
        Schedule::factory()->for($match, 'operator')->create(['from_port_id' => $sanur->id, 'to_port_id' => $penida->id]);

        $other = BoatOperator::factory()->create(['name' => 'Gili Runner']);
        Vessel::factory()->for($other, 'operator')->create(['name' => 'Gili Glider']);
        Schedule::factory()->for($other, 'operator')->create(['from_port_id' => $sanur->id, 'to_port_id' => $gili->id]);

        $this->get(route('boats.index', ['from' => 'Sanur', 'to' => 'penida']))
            ->assertOk()
            ->assertSee('Penida Runner')
            ->assertDontSee('Gili Glider');
    }

    public function test_boat_page_shows_only_its_own_active_schedules(): void
    {
        $vessel = Vessel::factory()->create();
        $live = Schedule::factory()->for($vessel->operator, 'operator')->create(['vessel_id' => $vessel->id, 'departure_time' => '07:15']);
        Schedule::factory()->for($vessel->operator, 'operator')->draft()->create(['vessel_id' => $vessel->id, 'departure_time' => '21:45']);

        $this->get(route('boats.vessel', $vessel))
            ->assertOk()
            ->assertSee($live->departure_label)
            ->assertDontSee('09:45 PM');
    }

    public function test_boat_of_an_inactive_operator_is_not_found(): void
    {
        $vessel = Vessel::factory()->for(BoatOperator::factory()->inactive(), 'operator')->create();

        $this->get(route('boats.vessel', $vessel))->assertNotFound();
    }

    public function test_boat_order_page_prices_the_selected_schedule(): void
    {
        $operator = BoatOperator::factory()->create();
        $schedule = Schedule::factory()->for($operator, 'operator')->create(['price_adult' => 200_000, 'price_child' => 100_000]);

        $this->get(route('boats.order', [$operator, 'schedule' => $schedule->id, 'adults' => 2, 'children' => 1, 'date' => now()->addWeek()->toDateString()]))
            ->assertOk()
            ->assertSee('IDR 500.000');
    }

    public function test_hotel_order_page_charges_per_night(): void
    {
        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['price_per_night' => 1_000_000]);

        $this->get(route('hotels.order', [$hotel, 'room' => $room->id, 'check_in' => '2030-01-01', 'check_out' => '2030-01-04']))
            ->assertOk()
            ->assertSee('IDR 3.000.000');

        $this->get(route('hotels.order', [$hotel, 'room' => $room->id, 'check_in' => '2030-01-01', 'check_out' => '2030-01-04', 'rooms' => 2]))
            ->assertOk()
            ->assertSee('IDR 6.000.000');

        // 6 guests need two rooms even when only one was requested.
        $this->get(route('hotels.order', [$hotel, 'room' => $room->id, 'check_in' => '2030-01-01', 'check_out' => '2030-01-04', 'adults' => 6, 'rooms' => 1]))
            ->assertOk()
            ->assertSee('IDR 6.000.000');
    }

    public function test_activity_detail_and_order_pages_render(): void
    {
        $activity = Activity::factory()->create(['price_adult' => 90_000]);

        $this->get(route('activities.show', $activity))->assertOk()->assertSee($activity->name);
        $this->get(route('activities.order', [$activity, 'adults' => 3]))->assertOk()->assertSee('IDR 270.000');
    }

    public function test_draft_activity_is_hidden_from_listing_and_detail(): void
    {
        $draft = Activity::factory()->draft()->create(['name' => 'Secret Draft Tour']);

        $this->get(route('activities.index'))->assertOk()->assertDontSee('Secret Draft Tour');
        $this->get(route('activities.show', $draft))->assertNotFound();
    }

    public function test_article_search_filters_published_articles(): void
    {
        Article::factory()->create(['title' => 'Snorkeling With Mantas']);
        Article::factory()->create(['title' => 'Packing For Lembongan']);
        Article::factory()->draft()->create(['title' => 'Unpublished Snorkeling Draft']);

        $this->get(route('articles.index', ['q' => 'snorkeling']))
            ->assertOk()
            ->assertSee('Snorkeling With Mantas')
            ->assertDontSee('Packing For Lembongan')
            ->assertDontSee('Unpublished Snorkeling Draft');
    }

    public function test_article_detail_counts_a_view_and_hides_drafts(): void
    {
        $article = Article::factory()->create(['views' => 4]);
        $draft = Article::factory()->draft()->create();

        $this->get(route('articles.show', $article))->assertOk();
        $this->get(route('articles.show', $draft))->assertNotFound();

        $this->assertSame(5, $article->fresh()->views);
    }

    public function test_article_index_filters_by_category(): void
    {
        Article::factory()->create(['title' => 'Harbour Basics', 'category' => 'Boat Tips', 'status' => ArticleStatus::Published, 'published_at' => now()->subDay()]);
        Article::factory()->create(['title' => 'Temple Manners', 'category' => 'Culture', 'status' => ArticleStatus::Published, 'published_at' => now()->subDays(2)]);
        Article::factory()->create(['title' => 'Unpublished Idea', 'category' => 'Secret', 'status' => ArticleStatus::Draft]);

        // The pills list the categories that actually have published articles.
        $this->get(route('articles.index'))
            ->assertOk()
            ->assertSee('All Articles')
            ->assertSee('Boat Tips')
            ->assertSee('Culture')
            ->assertDontSee('Secret')
            ->assertDontSee('Maritime Journal');

        $this->get(route('articles.index', ['category' => 'Boat Tips']))
            ->assertOk()
            ->assertSee('Harbour Basics')
            ->assertDontSee('Temple Manners');

        // Searching inside a category keeps the category.
        $this->get(route('articles.index', ['category' => 'Boat Tips', 'q' => 'harbour']))
            ->assertOk()
            ->assertSee('Harbour Basics')
            ->assertDontSee('Temple Manners');
    }

    public function test_schedule_search_lists_sailings_on_the_route_with_guest_totals(): void
    {
        $sanur = Port::factory()->create(['name' => 'Sanur', 'area' => 'Bali']);
        $penida = Port::factory()->create(['name' => 'Nusa Penida', 'area' => 'Nusa Penida']);
        $gili = Port::factory()->create(['name' => 'Gili Air', 'area' => 'Gili']);

        $operator = BoatOperator::factory()->create();
        $vessel = Vessel::factory()->for($operator, 'operator')->create(['name' => 'Penida Runner']);
        Schedule::factory()->for($operator, 'operator')->create([
            'vessel_id' => $vessel->id, 'from_port_id' => $sanur->id, 'to_port_id' => $penida->id,
            'departure_time' => '08:00', 'arrival_time' => '08:45', 'price_adult' => 100_000,
        ]);
        Schedule::factory()->for($operator, 'operator')->create(['from_port_id' => $sanur->id, 'to_port_id' => $gili->id, 'departure_time' => '09:15']);
        Schedule::factory()->for($operator, 'operator')->draft()->create(['from_port_id' => $sanur->id, 'to_port_id' => $penida->id, 'departure_time' => '11:30']);

        $this->get(route('boats.schedules', ['from' => 'Sanur', 'to' => 'Nusa Penida', 'guests' => 3]))
            ->assertOk()
            ->assertSee('Penida Runner')
            ->assertSee('08:00')
            ->assertSee('IDR 300.000')
            ->assertDontSee('09:15')
            ->assertDontSee('11:30');
    }

    public function test_where_to_search_lists_activities_on_that_island(): void
    {
        Activity::factory()->create(['name' => 'Manta Snorkel Trip', 'location' => 'Manta Bay, Nusa Penida', 'place_label' => 'Manta Bay']);
        Activity::factory()->create(['name' => 'Kecak Fire Dance', 'location' => 'Uluwatu, Badung', 'place_label' => 'Uluwatu']);

        $this->get(route('activities.explore', ['q' => 'Nusa Penida']))
            ->assertOk()
            ->assertSee('Things to do')
            ->assertSee('Manta Snorkel Trip')
            ->assertDontSee('Kecak Fire Dance');

        $this->get(route('activities.explore', ['q' => 'Bali']))
            ->assertOk()
            ->assertSee('Kecak Fire Dance')
            ->assertDontSee('Manta Snorkel Trip');
    }

    public function test_the_guests_stepper_is_not_wrapped_in_a_label(): void
    {
        // A <label> adopts the first labelable element inside it as its control.
        // With the stepper inside one that was the - button, so pointing at +
        // put :hover on - as well.
        $html = $this->get(route('home'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<div class="block w-full">\s*<span[^>]*>Guests/', $html);
        $this->assertDoesNotMatchRegularExpression('/<label[^>]*>(?:(?!<\/label>).)*?data-stepper/s', $html);
    }

    public function test_explore_pages_nine_activities_at_a_time_and_names_their_place(): void
    {
        Activity::factory()->count(10)->sequence(fn ($sequence) => [
            'name' => 'Island Outing '.($sequence->index + 1),
            // Admin-created activities carry only a place label, so the card has
            // to fall back to it for the pin badge.
            'location' => null,
            'place_label' => 'Nusa Penida',
        ])->create();

        $first = $this->get(route('activities.explore'));
        $first->assertOk()->assertSee('Nusa Penida');
        $this->assertCount(9, $first->viewData('activities')->items());

        $second = $this->get(route('activities.explore', ['page' => 2]));
        $second->assertOk();
        $this->assertCount(1, $second->viewData('activities')->items());

        // The bar is drawn even on a single page, so the layout does not jump.
        $this->get(route('activities.explore', ['q' => 'Gili Trawangan']))
            ->assertOk()
            ->assertSee('aria-label="Pagination"', false);
    }
}
