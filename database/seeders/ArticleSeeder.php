<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Author;
use Database\Seeders\Concerns\PicksDemoPhotos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Ten sample articles with their bylines, filled the way the console editor
 * fills them: rich-text body, SEO block, hero caption and reader segment.
 */
class ArticleSeeder extends Seeder
{
    use PicksDemoPhotos;

    public function run(): void
    {
        $covers = $this->photoPool('articles', [
            'featured-fastboat.png', 'snorkeling.png', 'badung-strait.png', 'kelingking.png',
            'ferry-vs-speedboat.png', 'lembongan-packing.png', 'goa-giri-putri.png',
        ]);

        $portraits = $this->photoPool('authors');
        $authors = [];

        foreach ($this->authors() as $i => $person) {
            $authors[$person['name']] = Author::query()->updateOrCreate(
                ['name' => $person['name']],
                $person + ['photo' => $this->photo($portraits, $i)],
            );
        }

        foreach ($this->articles() as $i => $data) {
            $author = $authors[$data['author']];
            unset($data['author']);

            Article::query()->updateOrCreate(['title' => $data['title']], $data + [
                'image' => $this->photo($covers, $i),
                'hero_alt' => $data['title'],
                'author_id' => $author->id,
                'author_name' => $author->name,
                'author_role' => $author->role,
                'read_time_minutes' => max(3, (int) ceil(str_word_count(strip_tags($data['body'])) / 200)),
                'meta_title' => Str::limit($data['title'], 58, ''),
                'meta_description' => Str::limit($data['excerpt'], 155, ''),
                'is_featured' => $i === 0,
                'embed_booking_widget' => false,
                'widget_route' => null,
                'status' => ArticleStatus::Published,
                'published_at' => now()->subDays(($i + 1) * 6),
            ]);
        }

        $this->command?->info('Seeded '.count($this->articles()).' articles by '.count($this->authors()).' authors.');
    }

    /** @return list<array<string, string>> */
    private function authors(): array
    {
        return [
            [
                'name' => 'Adityarana',
                'role' => 'Senior Travel Writer & Island Specialist',
                'credential' => 'Twelve years covering the Bali–Lombok crossings',
                'bio' => 'Writes about the practical side of island travel: which harbour to use, when the strait is calm, and what a crossing actually costs once the extras are added up.',
            ],
            [
                'name' => 'Ni Luh Candra',
                'role' => 'Marine Operations Editor',
                'credential' => 'Former harbour dispatcher, Sanur Beach Port',
                'bio' => 'Spent six years scheduling departures out of Sanur before moving to writing. Covers timetables, weather windows and what happens when a sailing is cancelled.',
            ],
            [
                'name' => 'Gede Mahendra',
                'role' => 'Dive & Snorkel Correspondent',
                'credential' => 'PADI Divemaster, 900+ logged dives around Nusa Penida',
                'bio' => 'Reports on the reefs between Penida and the Gilis, with a long-standing interest in how visitor numbers are changing the manta cleaning stations.',
            ],
            [
                'name' => 'Sarah Jenkins',
                'role' => 'Travel Writer',
                'credential' => 'Contributor on Southeast Asia island routes',
                'bio' => 'Writes the packing lists, budget breakdowns and first-timer guides, mostly from the perspective of someone who has already made the mistakes.',
            ],
        ];
    }

