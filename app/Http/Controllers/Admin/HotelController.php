<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\ListingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelRequest;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Support\RichText;
use App\Support\Uploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HotelController extends Controller
{
    /** The eight amenity cards of Figma 1:7714; `icon` is the public detail-page glyph, `editorIcon` the console one. */
    public const AMENITIES = [
        ['label' => 'Free High-Speed Wi-Fi', 'shortLabel' => 'Free Wi-Fi', 'icon' => 'wifi.svg', 'editorIcon' => 'amenity-wifi.svg', 'note' => 'Starlink Mesh'],
        ['label' => 'Oceanfront Infinity Pool', 'shortLabel' => 'Infinity Pool', 'icon' => 'pool.svg', 'editorIcon' => 'amenity-pool.svg', 'note' => 'Panoramic view'],
        ['label' => 'Full-Service Spa', 'shortLabel' => 'Luxury Spa', 'icon' => 'spa.svg', 'editorIcon' => 'amenity-spa.svg', 'note' => 'Balinese therapy'],
        ['label' => 'Sunset Cliff Bar', 'shortLabel' => 'Sunset Bar', 'icon' => 'bar.svg', 'editorIcon' => 'amenity-bar.svg', 'note' => 'Signature cocktails'],
        ['label' => 'Oceanfront Restaurant', 'shortLabel' => 'Fine Dining', 'icon' => 'restaurant.svg', 'editorIcon' => 'amenity-restaurant.svg', 'note' => 'Fresh seafood dining'],
        ['label' => '24/7 Butler Service', 'shortLabel' => 'Butler', 'icon' => 'butler.svg', 'editorIcon' => 'amenity-butler.svg', 'note' => 'VIP guest assistance'],
        ['label' => 'Airport/Harbor Shuttle', 'shortLabel' => 'Shuttle', 'icon' => 'shuttle.svg', 'editorIcon' => 'amenity-shuttle.svg', 'note' => 'Included for boats'],
        ['label' => 'Air Conditioning', 'shortLabel' => 'AC', 'icon' => 'ac.svg', 'editorIcon' => 'amenity-ac.svg', 'note' => 'Climate controlled'],
    ];

    /** Destination pill options (Figma 1:9314); matched against the hotel address. */
    public const DESTINATIONS = ['Nusa Penida', 'Nusa Lembongan', 'Sanur', 'Bali', 'Gili'];

    /** Hotel listing — Figma node 1:9280. */
    public function index(Request $request): View
    {
        $hotels = Hotel::query()
            ->with('rooms')
            ->withCount(['rooms'])
            ->withSum('rooms as room_stock', 'stock')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$request->string('q').'%')
                ->orWhere('address', 'like', '%'.$request->string('q').'%')
                ->orWhere('partner_label', 'like', '%'.$request->string('q').'%')))
            ->when($request->filled('destination'), fn ($q) => $q->where('address', 'like', '%'.$request->string('destination').'%'))
            ->when($request->filled('stars'), fn ($q) => $q->where('stars', $request->integer('stars')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')->value()))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // "Bookings (Mo)": stays booked this month per property, with units against the room stock.
        $monthlyBookings = Booking::query()
            ->where('bookable_type', (new HotelRoom)->getMorphClass())
            ->where('bookings.status', '!=', BookingStatus::Cancelled)
            ->whereBetween('bookings.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->join('hotel_rooms', 'hotel_rooms.id', '=', 'bookings.bookable_id')
            ->groupBy('hotel_rooms.hotel_id')
            ->selectRaw('hotel_rooms.hotel_id, count(*) as stays, sum(bookings.rooms) as units')
            ->get()
            ->keyBy('hotel_id');

        return view('admin.hotels', [
            'hotels' => $hotels,
            'filters' => $request->only(['q', 'destination', 'stars', 'status']),
            'destinations' => self::DESTINATIONS,
            'monthlyBookings' => $monthlyBookings,
            'activeCount' => Hotel::query()->active()->count(),
            'totalCount' => Hotel::query()->count(),
        ]);
    }

    /** Add New Hotel — Figma node 1:7501. */
    public function create(): View
    {
        return $this->form(new Hotel(['status' => ListingStatus::Active, 'stars' => 5, 'category' => 'Resort']));
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $hotel = DB::transaction(function () use ($request) {
            $hotel = Hotel::query()->create($this->payload($request));
            $this->syncRooms($hotel, $request->input('rooms', []));

            return $hotel;
        });

        return redirect()->route('admin.hotels')->with('flash', "{$hotel->name} listed.");
    }

    public function edit(Hotel $hotel): View
    {
        return $this->form($hotel->load('rooms'));
    }

    public function update(StoreHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        DB::transaction(function () use ($request, $hotel): void {
            $hotel->update($this->payload($request, $hotel));
            $this->syncRooms($hotel, $request->input('rooms', []));
        });

        return redirect()->route('admin.hotels')->with('flash', "{$hotel->name} updated.");
    }

    public function destroy(Hotel $hotel): RedirectResponse
    {
        $hotel->delete();

        return redirect()->route('admin.hotels')->with('flash', "{$hotel->name} removed.");
    }

    private function form(Hotel $hotel): View
    {
        $selected = old('amenities', collect($hotel->amenities ?? [])->pluck('label')->all());

        return view('admin.hotels-create', [
            'hotel' => $hotel,
            'rooms' => old('rooms', $hotel->rooms->map->only(['id', 'name', 'guests', 'bed', 'size_label', 'price_per_night', 'stock'])->all()),
            'amenities' => collect(self::AMENITIES)->map(fn ($a) => $a + ['checked' => in_array($a['label'], $selected, true)])->all(),
            'categories' => ['Luxury Resort', 'Resort', 'Hotel', 'Villa', 'Boutique'],
            'regions' => StoreHotelRequest::REGIONS,
            'publishModes' => [
                ['value' => 'publish', 'label' => 'Publish Immediately', 'description' => 'Live to all passenger channels right away'],
                ['value' => 'draft', 'label' => 'Save as Draft', 'description' => 'Internal review without public URL'],
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(StoreHotelRequest $request, ?Hotel $existing = null): array
    {
        $data = $request->safe()->except(['cover', 'gallery', 'rooms', 'amenities', 'submit_as', 'publish', 'remove_photos']);
        $picked = $request->input('amenities', []);
        $data['amenities'] = array_values(array_filter(self::AMENITIES, fn ($a) => in_array($a['label'], $picked, true)));
        // Only the editor's own tags survive, so the description is safe to print unescaped.
        $data['description'] = RichText::clean($data['description']);

        if ($cover = Uploads::store($request->file('cover'), 'hotels')) {
            $data['image'] = $cover;
        } elseif (! $existing) {
            $data['image'] = 'nusa-penida-resort.png';
        }

        // Uploads are added to the gallery; photos only disappear when they were ticked for removal.
        $dropped = $request->input('remove_photos', []);
        $kept = collect($existing?->gallery ?? [])
            ->reject(fn (array $photo) => in_array($photo['image'], $dropped, true))
            ->values()
            ->all();

        $gallery = [...$kept, ...Uploads::gallery($request->file('gallery'), 'hotels', $request->string('name')->value())];

        if ($gallery !== [] || $dropped !== []) {
            $data['gallery'] = $gallery;
        }

        return $data;
    }

    /**
     * Upsert the submitted room rows and drop the ones the admin removed.
     *
     * @param  array<int, array<string, mixed>>  $rooms
     */
    private function syncRooms(Hotel $hotel, array $rooms): void
    {
        $keep = [];

        foreach ($rooms as $i => $room) {
            $attributes = collect($room)->only(['name', 'guests', 'bed', 'size_label', 'price_per_night', 'stock'])->all() + ['sort_order' => $i];
            $model = ! empty($room['id']) ? $hotel->rooms()->whereKey($room['id'])->first() : null;

            $keep[] = $model ? tap($model)->update($attributes)->id : $hotel->rooms()->create($attributes)->id;
        }

        $hotel->rooms()->whereKeyNot($keep)->delete();
    }
}
