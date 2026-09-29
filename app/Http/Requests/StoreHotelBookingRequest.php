<?php

namespace App\Http\Requests;

use App\Models\HotelRoom;
use App\Support\BookingOptions;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHotelBookingRequest extends StoreBookingRequest
{
    /** Longest stay a guest may book online; longer stays go through the operator. */
    public const MAX_NIGHTS = 30;

    /** Rooms are no longer picked by hand: the party size decides how many units are needed. */
    protected function prepareForValidation(): void
    {
        $people = $this->integer('adults') + $this->integer('children');
        $needed = max(1, (int) ceil($people / BookingOptions::GUESTS_PER_ROOM));

        $this->merge(['rooms' => max($needed, $this->integer('rooms'))]);
    }

    public function rules(): array
    {
        return parent::rules() + [
            'room_id' => ['required', Rule::exists('hotel_rooms', 'id')->where('hotel_id', $this->route('hotel')->id)],
            'check_out' => ['required', 'date', 'after:travel_date'],
            'rooms' => ['required', 'integer', 'min:1', 'max:10'],
        ];
    }

    /**
     * Cross-field checks that need the validated room: guests must fit the
     * rooms, the stay must be reasonable, and the units must still be free.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $checkIn = $this->date('travel_date');
                $checkOut = $this->date('check_out');
                $nights = (int) $checkIn->startOfDay()->diffInDays($checkOut->startOfDay());

                if ($nights > self::MAX_NIGHTS) {
                    $validator->errors()->add('check_out', 'Stays longer than '.self::MAX_NIGHTS.' nights must be arranged directly with the hotel.');

                    return;
                }

                $available = $this->room()->unitsAvailableBetween($checkIn, $checkOut);

                if ($this->integer('rooms') > $available) {
                    $validator->errors()->add('rooms', $available === 0
                        ? 'This room type is fully booked for the selected dates.'
                        : "Only {$available} ".($available === 1 ? 'unit is' : 'units are').' left for the selected dates.');
                }
            },
        ];
    }

    public function room(): HotelRoom
    {
        return HotelRoom::query()->with('hotel')->findOrFail($this->integer('room_id'));
    }
}