    /** @return list<array<string, mixed>> */
    private function articles(): array
    {
        return [
            [
                'title' => 'Complete Guide to Nusa Penida Fast Boat Transfers',
                'category' => 'Fast Boat Transfers',
                'author' => 'Adityarana',
                'reader_segment' => 'First-time Island Travelers',
                'subtitle' => 'Schedules, harbours and what the crossing is really like',
                'lead' => 'Everything you need to get from Sanur to Nusa Penida without standing in the wrong queue.',
                'excerpt' => 'Which harbour to use, how early to arrive, what the luggage rules actually are, and how to read a timetable that changes with the swell.',
                'hero_caption' => 'The 07:00 departure leaving Sanur Beach Port',
                'tags' => ['#NusaPenida', '#FastBoat', '#TravelGuide'],
                'views' => 28_400,
                'body' => '<h2>Which harbour you leave from</h2><p>Almost every crossing to Nusa Penida leaves from Sanur Beach Port, the purpose-built terminal that replaced the old beach launches in 2022. Boats pull alongside a concrete pier, which means you board dry and with your luggage carried.</p><p>Arrivals on Penida split between Toya Pakeh in the north-west and Banjar Nyuh a few minutes south. Check which one your ticket names, because the drive between them is fifteen minutes and drivers wait at one or the other.</p><h2>How early to arrive</h2><p>Check-in closes thirty minutes before departure and the operators enforce it. Arriving an hour early is comfortable; arriving forty minutes early during July means queueing.</p><h3>Luggage</h3><p>One large bag plus a day pack per passenger is standard. Boards and dive gear travel for a small fee and are stowed in the hull rather than on the open deck.</p><h2>When the strait is rough</h2><p>The Badung Strait is at its flattest before ten in the morning. Afternoon crossings in the January–February wet season can be genuinely uncomfortable, and cancellations are decided by the harbour master, not the operator.</p>',
            ],
            [
                'title' => 'Top 7 Snorkeling Spots Around Nusa Penida and the Gilis',
                'category' => 'Activities',
                'author' => 'Gede Mahendra',
                'reader_segment' => 'Divers & Snorkelers',
                'subtitle' => 'Where the reefs still hold up, and when to go',
                'lead' => 'Seven stops worth the boat ride, ranked by what you are likely to actually see.',
                'excerpt' => 'Manta Point, Gamat Bay, Wall Point and four more, with the tide and season that make each one worth the trip.',
                'hero_caption' => 'Early light over the reef at Gamat Bay',
                'tags' => ['#Snorkeling', '#NusaPenida', '#GiliIslands'],
                'views' => 19_250,
                'body' => '<h2>Manta Point</h2><p>The cleaning station on the south-west corner is the only spot on this list where the animal you came for is close to guaranteed, though "close to" is doing real work in that sentence. Mantas are most reliable between April and October.</p><h2>Gamat Bay</h2><p>A short hop north, and the better choice when the swell shuts Manta Point. The coral garden starts at two metres, so this is the one place a nervous snorkeler sees as much as a confident one.</p><h2>Wall Point</h2><p>A drift along a vertical face with the current doing the work. Go with a guide who knows the timing; the pull here runs faster than it looks from the surface.</p><h3>The Gili side</h3><p>Turtle numbers around Gili Meno remain the highest of the three islands, and the shallow sand flats on the east shore mean you can snorkel straight off the beach without a boat at all.</p>',
            ],
            [
                'title' => 'Best Time of Day for Calm Waters Crossing the Badung Strait',
                'category' => 'Boat Tips',
                'author' => 'Ni Luh Candra',
                'reader_segment' => 'First-time Island Travelers',
                'subtitle' => 'Reading the tide table before you book',
                'lead' => 'The same route can be glassy at seven and unpleasant at two. The difference is predictable.',
                'excerpt' => 'How morning winds, tidal flow and the wet season combine, plus the departure windows that stay smoothest year round.',
                'hero_caption' => 'Flat water on the early run to Penida',
                'tags' => ['#BoatTips', '#BadungStrait', '#TravelPlanning'],
                'views' => 16_880,
                'body' => '<h2>Why mornings are calmer</h2><p>The sea breeze builds through the day as the land heats. By early afternoon it is often blowing fifteen knots against the tidal flow, and wind against tide is what produces the short steep chop that makes people ill.</p><h2>The windows that work</h2><p>Departures before 09:30 are the safest bet in any season. The next best slot is the late afternoon lull after 16:00, once the sea breeze has dropped but before the light goes.</p><h3>Wet season caveats</h3><p>From December to February, add the swell direction to the picture. A southerly swell wraps around Penida and affects the Banjar Nyuh approach more than Toya Pakeh.</p><h2>If you are prone to seasickness</h2><p>Sit low and central, near the middle of the hull, and stay on deck where you can see the horizon. Take the tablet forty minutes before boarding rather than when you start to feel it.</p>',
            ],
            [
                'title' => 'Kelingking and Diamond Beach in One Day: Is It Realistic?',
                'category' => 'Travel Guides',
                'author' => 'Adityarana',
                'reader_segment' => 'Returning Visitors',
                'subtitle' => 'The honest timing for a west-and-east day trip',
                'lead' => 'It can be done. Whether it should be depends on what you want out of the day.',
                'excerpt' => 'Drive times, staircase conditions and a realistic schedule for seeing both ends of Nusa Penida between boats.',
                'hero_caption' => 'The T-Rex headland in late morning light',
                'tags' => ['#NusaPenida', '#DayTrip', '#Kelingking'],
                'views' => 22_100,
                'body' => '<h2>The drive is the constraint</h2><p>Kelingking to Diamond Beach is roughly ninety minutes on roads that have improved but still narrow to a single lane in places. Doing both means three hours in the car before you have walked anywhere.</p><h2>A schedule that works</h2><p>Land at 08:00, reach Kelingking by 09:00 while the stairs are shaded, leave by 10:30, and arrive at Diamond Beach around noon. That leaves two hours on the east coast before the drive back for a 16:30 boat.</p><h3>What you give up</h3><p>You will not swim at Crystal Bay, and you will see both viewpoints at their busiest. If photographs are the point, pick one side and spend the day there.</p><h2>The staircases</h2><p>Kelingking is the harder of the two: steep, exposed and roughly twenty minutes down. Diamond Beach is carved and handrailed for the full descent.</p>',
            ],
            [
                'title' => 'Fast Ferry vs Speedboat: Comfort, Speed and Price Compared',
                'category' => 'Boat Tips',
                'author' => 'Ni Luh Candra',
                'reader_segment' => 'First-time Island Travelers',
                'subtitle' => 'What the hull shape actually changes',
                'lead' => 'The price gap between the two is smaller than the comfort gap.',
                'excerpt' => 'Multi-engine aluminium hulls against traditional fibreglass speedboats: capacity, ride quality, cancellation rates and cost.',
                'hero_caption' => 'A multi-engine fast ferry alongside at Sanur',
                'tags' => ['#BoatTips', '#FastBoat', '#Comparison'],
                'views' => 14_320,
                'body' => '<h2>The two kinds of boat</h2><p>What operators call a fast ferry is usually a 150–200 seat aluminium catamaran with four or more outboards. A speedboat is a single-hull fibreglass vessel carrying thirty to fifty.</p><h2>Ride quality</h2><p>The catamaran is noticeably steadier in chop because the two hulls damp the roll. In flat water, there is little in it, and the speedboat is often faster door to door.</p><h3>Cancellations</h3><p>The larger boats keep running in conditions that ground the small ones. If your schedule has no slack, the ferry is the safer booking.</p><h2>Cost</h2><p>Expect the ferry to run ten to twenty per cent higher on the same route. For a two-hour crossing to the Gilis that difference buys a lot of comfort; for the forty-five minutes to Penida it matters much less.</p>',
            ],
            [
                'title' => 'What to Pack for Nusa Lembongan and Ceningan',
                'category' => 'Travel Guides',
                'author' => 'Sarah Jenkins',
                'reader_segment' => 'Backpackers',
                'subtitle' => 'Light luggage, wet landings and cash logistics',
                'lead' => 'Both islands are small, hot and short on ATMs. Pack accordingly.',
                'excerpt' => 'Footwear, waterproofing, cash planning and the handful of things that are genuinely hard to buy once you are there.',
                'hero_caption' => 'The yellow bridge between Lembongan and Ceningan',
                'tags' => ['#PackingList', '#NusaLembongan', '#Budget'],
                'views' => 11_460,
                'body' => '<h2>Footwear</h2><p>Reef shoes or sturdy sandals, not flip-flops. Several beaches are reached over rock, and the Ceningan cliff paths are loose underfoot.</p><h2>Keeping things dry</h2><p>Some boats still use wet landings onto the sand. A twenty-litre dry bag for electronics costs very little and removes the problem entirely.</p><h3>Cash</h3><p>There are only a few ATMs across both islands and they run empty at weekends. Bring enough rupiah for the whole stay plus a day, and keep small notes for scooter rental and warungs.</p><h2>What is hard to buy there</h2><p>Reef-safe sunscreen, prescription medication and anything for a specific dietary requirement. Everything else, including a forgotten towel, is available at a markup.</p>',
            ],
            [
                'title' => 'Balinese Temple Etiquette: Visiting Pura Goa Giri Putri',
                'category' => 'Culture',
                'author' => 'Adityarana',
                'reader_segment' => 'Returning Visitors',
                'subtitle' => 'Entering a cave temple without causing offence',
                'lead' => 'The entrance is a crawl through a gap in the rock, and the rules start before you reach it.',
                'excerpt' => 'Sarongs, purification, the menstruation restriction, and how to behave in an active place of worship that is also a tourist attraction.',
                'hero_caption' => 'Offerings at the cave mouth of Goa Giri Putri',
                'tags' => ['#Culture', '#Temples', '#NusaPenida'],
                'views' => 9_740,
                'body' => '<h2>Before you go in</h2><p>A sarong and sash are required and can be rented at the steps. You will be asked to make a small donation and have holy water sprinkled by the pemangku; both are part of entering, not an upsell.</p><h2>The entrance</h2><p>The way in is a low gap you pass through on hands and knees. It opens into a chamber large enough for several hundred people, which is the point of the design.</p><h3>Who should not enter</h3><p>Balinese custom asks that menstruating women and anyone who has had a recent death in the immediate family stay outside. This is observed seriously and nobody will check; it is left to you.</p><h2>Inside</h2><p>Keep your head lower than the shrines, do not point your feet at them, and wait rather than walk through a family mid-prayer. Photography is allowed except during ceremonies.</p>',
            ],
            [
                'title' => 'Getting from Nusa Penida to the Gili Islands Without Backtracking',
                'category' => 'Fast Boat Transfers',
                'author' => 'Ni Luh Candra',
                'reader_segment' => 'Returning Visitors',
                'subtitle' => 'The direct crossings most itineraries miss',
                'lead' => 'You do not have to return to Sanur to reach Gili Trawangan.',
                'excerpt' => 'Direct Penida–Gili sailings, the transit stop that catches people out, and how to plan the day so you arrive in daylight.',
                'hero_caption' => 'Mid-morning departure from Toya Pakeh',
                'tags' => ['#FastBoat', '#GiliIslands', '#Itinerary'],
                'views' => 8_930,
                'body' => '<h2>The direct route exists</h2><p>Boats leave Nusa Penida mid-morning and reach Gili Trawangan in about two hours, continuing to Meno, Air and Bangsal. Many agents still sell the Sanur backtrack because it is easier to book.</p><h2>Watch for the transit stop</h2><p>Some tickets sold as Sanur–Gili include an hour alongside at Penida. That is fine if you know about it and inconvenient if you do not, so check the total journey time rather than the departure hour.</p><h3>Arriving in daylight</h3><p>The last useful departure puts you on Trawangan around half past three. Later arrivals mean finding accommodation in the dark on an island with no street lighting and no cars.</p><h2>Luggage between islands</h2><p>Porters at the Gili jetties work for tips and will quote on the spot. Agree the price before your bag leaves the pier.</p>',
            ],
            [
                'title' => 'A Family Guide to Nusa Penida with Young Children',
                'category' => 'Travel Guides',
                'author' => 'Sarah Jenkins',
                'reader_segment' => 'Families with Children',
                'subtitle' => 'Which parts of the island work with a four-year-old',
                'lead' => 'Half the famous viewpoints involve a staircase nobody should carry a toddler down.',
                'excerpt' => 'Realistic stops, boat tips for small children, where to stay, and the sights worth skipping until they are older.',
                'hero_caption' => 'The shallow entry at Crystal Bay',
                'tags' => ['#FamilyTravel', '#NusaPenida', '#TravelWithKids'],
                'views' => 7_210,
                'body' => '<h2>Start with the crossing</h2><p>Book the earliest boat, sit mid-hull, and bring a change of clothes in the day bag. Children under two usually travel free but still need to be on the manifest.</p><h2>What works</h2><p>Crystal Bay has a gentle sand entry and shade behind the beach. Atuh is a longer drive but has the same forgiving shallows. Both are worth a full half-day rather than a photo stop.</p><h3>What to skip</h3><p>Kelingking, Diamond Beach and Peguyangan. The staircases are steep, exposed and in places genuinely dangerous with a tired child.</p><h2>Where to base yourself</h2><p>Stay in the north-west near Toya Pakeh. The drive to anywhere else on the island is long and rough, and the short transfer at each end of the day matters more than the view from the room.</p>',
            ],
            [
                'title' => 'Scooter Rental on Nusa Penida: Costs, Roads and Insurance',
                'category' => 'Travel Guides',
                'author' => 'Gede Mahendra',
                'reader_segment' => 'Backpackers',
                'subtitle' => 'What the rental shop will not tell you',
                'lead' => 'The island is cheap to ride and expensive to crash on.',
                'excerpt' => 'Daily rates, which roads are still rough, licence and insurance reality, and when hiring a driver is the better call.',
                'hero_caption' => 'The coast road above Toya Pakeh',
                'tags' => ['#NusaPenida', '#Scooter', '#Budget'],
                'views' => 6_480,
                'body' => '<h2>What it costs</h2><p>Expect 75,000 to 100,000 rupiah a day including a helmet, less for longer hires. Fuel is sold in one-litre bottles at roadside stalls and a full day of riding rarely needs more than two.</p><h2>The roads</h2><p>The main north-coast road is sealed and fine. The routes to Kelingking and the south-west are steep, with sharp broken sections and gradients that struggle an underpowered automatic carrying two people.</p><h3>Licence and insurance</h3><p>You need an international permit with a motorcycle endorsement. Without one, travel insurance will decline a claim, and rental shops do not check.</p><h2>When to hire a driver</h2><p>If you are two up, riding for the first time, or trying to cover both ends of the island in a day, a car and driver at around 600,000 rupiah is the better decision.</p>',
            ],
        ];
    }
}
