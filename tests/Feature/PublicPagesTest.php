<?php

namespace Tests\Feature;

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
}
