<?php

namespace App\Actions;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\HotelRoom;
use App\Notifications\BookingReceived;
use App\Support\BookingQuote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CreateBooking
{
    /**
     * Persist a reservation from a priced quote and the traveller details.
     *
     * @param  array{full_name: string, email: string, dial_code: string, phone: string, nationality: string, notes?: string|null}  $traveller
     */
    public function handle(BookingQuote $quote, array $traveller): Booking
    {
        $booking = DB::transaction(fn () => Booking::query()->create([
            'bookable_type' => $quote->bookable->getMorphClass(),
            'bookable_id' => $quote->bookable->getKey(),
            'customer_name' => $traveller['full_name'],
            'customer_email' => $traveller['email'],
            'dial_code' => $traveller['dial_code'],
            'phone' => $traveller['phone'],
            'nationality' => $traveller['nationality'],
            'travel_date' => $quote->date->toDateString(),
            'check_out' => $quote->checkOut?->toDateString(),
            'adults' => $quote->adults,
            'children' => $quote->children,
            'nights' => $quote->nights,
            'rooms' => $quote->rooms,
            'unit_price_adult' => $quote->unitAdult,
            'unit_price_child' => $quote->bookable instanceof HotelRoom ? $quote->extraAdultPrice : $quote->unitChild,
            'total' => $quote->total(),
            'status' => BookingStatus::Pending,
            'payment_status' => PaymentStatus::Unpaid,
            'notes' => $traveller['notes'] ?? null,
        ]));

        // The order form no longer asks for an email, so only notify when one is on file.
        if (filled($booking->customer_email)) {
            Notification::route('mail', [$booking->customer_email => $booking->customer_name])
                ->notify(new BookingReceived($booking));
        }

        return $booking;
    }
}
