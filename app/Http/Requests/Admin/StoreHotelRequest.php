<?php

namespace App\Http\Requests\Admin;

use App\Enums\ListingStatus;
use App\Support\BookingOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRequest extends FormRequest
{
    /** Island / Region options (Figma 1:7904). */
    public const REGIONS = ['Nusa Penida', 'Nusa Lembongan', 'Nusa Ceningan', 'Sanur, Bali', 'Gili Trawangan', 'Gili Air', 'Lombok'];

    protected function prepareForValidation(): void
    {
        // Rooms with an empty name are unfilled editor slots or removed cards.
        // Each room's photo arrives in the file bag; carry it alongside the typed values so it
        // survives the filtering below and is validated with the rest of the row.
        $files = $this->file('rooms', []);

        $rooms = collect($this->input('rooms', []))
            ->map(fn ($room, $i) => $room + ['photo' => $files[$i]['photo'] ?? null])
            ->filter(fn ($room) => filled($room['name'] ?? null))
            ->map(fn ($room) => array_merge($room, ['price_per_night' => (int) preg_replace('/\D+/', '', (string) ($room['price_per_night'] ?? 0))]))
            ->values()
            ->all();

        // Publishing Settings: "Save as Draft" (radio or the header button) parks the listing
        // in review; "Publish Immediately" makes it active.
        $draft = $this->input('submit_as') === 'draft' || $this->input('publish') === 'draft';

        $this->merge([
            'rooms' => $rooms,
            'status' => $draft ? ListingStatus::Draft->value : ListingStatus::Active->value,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'max:40'],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
            'description' => ['required', 'string', 'max:3000'],
            'region' => ['nullable', 'string', 'max:80'],
            'address' => ['required', 'string', 'max:160'],
            'full_address' => ['nullable', 'string', 'max:255'],
            'harbor_distance' => ['nullable', 'string', 'max:120'],
            'coordinates' => ['nullable', 'string', 'max:60'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:60'],
            'status' => ['required', Rule::enum(ListingStatus::class)],
            'publish' => ['nullable', Rule::in(['publish', 'draft'])],
            'submit_as' => ['nullable', Rule::in(['draft', 'publish'])],
            'cover' => ['nullable', 'image', 'max:12288'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'max:12288'],
            'remove_photos' => ['nullable', 'array'],
            'remove_photos.*' => ['string'],
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.id' => ['nullable', 'integer'],
            'rooms.*.name' => ['required', 'string', 'max:120'],
            // Guests never exceed the online booking cap, or a room could advertise capacity nobody can book.
            'rooms.*.guests' => ['required', 'integer', 'min:1', 'max:'.BookingOptions::GUESTS_PER_ROOM],
            'rooms.*.bed' => ['nullable', 'string', 'max:60'],
            'rooms.*.size_label' => ['nullable', 'string', 'max:60'],
            'rooms.*.price_per_night' => ['required', 'integer', 'min:0'],
            'rooms.*.stock' => ['required', 'integer', 'min:0', 'max:500'],
            // Room photo: a new upload, or the path already on file when nothing is chosen.
            'rooms.*.photo' => ['nullable', 'image', 'max:12288'],
            'rooms.*.image' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'rooms.required' => 'Add at least one room category with a nightly rate.',
            'rooms.*.guests.max' => 'Rooms hold at most '.BookingOptions::GUESTS_PER_ROOM.' guests online; extra adults are charged as a surcharge.',
        ];
    }
}
