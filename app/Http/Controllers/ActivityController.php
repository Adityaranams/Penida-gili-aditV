<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Support\BookingQuote;
use App\Support\Destinations;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Activity listing — Figma node 1:623.
     */
    public function index(Request $request): View
    {
        $destination = $request->string('q')->trim()->value();

        return view('pages.activities', [
            'activities' => Activity::query()
                ->active()
                ->when($destination !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('location', 'like', "%{$destination}%")
                    ->orWhere('name', 'like', "%{$destination}%")))
                ->orderByDesc('rating')
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    /**
     * "Things to do in …" — results of the hero "Where To?" search (?q=).
     * A known island narrows by its keywords; any other text searches name and location.
     */
    public function explore(Request $request): View
    {
        $search = $request->string('q')->trim()->value();
        $destination = Destinations::find($search);

        $tabs = Destinations::all()->map(function (array $place, string $slug) {
            $matches = Destinations::scope(Activity::query()->active(), $place['terms']);

            return [
                'slug' => $slug,
                'name' => $place['name'],
                'count' => (clone $matches)->count(),
                // The island's own photo, so the tab and the hero stay the same
                // whatever activities happen to be published.
                'image' => asset('images/'.$place['image']),
            ];
        })->values();

        $activities = Activity::query()
            ->active()
            ->when($destination, fn (Builder $query) => Destinations::scope($query, $destination['terms']))
            ->when(! $destination && $search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('place_label', 'like', "%{$search}%")))
            ->orderByDesc('rating')
            ->paginate(9)
            ->withQueryString();

        return view('pages.activity-explore', [
            'search' => $search,
            'destination' => $destination,
            'tabs' => $tabs,
            'totalCount' => Activity::query()->active()->count(),
            'activities' => $activities,
        ]);
    }

    /**
     * Activity detail — Figma node 1:1442.
     */
    public function show(Activity $activity): View
    {
        abort_unless($activity->status->value === 'active', 404);

        $related = Activity::query()->active()->whereKeyNot($activity->id)->orderByDesc('sold_count')->take(4)->get();

        return view('pages.activity-detail', ['activity' => $activity, 'related' => $related]);
    }

    /**
     * Order summary — Figma node 1:2874.
     */
    public function order(Request $request, Activity $activity): View
    {
        $quote = BookingQuote::forActivity(
            $activity,
            $request->date('date') ?? Carbon::tomorrow(),
            $request->integer('adults', 1),
            $request->integer('children', 0),
        );

        return view('pages.activity-order', [
            'order' => $quote->toOrderDraft() + ['action' => route('activities.book', $activity)],
        ]);
    }
}
