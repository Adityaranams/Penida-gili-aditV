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

        return Schedule::query()
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
    }
}
