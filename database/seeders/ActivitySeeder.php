<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\Activity;
use Database\Seeders\Concerns\PicksDemoPhotos;
use Illuminate\Database\Seeder;

/**
 * Ten sample activities, every console field filled, so the listing, the
 * detail page and the order flow all have something realistic to render.
 */
class ActivitySeeder extends Seeder
{
    use PicksDemoPhotos;

    public function run(): void
    {
        $photos = $this->photoPool('activities', ['bali-farm-house.png', 'barong-kris-dance.png', 'costume-penglipuran.png']);

        foreach ($this->activities() as $i => $data) {
            // Five slots per activity so covers and galleries do not overlap.
            $slot = $i * 5;
            $cover = $this->photo($photos, $slot);

            Activity::query()->updateOrCreate(['name' => $data['name']], $data + [
                'image' => $cover,
                'summary_image' => $cover,
                'gallery' => $this->galleryFor($photos, $slot, $data['name'], 4),
                'intro' => strtok($data['description'], '.').'.',
                'summary' => $data['description'],
                'price_child' => (int) round($data['price_adult'] * 0.6 / 1000) * 1000,
                'price_was' => (int) round($data['price_adult'] / 0.8 / 1000) * 1000,
                'dual_pricing' => false,
                'price_foreign' => null,
                'days' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'instant_confirmation' => true,
                'is_public' => true,
                'status' => ListingStatus::Active,
                'publish_at' => null,
                'cancellation_policy' => 'free_24h',
                'important_notes' => 'Bring sunscreen, a hat and cash for personal spending. Pick-up windows shift by up to 20 minutes in heavy traffic.',
            ]);
        }

        $this->command?->info('Seeded '.count($this->activities()).' activities.');
    }

