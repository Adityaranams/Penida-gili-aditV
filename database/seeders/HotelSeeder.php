<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Http\Controllers\Admin\HotelController;
use App\Models\Hotel;
use Database\Seeders\Concerns\PicksDemoPhotos;
use Illuminate\Database\Seeder;

/**
 * Ten sample hotels with their room types, filled the way the console form
 * fills them so the listing, detail page and booking flow all work.
 */
class HotelSeeder extends Seeder
{
    use PicksDemoPhotos;

    /**
     * Covers resolve against public/images/hotels, while gallery and room
     * photos resolve against public/images/hotels/detail — so the two lists
     * hold bare filenames from their own folder, never a shared path.
     */
    private const COVERS = ['nusa-penida-resort.png', 'meru-resort.png', 'grand-hyatt-resort.png'];

    private const GALLERY = ['pool-main.png', 'sunset-dining.png', 'suite.png', 'bedroom.png', 'room-villa.png', 'cocktail.png', 'room-deluxe.png'];

    private const ROOM_PHOTOS = ['room-deluxe.png', 'room-villa.png', 'suite.png', 'bedroom.png'];

    public function run(): void
    {
        foreach ($this->hotels() as $i => $data) {
            $rooms = $data['rooms'];
            $amenities = $this->amenities($data['amenity_labels']);
            unset($data['rooms'], $data['amenity_labels']);

            $hotel = Hotel::query()->updateOrCreate(['name' => $data['name']], $data + [
                'image' => self::COVERS[$i % count(self::COVERS)],
                'gallery' => $this->galleryFor(self::GALLERY, $i * 3, $data['name'], 3),
                'amenities' => $amenities,
                'status' => ListingStatus::Active,
            ]);

            foreach ($rooms as $order => $room) {
                $hotel->rooms()->updateOrCreate(
                    ['name' => $room['name']],
                    $room + ['sort_order' => $order, 'image' => self::ROOM_PHOTOS[($i + $order) % count(self::ROOM_PHOTOS)]],
                );
            }
        }

        $this->command?->info('Seeded '.count($this->hotels()).' hotels with their rooms.');
    }

    /**
     * Amenity rows keep the icon metadata the detail page draws, exactly as
     * HotelController stores them when the admin ticks the boxes.
     *
     * @param  list<string>  $labels
     * @return list<array<string, string>>
     */
    private function amenities(array $labels): array
    {
        return array_values(array_filter(
            HotelController::AMENITIES,
            fn (array $amenity) => in_array($amenity['label'], $labels, true),
        ));
    }

