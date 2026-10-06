<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\BoatOperator;
use App\Models\Port;
use Illuminate\Database\Seeder;

class BoatOperatorSeeder extends Seeder
{
    public function run(): void
    {
        $ports = Port::query()->pluck('id', 'name');

        $facilities = [
            ['icon' => 'air-conditioning.svg', 'label' => 'Air Conditioning'],
            ['icon' => 'toilet.svg', 'label' => 'Toilet'],
            ['icon' => 'life-jackets.svg', 'label' => 'Life Jackets'],
            ['icon' => 'insurance.svg', 'label' => 'Insurance'],
        ];

        // Only Maruti has studio shots on disk; the others fall back to their own photo
        // through BoatOperator::galleryPhotos().
        $galleries = [
            'Maruti Fast Boat' => [
                ['image' => 'maruti-side.png', 'alt' => 'Side view'],
                ['image' => 'maruti-front.png', 'alt' => 'Front view'],
                ['image' => 'maruti-deck.png', 'alt' => 'Deck view'],
            ],
        ];

        $operators = [
            [
                'name' => 'Maruti Fast Boat',
                'description' => 'Cruise along the iconic Monaco Riviera and experience world-class service.',
                'tagline' => 'Cruise along the iconic Monaco Riviera and experience world-class maritime travel.',
                'rating' => 4.8, 'review_count' => 124, 'image' => 'boat-maruti.png',
                'top_speed_knots' => 35, 'capacity' => 100,
                'vessels' => [
                    ['name' => 'Sanjaya Ocean Queen', 'code' => 'SFB-001', 'type' => 'Catamaran Fast Ferry', 'capacity' => 150, 'top_speed_knots' => 24, 'inspected_at' => '2023-10-12'],
                    ['name' => 'Sanjaya Express II', 'code' => 'SFB-002', 'type' => 'Mono-hull Fastboat', 'capacity' => 85, 'top_speed_knots' => 28, 'inspected_at' => '2023-11-01'],
                ],
                'routes' => [
                    ['Sanur', 'Nusa Penida', '08:00', '08:45', 100_000],
                    ['Nusa Penida', 'Sanur', '16:00', '16:45', 100_000],
                    ['Sanur', 'Gili Trawangan', '09:00', '11:30', 350_000],
                ],
            ],
            [
                'name' => 'Semabu Hill Fast Boat',
                'description' => 'Sail through the breathtaking Amalfi Coast and indulge in exquisite comfort.',
                'tagline' => 'Sail through the breathtaking Amalfi Coast and indulge in exquisite comfort.',
                'rating' => 4.7, 'review_count' => 98, 'image' => 'boat-semabu.png',
                'top_speed_knots' => 30, 'capacity' => 80,
                'vessels' => [
                    ['name' => 'Sanjaya Explorer', 'code' => 'SFB-005', 'type' => 'Luxury Catamaran', 'capacity' => 120, 'top_speed_knots' => 30, 'status' => ListingStatus::Inactive, 'inspected_at' => '2023-09-28'],
                    ['name' => 'Semabu Voyager', 'code' => 'SFB-006', 'type' => 'Mono-hull Fastboat', 'capacity' => 80, 'top_speed_knots' => 28, 'inspected_at' => '2024-01-15'],
                ],
                'routes' => [
                    ['Sanur', 'Nusa Penida', '09:30', '10:15', 100_000],
                    ['Nusa Penida', 'Sanur', '15:00', '15:45', 100_000],
                ],
            ],
            [
                'name' => 'Angel Billabong Fast Cruise',
                'description' => 'Explore the stunning Whitsunday Islands and enjoy unparalleled views.',
                'tagline' => 'Explore the stunning Whitsunday Islands and enjoy unparalleled views.',
                'rating' => 4.9, 'review_count' => 210, 'image' => 'boat-angel.png',
                'top_speed_knots' => 32, 'capacity' => 120,
                'vessels' => [
                    ['name' => 'Angel Billabong I', 'code' => 'SFB-010', 'type' => 'Catamaran Fast Ferry', 'capacity' => 120, 'top_speed_knots' => 32, 'inspected_at' => '2024-02-20'],
                ],
                'routes' => [
                    ['Kusamba', 'Nusa Penida', '07:30', '08:00', 90_000],
                    ['Nusa Penida', 'Kusamba', '14:30', '15:00', 90_000],
                    ['Padang Bai', 'Gili Trawangan', '09:00', '10:30', 400_000],
                ],
            ],
        ];

        foreach ($operators as $data) {
            $vessels = $data['vessels'];
            $routes = $data['routes'];
            unset($data['vessels'], $data['routes']);

            $operator = BoatOperator::query()->updateOrCreate(
                ['name' => $data['name']],
                $data + ['hero_image' => $data['image'], 'facilities' => $facilities, 'gallery' => $galleries[$data['name']] ?? []],
            );

            $vesselIds = [];
            foreach ($vessels as $vessel) {
                $vesselIds[] = $operator->vessels()->updateOrCreate(['code' => $vessel['code']], $vessel)->id;
            }

            foreach ($routes as $i => [$from, $to, $depart, $arrive, $price]) {
                $operator->schedules()->updateOrCreate(
                    ['from_port_id' => $ports[$from], 'to_port_id' => $ports[$to], 'departure_time' => $depart],
                    [
                        'vessel_id' => $vesselIds[$i % count($vesselIds)],
                        'arrival_time' => $arrive,
                        'price_adult' => $price,
                        'price_child' => (int) round($price * 0.75),
                        'price_foreign' => (int) round($price * 1.8),
                        'price_foreign_child' => (int) round($price * 1.8 * 0.75),
                        'days' => null,
                        'status' => ListingStatus::Active,
                    ],
                );
            }

            if ($operator->reviews()->doesntExist()) {
                $operator->reviews()->createMany([
                    ['name' => 'Sarah Jenkins', 'stars' => 5, 'quote' => '"Incredibly smooth ride and the staff was extremely helpful with our luggage. Highly recommend for trips to Nusa Penida!"', 'experienced_at' => '2023-10-15'],
                    ['name' => 'Mark D.', 'stars' => 4, 'quote' => '"Fast and comfortable. The AC worked perfectly which was a lifesaver in the heat. Will book again."', 'experienced_at' => '2023-09-20'],
                ]);
            }
        }
    }
}