    /** @return list<array<string, mixed>> */
    private function activities(): array
    {
        return [
            [
                'name' => 'Kelingking Beach & West Penida Day Tour',
                'badge' => 'Best Seller',
                'category' => 'Adventure',
                'location' => 'Nusa Penida',
                'place_label' => 'Hotel pick-up, West Nusa Penida',
                'opens_at' => '07:30',
                'closes_at' => '17:00',
                'duration_label' => '8 hours',
                'description' => 'Ride the cliff road to the T-Rex headland, then drop down to Broken Beach and Angel\'s Billabong before the afternoon crowds arrive. A private driver carries you between the four western viewpoints with time to swim at Crystal Bay.',
                'experiences' => [
                    ['title' => 'Cliff-top sunrise light', 'body' => 'Leaving at half past seven puts you on the headland while the light is still soft and the stairs are almost empty.'],
                    ['title' => 'Unhurried swim stop', 'body' => 'Ninety minutes at Crystal Bay, long enough to snorkel the reef edge rather than wade in and turn around.'],
                ],
                'included' => ['Private driver and fuel', 'Bottled water', 'Entrance tickets', 'Hotel pick-up and drop-off'],
                'excluded' => ['Lunch', 'Snorkel gear rental', 'Personal expenses'],
                'price_adult' => 450_000,
                'max_daily_capacity' => 40,
                'rating' => 4.9,
                'review_count' => 412,
                'sold_count' => 1_830,
            ],
            [
                'name' => 'Manta Point Snorkeling Trip',
                'badge' => 'Popular',
                'category' => 'Water Sports',
                'location' => 'Nusa Penida',
                'place_label' => 'Toya Pakeh Harbour',
                'opens_at' => '07:00',
                'closes_at' => '13:00',
                'duration_label' => '5 hours',
                'description' => 'Four snorkel stops around the south-west coast, starting at the manta cleaning station where rays of up to four metres circle just below the surface. Guides stay in the water with every group.',
                'experiences' => [
                    ['title' => 'First boat out', 'body' => 'The seven o\'clock departure reaches Manta Point before the day trippers from Sanur, which is when the rays feed closest to the surface.'],
                    ['title' => 'Guides in the water', 'body' => 'One guide per six guests swims with the group rather than watching from the boat.'],
                ],
                'included' => ['Mask, snorkel and fins', 'Life jacket', 'In-water guide', 'Light snack and water'],
                'excluded' => ['Wetsuit rental', 'Underwater camera', 'Harbour tax'],
                'price_adult' => 375_000,
                'max_daily_capacity' => 60,
                'rating' => 4.8,
                'review_count' => 356,
                'sold_count' => 2_140,
            ],
            [
                'name' => 'Diamond Beach & East Coast Explorer',
                'badge' => 'New',
                'category' => 'Adventure',
                'location' => 'Nusa Penida',
                'place_label' => 'Hotel pick-up, East Nusa Penida',
                'opens_at' => '08:00',
                'closes_at' => '16:30',
                'duration_label' => '7 hours',
                'description' => 'The eastern cliffs in one loop: the carved staircase down to Diamond Beach, the white sand crescent at Atuh, and the thatched tree houses at Rumah Pohon looking over Pulau Seribu.',
                'experiences' => [
                    ['title' => 'Down to the sand', 'body' => 'The staircase is steep but handrailed the whole way, so you actually reach the beach instead of photographing it from above.'],
                    ['title' => 'Quiet side of the island', 'body' => 'The east coast sees a fraction of Kelingking\'s traffic, and the drive between stops is short.'],
                ],
                'included' => ['Private driver and fuel', 'Entrance tickets', 'Bottled water', 'Hotel pick-up and drop-off'],
                'excluded' => ['Lunch', 'Tips', 'Personal expenses'],
                'price_adult' => 425_000,
                'max_daily_capacity' => 35,
                'rating' => 4.7,
                'review_count' => 198,
                'sold_count' => 760,
            ],
            [
                'name' => 'Balinese Cooking Class in a Village Kitchen',
                'badge' => 'Family Friendly',
                'category' => 'Culture & Heritage',
                'location' => 'Nusa Lembongan',
                'place_label' => 'Jungutbatu village',
                'opens_at' => '09:00',
                'closes_at' => '14:00',
                'duration_label' => '4 hours',
                'description' => 'Shop the morning market with the family that teaches the class, then grind the base genep spice paste by hand and cook five dishes over a wood fire before eating what you made.',
                'experiences' => [
                    ['title' => 'Market first', 'body' => 'You pick the vegetables and fish yourself, which is where most of the lesson about Balinese cooking actually happens.'],
                    ['title' => 'Cook, then eat together', 'body' => 'The class ends at a shared table in the family compound, not with a packed lunch handed over at the gate.'],
                ],
                'included' => ['Market tour', 'All ingredients', 'Recipe booklet', 'Lunch and drinks'],
                'excluded' => ['Hotel transfer', 'Alcoholic drinks', 'Tips'],
                'price_adult' => 320_000,
                'max_daily_capacity' => 16,
                'rating' => 4.9,
                'review_count' => 141,
                'sold_count' => 520,
            ],
            [
                'name' => 'Kecak Fire Dance at Uluwatu Temple',
                'badge' => 'Sunset',
                'category' => 'Cultural Show',
                'location' => 'Bali',
                'place_label' => 'Uluwatu Temple amphitheatre',
                'opens_at' => '16:30',
                'closes_at' => '19:30',
                'duration_label' => '3 hours',
                'description' => 'Seventy men chanting in interlocking rhythm while the Ramayana plays out on a clifftop stage, timed so the fire scene lands as the sun drops into the Indian Ocean.',
                'experiences' => [
                    ['title' => 'Seats that face the sun', 'body' => 'Tickets are for the western tiers, so the performance and the sunset are in the same frame.'],
                    ['title' => 'Arrive before the gate queue', 'body' => 'Entry an hour early leaves time to walk the cliff path while it is still quiet.'],
                ],
                'included' => ['Reserved seat ticket', 'Temple entrance', 'Sarong and sash', 'English programme notes'],
                'excluded' => ['Transport to Uluwatu', 'Dinner', 'Monkey-proof bag'],
                'price_adult' => 285_000,
                'max_daily_capacity' => 80,
                'rating' => 4.6,
                'review_count' => 523,
                'sold_count' => 3_410,
            ],
            [
                'name' => 'Mangrove Kayak & Blue Lagoon Paddle',
                'badge' => 'Eco Tour',
                'category' => 'Water Sports',
                'location' => 'Nusa Lembongan',
                'place_label' => 'Mangrove Point jetty',
                'opens_at' => '07:00',
                'closes_at' => '11:00',
                'duration_label' => '3 hours',
                'description' => 'Paddle the channels behind Lembongan at high tide, when the water sits clear over the roots and the herons are still feeding, then cross to the blue lagoon for a float before the wind picks up.',
                'experiences' => [
                    ['title' => 'Tide-timed departure', 'body' => 'The start time moves with the tide table so the channels are always deep enough to paddle properly.'],
                    ['title' => 'Stable sit-on-top kayaks', 'body' => 'No experience needed; the guide takes a tow line for anyone who would rather be pulled.'],
                ],
                'included' => ['Kayak and paddle', 'Life jacket', 'Dry bag', 'Local guide'],
                'excluded' => ['Towel', 'Hotel transfer', 'Snacks'],
                'price_adult' => 265_000,
                'max_daily_capacity' => 24,
                'rating' => 4.7,
                'review_count' => 96,
                'sold_count' => 430,
            ],
            [
                'name' => 'Penglipuran Village Traditional Costume Session',
                'badge' => 'Photo Session',
                'category' => 'Photography',
                'location' => 'Bali',
                'place_label' => 'Penglipuran heritage village',
                'opens_at' => '08:00',
                'closes_at' => '16:00',
                'duration_label' => '2 hours',
                'description' => 'Dress in full Balinese ceremonial costume and walk the swept stone lane of one of the tidiest villages in Indonesia, with a photographer working the morning light between the bamboo gates.',
                'experiences' => [
                    ['title' => 'Dressed by locals', 'body' => 'The fitting is done by women from the village who tie the sash the way it is actually worn.'],
                    ['title' => 'Photos you keep', 'body' => 'Forty edited frames arrive by link within two days, with the raw files available on request.'],
                ],
                'included' => ['Costume rental', 'Hair styling', 'Professional photographer', 'Edited photo gallery'],
                'excluded' => ['Village entrance ticket', 'Transport', 'Printed album'],
                'price_adult' => 395_000,
                'max_daily_capacity' => 20,
                'rating' => 4.8,
                'review_count' => 87,
                'sold_count' => 290,
            ],
            [
                'name' => 'Nusa Penida Sunrise Trekking at Bukit Teletubbies',
                'badge' => 'Early Bird',
                'category' => 'Wildlife & Nature',
                'location' => 'Nusa Penida',
                'place_label' => 'Bukit Teletubbies trailhead',
                'opens_at' => '04:30',
                'closes_at' => '09:00',
                'duration_label' => '4 hours',
                'description' => 'A pre-dawn walk up the rolling green hills on the south-east of the island, reaching the ridge in time for first light over the Lombok Strait and breakfast laid out on the grass.',
                'experiences' => [
                    ['title' => 'Walking in the dark', 'body' => 'The guide sets a slow pace with head torches for everyone; the climb takes about fifty minutes.'],
                    ['title' => 'Breakfast at the top', 'body' => 'Coffee, fruit and jaffles are carried up and served once the sun is clear of the horizon.'],
                ],
                'included' => ['Guide', 'Head torch', 'Breakfast and coffee', 'Hotel pick-up'],
                'excluded' => ['Trekking shoes', 'Rain poncho', 'Tips'],
                'price_adult' => 310_000,
                'max_daily_capacity' => 18,
                'rating' => 4.8,
                'review_count' => 73,
                'sold_count' => 240,
            ],
            [
                'name' => 'Barong & Kris Dance Morning Performance',
                'badge' => 'Classic',
                'category' => 'Cultural Show',
                'location' => 'Bali',
                'place_label' => 'Batubulan village stage',
                'opens_at' => '09:00',
                'closes_at' => '11:00',
                'duration_label' => '2 hours',
                'description' => 'The oldest story in the Balinese repertoire, staged with a live gamelan: the lion Barong against the witch Rangda, ending with the kris dancers turning their blades on themselves under trance.',
                'experiences' => [
                    ['title' => 'A story, not a medley', 'body' => 'The full hour-long drama is performed rather than the shortened tourist cut.'],
                    ['title' => 'Close to the stage', 'body' => 'Seating is reserved in the first four rows, where the gamelan is loud enough to feel.'],
                ],
                'included' => ['Reserved seat', 'Printed synopsis', 'Welcome drink', 'Parking'],
                'excluded' => ['Transport', 'Photographs with the cast', 'Souvenir mask'],
                'price_adult' => 195_000,
                'max_daily_capacity' => 100,
                'rating' => 4.5,
                'review_count' => 264,
                'sold_count' => 1_120,
            ],
            [
                'name' => 'Gili Trawangan Sunset Horse Ride',
                'badge' => 'Sunset',
                'category' => 'Wildlife & Nature',
                'location' => 'Gili Trawangan',
                'place_label' => 'West beach stable',
                'opens_at' => '15:30',
                'closes_at' => '18:30',
                'duration_label' => '90 minutes',
                'description' => 'Ride the hard sand along the car-free west shore as the light goes gold behind Bali\'s Mount Agung, with a handler walking alongside every horse for the whole route.',
                'experiences' => [
                    ['title' => 'Walking pace, by design', 'body' => 'The ride stays at a walk the whole way, so no riding experience is needed.'],
                    ['title' => 'Horses that are rested', 'body' => 'The stable runs two rides a day per horse and keeps the afternoon slot short.'],
                ],
                'included' => ['Horse and handler', 'Helmet', 'Bottled water', 'Beach photo stop'],
                'excluded' => ['Riding boots', 'Hotel transfer', 'Gratuity'],
                'price_adult' => 350_000,
                'max_daily_capacity' => 12,
                'rating' => 4.6,
                'review_count' => 58,
                'sold_count' => 180,
            ],
        ];
    }
}