    /** @return list<array<string, mixed>> */
    private function hotels(): array
    {
        $wifi = 'Free High-Speed Wi-Fi';
        $pool = 'Oceanfront Infinity Pool';
        $spa = 'Full-Service Spa';
        $bar = 'Sunset Cliff Bar';
        $dining = 'Oceanfront Restaurant';
        $butler = '24/7 Butler Service';
        $shuttle = 'Airport/Harbor Shuttle';
        $ac = 'Air Conditioning';

        return [
            [
                'name' => 'Semabu Hills Hotel Nusa Penida',
                'category' => 'Resort',
                'partner_label' => 'Preferred Partner',
                'stars' => 4,
                'rating' => 4.7,
                'review_count' => 684,
                'region' => 'Nusa Penida',
                'address' => 'Ped, Nusa Penida',
                'full_address' => 'Jl. Semabu Hills, Ped, Nusa Penida, Klungkung, Bali 80771',
                'harbor_distance' => '8 minutes from Toya Pakeh Harbour',
                'coordinates' => '-8.6761, 115.4738',
                'description' => 'A hillside resort above the Toya Pakeh channel, built so every room looks west across the water to Bali. The infinity pool sits on the edge of the slope and the shuttle meets each arriving fast boat.',
                'amenity_labels' => [$wifi, $pool, $dining, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Hillside Deluxe', 'description' => 'Thirty-two square metres with a private balcony facing the strait.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '32 m²', 'price_per_night' => 1_250_000, 'stock' => 12],
                    ['name' => 'Ocean View Suite', 'description' => 'Separate living area, soaking tub and a wide terrace over the pool deck.', 'guests' => 3, 'bed' => '1 King + 1 Sofa Bed', 'size_label' => '54 m²', 'price_per_night' => 2_100_000, 'stock' => 6],
                ],
            ],
            [
                'name' => 'The Nusa Penida Resort & Spa',
                'category' => 'Luxury Resort',
                'partner_label' => 'Signature Partner',
                'stars' => 5,
                'rating' => 4.9,
                'review_count' => 1_204,
                'region' => 'Nusa Penida',
                'address' => 'Sakti, Nusa Penida',
                'full_address' => 'Jl. Raya Crystal Bay, Sakti, Nusa Penida, Klungkung, Bali 80771',
                'harbor_distance' => '15 minutes from Banjar Nyuh Harbour',
                'coordinates' => '-8.7211, 115.4512',
                'description' => 'Twenty-four pool villas spread through a coconut grove above Crystal Bay, with a spa pavilion at the back of the garden and a restaurant that opens straight onto the sand.',
                'amenity_labels' => [$wifi, $pool, $spa, $dining, $butler, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Garden Pool Villa', 'description' => 'Walled garden, private plunge pool and an outdoor shower.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '68 m²', 'price_per_night' => 3_400_000, 'stock' => 10],
                    ['name' => 'Beachfront Villa', 'description' => 'Steps from the sand, with a full-length deck and a day bed over the water.', 'guests' => 4, 'bed' => '2 Queen Beds', 'size_label' => '92 m²', 'price_per_night' => 5_200_000, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Batu Karang Lembongan Resort',
                'category' => 'Resort',
                'partner_label' => 'Preferred Partner',
                'stars' => 4,
                'rating' => 4.8,
                'review_count' => 912,
                'region' => 'Nusa Lembongan',
                'address' => 'Jungutbatu, Nusa Lembongan',
                'full_address' => 'Jl. Jungutbatu, Nusa Lembongan, Klungkung, Bali 80771',
                'harbor_distance' => '5 minutes from Jungutbatu Beach',
                'coordinates' => '-8.6802, 115.4441',
                'description' => 'Terraced villas climbing the headland between Jungutbatu and Mushroom Bay, with three pools on different levels and a bar that catches the last of the light over Mount Agung.',
                'amenity_labels' => [$wifi, $pool, $spa, $bar, $dining, $ac],
                'rooms' => [
                    ['name' => 'Terrace Studio', 'description' => 'Open-plan room with a hammock on the terrace and partial sea view.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '28 m²', 'price_per_night' => 980_000, 'stock' => 14],
                    ['name' => 'Two-Bedroom Pool Villa', 'description' => 'Family villa with a private pool and a full kitchen.', 'guests' => 5, 'bed' => '1 King + 2 Singles', 'size_label' => '110 m²', 'price_per_night' => 3_800_000, 'stock' => 3],
                ],
            ],
            [
                'name' => 'Meru Resort & Spa Sanur',
                'category' => 'Luxury Resort',
                'partner_label' => 'Signature Partner',
                'stars' => 5,
                'rating' => 4.8,
                'review_count' => 1_508,
                'region' => 'Sanur',
                'address' => 'Sanur Kaja, Denpasar',
                'full_address' => 'Jl. Danau Tamblingan No. 88, Sanur Kaja, Denpasar, Bali 80228',
                'harbor_distance' => '4 minutes from Sanur Beach Port',
                'coordinates' => '-8.6885, 115.2620',
                'description' => 'A low-rise garden resort a short walk from the boat terminal, useful for the night before an early crossing. The spa runs until ten and breakfast opens at half past five for departing guests.',
                'amenity_labels' => [$wifi, $pool, $spa, $dining, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Garden Deluxe', 'description' => 'Ground-floor room opening onto the lawn, with an early breakfast option.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '36 m²', 'price_per_night' => 1_450_000, 'stock' => 20],
                    ['name' => 'Lagoon Suite', 'description' => 'Upper-floor suite with a balcony over the lagoon pool.', 'guests' => 3, 'bed' => '1 King + 1 Day Bed', 'size_label' => '62 m²', 'price_per_night' => 2_650_000, 'stock' => 8],
                ],
            ],
            [
                'name' => 'Grand Hyatt Resort & Spa Nusa Dua',
                'category' => 'Luxury Resort',
                'partner_label' => 'Signature Partner',
                'stars' => 5,
                'rating' => 4.7,
                'review_count' => 2_341,
                'region' => 'Bali',
                'address' => 'Nusa Dua, Badung',
                'full_address' => 'Kawasan Wisata Nusa Dua BTDC, Benoa, Badung, Bali 80363',
                'harbor_distance' => '35 minutes from Sanur Beach Port',
                'coordinates' => '-8.7982, 115.2246',
                'description' => 'A large beachfront property on the Nusa Dua strip with five pools, a dedicated kids club and a shuttle that runs to the Sanur boat terminal twice each morning.',
                'amenity_labels' => [$wifi, $pool, $spa, $bar, $dining, $butler, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Resort King', 'description' => 'Classic resort room with a balcony over the gardens.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '42 m²', 'price_per_night' => 2_200_000, 'stock' => 40],
                    ['name' => 'Grand Club Suite', 'description' => 'Club lounge access, evening canapés and a corner terrace.', 'guests' => 4, 'bed' => '1 King + 1 Sofa Bed', 'size_label' => '88 m²', 'price_per_night' => 4_600_000, 'stock' => 12],
                ],
            ],
            [
                'name' => 'Coral Garden Bungalows Gili Air',
                'category' => 'Boutique',
                'partner_label' => 'Local Partner',
                'stars' => 3,
                'rating' => 4.6,
                'review_count' => 437,
                'region' => 'Gili',
                'address' => 'Gili Air, North Lombok',
                'full_address' => 'Jl. Pantai Timur, Gili Air, Pemenang, North Lombok, NTB 83352',
                'harbor_distance' => '3 minutes from Gili Air Harbour',
                'coordinates' => '-8.3570, 116.0836',
                'description' => 'Eight thatched bungalows set back from the east-facing beach, each with an open-air bathroom. No roads and no scooters, so the loudest thing at night is the water.',
                'amenity_labels' => [$wifi, $dining, $ac],
                'rooms' => [
                    ['name' => 'Beach Bungalow', 'description' => 'Timber bungalow with an open-air bathroom and a shaded porch.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '26 m²', 'price_per_night' => 720_000, 'stock' => 8],
                    ['name' => 'Family Lumbung', 'description' => 'Two-storey rice-barn style cottage sleeping four.', 'guests' => 4, 'bed' => '1 Queen + 2 Singles', 'size_label' => '48 m²', 'price_per_night' => 1_180_000, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Ombak Putih Villas Gili Trawangan',
                'category' => 'Villa',
                'partner_label' => 'Preferred Partner',
                'stars' => 4,
                'rating' => 4.7,
                'review_count' => 526,
                'region' => 'Gili',
                'address' => 'Gili Trawangan, North Lombok',
                'full_address' => 'Jl. Pantai Barat, Gili Trawangan, Pemenang, North Lombok, NTB 83352',
                'harbor_distance' => '10 minutes from Gili Trawangan Harbour',
                'coordinates' => '-8.3498, 116.0348',
                'description' => 'Six private-pool villas on the quiet west shore, far enough from the night market to sleep but close enough to walk back from dinner along the sand.',
                'amenity_labels' => [$wifi, $pool, $spa, $bar, $ac],
                'rooms' => [
                    ['name' => 'One-Bedroom Pool Villa', 'description' => 'Walled villa with a six-metre pool and an outdoor living pavilion.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '75 m²', 'price_per_night' => 2_450_000, 'stock' => 6],
                    ['name' => 'Sunset Loft', 'description' => 'Upper-level loft facing west, best used for the sunset rather than the space.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '38 m²', 'price_per_night' => 1_600_000, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Penida Cliff House',
                'category' => 'Villa',
                'partner_label' => 'Local Partner',
                'stars' => 4,
                'rating' => 4.9,
                'review_count' => 213,
                'region' => 'Nusa Penida',
                'address' => 'Bunga Mekar, Nusa Penida',
                'full_address' => 'Jl. Kelingking Beach, Bunga Mekar, Nusa Penida, Klungkung, Bali 80771',
                'harbor_distance' => '45 minutes from Toya Pakeh Harbour',
                'coordinates' => '-8.7516, 115.4723',
                'description' => 'Three glass-fronted rooms on the clifftop ten minutes from Kelingking, built for people who want to be at the viewpoint before the first boat-load of day trippers arrives.',
                'amenity_labels' => [$wifi, $pool, $dining, $ac],
                'rooms' => [
                    ['name' => 'Cliff Edge Room', 'description' => 'Floor-to-ceiling glass facing the drop, with a small private deck.', 'guests' => 2, 'bed' => '1 King Bed', 'size_label' => '40 m²', 'price_per_night' => 1_850_000, 'stock' => 3],
                    ['name' => 'Garden Room', 'description' => 'Set back from the edge, quieter and cooler through the afternoon.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '30 m²', 'price_per_night' => 1_100_000, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Lembongan Beach Club & Residence',
                'category' => 'Hotel',
                'partner_label' => 'Preferred Partner',
                'stars' => 4,
                'rating' => 4.5,
                'review_count' => 389,
                'region' => 'Nusa Lembongan',
                'address' => 'Mushroom Bay, Nusa Lembongan',
                'full_address' => 'Jl. Mushroom Bay, Nusa Lembongan, Klungkung, Bali 80771',
                'harbor_distance' => '2 minutes from Mushroom Bay Jetty',
                'coordinates' => '-8.6878, 115.4339',
                'description' => 'A beach club with rooms above it, which means an easy walk to the water and music until ten. Best for travellers who want the bay rather than an early night.',
                'amenity_labels' => [$wifi, $pool, $bar, $dining, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Club Room', 'description' => 'Compact room over the club with day-bed seating on the balcony.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '29 m²', 'price_per_night' => 890_000, 'stock' => 16],
                    ['name' => 'Bay View Residence', 'description' => 'Two-bedroom apartment with a kitchen and a wide bay-facing balcony.', 'guests' => 4, 'bed' => '2 Queen Beds', 'size_label' => '85 m²', 'price_per_night' => 2_400_000, 'stock' => 5],
                ],
            ],
            [
                'name' => 'Bangsal Harbour Inn',
                'category' => 'Hotel',
                'partner_label' => 'Local Partner',
                'stars' => 3,
                'rating' => 4.3,
                'review_count' => 164,
                'region' => 'Gili',
                'address' => 'Pemenang, North Lombok',
                'full_address' => 'Jl. Bangsal Harbour, Pemenang Barat, North Lombok, NTB 83352',
                'harbor_distance' => '2 minutes from Bangsal Harbour',
                'coordinates' => '-8.3936, 116.0632',
                'description' => 'A plain, clean place to sleep two minutes from the Bangsal jetty, aimed squarely at travellers catching a dawn crossing to the Gilis or Nusa Penida.',
                'amenity_labels' => [$wifi, $shuttle, $ac],
                'rooms' => [
                    ['name' => 'Standard Twin', 'description' => 'Two single beds, a desk and a hot shower. Breakfast from five in the morning.', 'guests' => 2, 'bed' => '2 Single Beds', 'size_label' => '22 m²', 'price_per_night' => 420_000, 'stock' => 18],
                    ['name' => 'Superior Double', 'description' => 'Slightly larger, with a window over the harbour road.', 'guests' => 2, 'bed' => '1 Queen Bed', 'size_label' => '26 m²', 'price_per_night' => 560_000, 'stock' => 10],
                ],
            ],
        ];
    }
}
