<?php

namespace Tests\Unit;

use App\Models\Activity;
use App\Models\HotelRoom;
use App\Models\Schedule;
use App\Support\BookingQuote;
use App\Support\Money;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookingQuoteTest extends TestCase
{
    public function test_schedule_quote_multiplies_adults_and_children(): void
    {
        $schedule = new Schedule(['price_adult' => 180_000, 'price_child' => 135_000]);

        $quote = BookingQuote::forSchedule($schedule, Carbon::parse('2030-01-01'), 2, 1);

        $this->assertSame(495_000, $quote->total());
        $this->assertSame([
            ['label' => '2 Adults', 'amount' => 'IDR 360.000', 'kind' => 'adult'],
            ['label' => '1 Child', 'amount' => 'IDR 135.000', 'kind' => 'child', 'hidden' => false],
        ], $quote->lines());

        // The child row stays in the breakdown, hidden, so the summary card can reveal it live.
        $adultsOnly = BookingQuote::forSchedule($schedule, Carbon::parse('2030-01-01'), 2, 0);

        $this->assertSame(['label' => '0 Child', 'amount' => 'IDR 0', 'kind' => 'child', 'hidden' => true], $adultsOnly->lines()[1]);
    }

    public function test_room_quote_charges_per_night_and_never_below_one_night(): void
    {
        $room = new HotelRoom(['price_per_night' => 1_000_000]);

        $three = BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-04'), 2, 2);
        $same = BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-01'), 2);

        $this->assertSame(3, $three->nights);
        $this->assertSame(3_000_000, $three->total());
        $this->assertSame(6_000_000, BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-04'), 2, 0, 2)->total());
        $this->assertSame(1, $same->nights);
    }

    public function test_room_quote_surcharges_adults_beyond_the_included_allowance(): void
    {
        config(['penida.booking.hotel_included_adults' => 2, 'penida.booking.hotel_extra_adult_price' => 180_000]);
        $room = new HotelRoom(['price_per_night' => 1_000_000]);

        $two = BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-03'), 2);
        $three = BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-03'), 3);
        $fiveInTwoRooms = BookingQuote::forRoom($room, Carbon::parse('2030-01-01'), Carbon::parse('2030-01-03'), 5, 0, 2);

        $this->assertSame(2_000_000, $two->total());
        $this->assertSame(2_180_000, $three->total());
        $this->assertSame('1 Extra Adult x IDR 180.000', $three->lines()[1]['label']);
        $this->assertSame(4_180_000, $fiveInTwoRooms->total());
        $this->assertSame('Included in room rate', $two->party()[0]['price']);
        $this->assertSame('+Rp 180.000 / extra adult', $three->party()[0]['price']);
    }

    public function test_activity_quote_clamps_party_size(): void
    {
        $activity = new Activity(['price_adult' => 75_000, 'price_child' => 50_000]);

        $quote = BookingQuote::forActivity($activity, Carbon::parse('2030-01-01'), 0, -3);

        $this->assertSame(1, $quote->adults);
        $this->assertSame(0, $quote->children);
        $this->assertSame(75_000, $quote->total());
    }

    public function test_money_formats_rupiah(): void
    {
        $this->assertSame('IDR 2.500.000', Money::idr(2_500_000));
        $this->assertSame('Rp. 100.000', Money::idr(100_000, 'Rp.'));
        $this->assertSame('IDR 2.5M', Money::compact(2_500_000));
        $this->assertSame('IDR 180K', Money::compact(180_000));
    }
}
