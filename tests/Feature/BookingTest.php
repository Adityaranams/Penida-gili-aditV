<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Models\Activity;
use App\Models\BoatOperator;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\Schedule;
use App\Notifications\BookingReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, mixed> */
    private function traveller(array $overrides = []): array
    {
        return $overrides + [
            'full_name' => 'Ayu Lestari',
            'email' => 'ayu@example.com',
            'nationality' => 'Indonesia',
            'dial_code' => '+62',
            'phone' => '812 3456 7890',
            'notes' => 'Window seat please',
            'adults' => 2,
            'children' => 1,
            'travel_date' => now()->addDays(3)->toDateString(),
        ];
    }

    public function test_guest_can_book_a_boat_schedule(): void
    {
        Notification::fake();

        $operator = BoatOperator::factory()->create();
        $schedule = Schedule::factory()->for($operator, 'operator')->create(['price_adult' => 180_000, 'price_child' => 135_000]);

        $response = $this->post(route('boats.book', $operator), $this->traveller(['schedule_id' => $schedule->id]));

        $booking = Booking::query()->sole();

        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertSame(2 * 180_000 + 135_000, $booking->total);
        $this->assertSame(BookingStatus::Pending, $booking->status);
        $this->assertTrue($booking->bookable->is($schedule));
        $this->assertStringStartsWith('PG-', $booking->reference);

        Notification::assertSentOnDemand(BookingReceived::class, fn (BookingReceived $n, array $channels, $notifiable) => $notifiable->routes['mail'] === ['ayu@example.com' => 'Ayu Lestari']);

        $this->get(route('bookings.show', $booking))->assertOk()->assertSee($booking->reference)->assertSee('IDR 495.000');
    }

    public function test_booking_without_an_email_is_accepted_and_sends_no_mail(): void
    {
        Notification::fake();

        $operator = BoatOperator::factory()->create();
        $schedule = Schedule::factory()->for($operator, 'operator')->create();

        // The order form no longer asks for an email address.
        $payload = $this->traveller(['schedule_id' => $schedule->id]);
        unset($payload['email']);

        $this->post(route('boats.book', $operator), $payload)->assertSessionHasNoErrors();

        $booking = Booking::query()->sole();
        $this->assertNull($booking->customer_email);
        Notification::assertNothingSent();

        $this->get(route('bookings.show', $booking))->assertOk()->assertSee('+62 81234567890');
    }

    public function test_boat_booking_rejects_a_schedule_from_another_operator(): void
    {
        $operator = BoatOperator::factory()->create();
        $foreign = Schedule::factory()->create();

        $this->from(route('boats.order', $operator))
            ->post(route('boats.book', $operator), $this->traveller(['schedule_id' => $foreign->id]))
            ->assertRedirect(route('boats.order', $operator))
            ->assertSessionHasErrors('schedule_id');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_boat_booking_validates_traveller_details(): void
    {
        $operator = BoatOperator::factory()->create();
        $schedule = Schedule::factory()->for($operator, 'operator')->create();

        $this->post(route('boats.book', $operator), [
            'schedule_id' => $schedule->id,
            'full_name' => '',
            'email' => 'not-an-email',
            'nationality' => 'Mars',
            'phone' => 'abc',
            'notes' => '<script>alert(1)</script>',
            'adults' => 0,
            'children' => 0,
            'travel_date' => now()->subDay()->toDateString(),
        ])->assertSessionHasErrors(['full_name', 'email', 'nationality', 'phone', 'notes', 'adults', 'travel_date']);

        $this->post(route('boats.book', $operator), $this->traveller(['schedule_id' => $schedule->id, 'full_name' => 'Al 9']))
            ->assertSessionHasErrors('full_name');
    }

    public function test_hotel_total_multiplies_by_number_of_rooms(): void
    {
        Notification::fake();

        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['price_per_night' => 1_000_000, 'stock' => 2]);

        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id,
            'travel_date' => '2030-05-10',
            'check_out' => '2030-05-12',
            'rooms' => 2,
        ]))->assertRedirect();

        $booking = Booking::query()->sole();
        $this->assertSame(2, $booking->rooms);
        $this->assertSame(4_000_000, $booking->total);
    }

    public function test_guest_can_book_a_hotel_room_for_several_nights(): void
    {
        Notification::fake();

        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['price_per_night' => 2_500_000]);

        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id,
            'travel_date' => '2030-05-10',
            'check_out' => '2030-05-12',
            'rooms' => 1,
        ]))->assertRedirect();

        $booking = Booking::query()->sole();
        $this->assertSame(2, $booking->nights);
        $this->assertSame(5_000_000, $booking->total);
        $this->assertSame('2030-05-12', $booking->check_out->toDateString());
    }

    public function test_hotel_booking_adds_the_rooms_a_large_party_needs(): void
    {
        Notification::fake();

        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['stock' => 5]);

        // The order form no longer shows a rooms stepper: five guests book two rooms on their own.
        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id,
            'travel_date' => '2030-05-10',
            'check_out' => '2030-05-12',
            'adults' => 5,
            'children' => 0,
            'rooms' => 1,
        ]))->assertSessionHasNoErrors();

        $this->assertSame(2, Booking::query()->sole()->rooms);
    }

    public function test_hotel_booking_cannot_exceed_the_units_still_free_for_the_dates(): void
    {
        Notification::fake();

        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['stock' => 2]);
        $stay = ['room_id' => $room->id, 'travel_date' => '2030-05-10', 'check_out' => '2030-05-12', 'rooms' => 1];

        // A cancelled overlapping stay frees its unit; a live one does not.
        $existing = ['travel_date' => '2030-05-10', 'check_out' => '2030-05-12', 'rooms' => 1];
        Booking::factory()->for($room, 'bookable')->create($existing + ['status' => BookingStatus::Cancelled]);
        Booking::factory()->for($room, 'bookable')->create($existing + ['status' => BookingStatus::Pending]);

        $this->post(route('hotels.book', $hotel), $this->traveller(['rooms' => 2] + $stay))
            ->assertSessionHasErrors(['rooms' => 'Only 1 unit is left for the selected dates.']);

        $this->post(route('hotels.book', $hotel), $this->traveller($stay))->assertRedirect();

        // The type is now full for any night that touches 10–12 May, but free right after check-out.
        $this->post(route('hotels.book', $hotel), $this->traveller(['travel_date' => '2030-05-11', 'check_out' => '2030-05-13'] + $stay))
            ->assertSessionHasErrors(['rooms' => 'This room type is fully booked for the selected dates.']);

        $this->post(route('hotels.book', $hotel), $this->traveller(['travel_date' => '2030-05-12', 'check_out' => '2030-05-14'] + $stay))
            ->assertRedirect();
    }

    public function test_hotel_stays_are_capped_at_thirty_nights(): void
    {
        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create();

        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id, 'travel_date' => '2030-05-01', 'check_out' => '2030-06-15', 'rooms' => 1,
        ]))->assertSessionHasErrors('check_out');
    }

    public function test_inactive_hotels_cannot_be_ordered_or_booked(): void
    {
        $hotel = Hotel::factory()->create(['status' => ListingStatus::Inactive]);
        $room = HotelRoom::factory()->for($hotel)->create();

        $this->get(route('hotels.order', $hotel))->assertNotFound();
        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id, 'travel_date' => '2030-05-10', 'check_out' => '2030-05-12', 'rooms' => 1,
        ]))->assertNotFound();

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_sidebar_guest_option_carries_its_room_count_to_the_order_page(): void
    {
        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create(['price_per_night' => 1_000_000, 'stock' => 3]);

        $this->get(route('hotels.order', [$hotel, 'room' => $room->id, 'guests' => '4-2', 'check_in' => '2030-05-10', 'check_out' => '2030-05-11']))
            ->assertOk()
            ->assertSee('name="rooms"', false)
            ->assertSee('value="2"', false)
            ->assertSee('IDR 2.000.000');
    }

    public function test_order_page_defaults_to_the_first_listed_room(): void
    {
        $hotel = Hotel::factory()->create();
        HotelRoom::factory()->for($hotel)->create(['name' => 'Villa', 'sort_order' => 1, 'price_per_night' => 5_000_000]);
        $deluxe = HotelRoom::factory()->for($hotel)->create(['name' => 'Deluxe', 'sort_order' => 0, 'price_per_night' => 2_000_000]);

        $this->get(route('hotels.order', $hotel))
            ->assertOk()
            ->assertSee('name="room_id" value="'.$deluxe->id.'"', false);
    }

    public function test_hotel_booking_requires_checkout_after_checkin(): void
    {
        $hotel = Hotel::factory()->create();
        $room = HotelRoom::factory()->for($hotel)->create();

        $this->post(route('hotels.book', $hotel), $this->traveller([
            'room_id' => $room->id,
            'travel_date' => '2030-05-10',
            'check_out' => '2030-05-10',
            'rooms' => 1,
        ]))->assertSessionHasErrors('check_out');
    }

    public function test_guest_can_book_an_activity(): void
    {
        Notification::fake();

        $activity = Activity::factory()->create(['price_adult' => 75_000, 'price_child' => 50_000]);

        $this->post(route('activities.book', $activity), $this->traveller(['adults' => 1, 'children' => 2]))->assertRedirect();

        $this->assertSame(175_000, Booking::query()->sole()->total);
    }

    public function test_unknown_booking_reference_is_not_found(): void
    {
        $this->get(route('bookings.show', 'PG-NOPE00'))->assertNotFound();
    }
}
