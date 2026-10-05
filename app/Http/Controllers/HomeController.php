<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Schedule;
use App\Models\Vessel;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Landing page — Figma node 1:55 (desktop) / 1:2386 (mobile).
     */
    public function index(): View
    {
        return view('pages.home', [
            // The three best-rated boats the public can actually book.
            'topBoats' => Vessel::query()
                ->active()
                ->whereHas('operator', fn (Builder $q) => $q->active())
                ->with(['schedules' => fn ($q) => $q->active()])
                ->orderByDesc('rating')
                ->orderBy('name')
                ->take(3)
                ->get(),
            'popularRoutes' => $this->popularRoutes(),
            'testimonials' => Review::query()
                ->where('is_published', true)
                ->whereMorphedTo('reviewable', Vessel::class)
                ->latest('experienced_at')
                ->take(2)
                ->get(),
        ]);
    }

    /**
     * The three busiest port pairs currently on sale, so the home page follows
     * whatever schedules the console publishes.
     *
     * @return Collection<int, array{title: string, body: string, icon: string, href: string}>
     */
    private function popularRoutes(): Collection
    {
        $icons = ['route-penida.svg', 'route-gili.svg', 'route-lembongan.svg'];

        // "· by Island Runner, Penida Express" — omitted when no boat is assigned yet.
        $boats = function ($group): string {
            $names = $group->pluck('vessel.name')->filter()->unique();

            return $names->isEmpty() ? '' : ', sailed by '.$names->join(', ');
        };

        $routes = Schedule::query()
            ->active()
            ->with(['fromPort', 'toPort', 'vessel'])
            ->get()
            ->groupBy(fn (Schedule $schedule) => $schedule->from_port_id.'-'.$schedule->to_port_id)
            ->sortByDesc(fn ($group) => $group->count())
            ->take(3)
            ->values()
            ->map(function ($group, $index) use ($icons, $boats) {
                /** @var Schedule $first */
                $first = $group->first();
                $sailings = $group->count();

                return [
                    'icon' => $icons[$index % count($icons)],
                    'title' => $first->fromPort->name.' ➔ '.$first->toPort->name,
                    // Named after the boats that sail it, so the copy follows what the console manages.
                    'body' => $sailings.' daily sailing'.($sailings > 1 ? 's' : '')
                        .' from '.$group->min('departure_label')
                        .$boats($group).'. Fares from '
                        .Money::idr($group->min('price_adult')).'.',
                    'href' => route('boats.index', ['from' => $first->fromPort->name, 'to' => $first->toPort->name]),
                ];
            });

        return $routes->isEmpty() ? $this->plannedRoutes() : $routes;
    }

    /**
     * What the section shows before any schedule is published: the crossings
     * the service is built around, so a fresh install still reads as a site
     * rather than an empty block. Real schedules replace these as soon as the
     * console has any.
     *
     * @return Collection<int, array{title: string, body: string, icon: string, href: string}>
     */
    private function plannedRoutes(): Collection
    {
        return collect([
            [
                'icon' => 'route-penida.svg',
                'from' => 'Bali',
                'to' => 'Nusa Penida',
                'body' => 'Frequent hourly crossings. The perfect choice for a day trip to witness the iconic Kelingking Beach or Crystal Bay.',
            ],
            [
                'icon' => 'route-gili.svg',
                'from' => 'Bali',
                'to' => 'Gili Trawangan',
                'body' => 'Enjoy a smooth sea journey to the ultimate car-free island hub renowned for its white sand beaches and vibrant nightlife.',
            ],
            [
                'icon' => 'route-lembongan.svg',
                'from' => 'Lombok',
                'to' => 'Nusa Lembongan',
                'body' => 'A specialized route tailored for surfers and explorers seeking exotic coral reefs and a laid-back atmosphere.',
            ],
        ])->map(fn (array $route) => [
            'icon' => $route['icon'],
            'title' => $route['from'].' ➔ '.$route['to'],
            'body' => $route['body'],
            'href' => route('boats.index', ['from' => $route['from'], 'to' => $route['to']]),
        ]);
    }
}
