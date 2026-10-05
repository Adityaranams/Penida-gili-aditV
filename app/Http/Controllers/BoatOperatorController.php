<?php

namespace App\Http\Controllers;

use App\Enums\ListingStatus;
use App\Models\BoatOperator;
use App\Models\Schedule;
use App\Models\Vessel;
use App\Support\BookingQuote;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class BoatOperatorController extends Controller
{
    /**
     * Boat Operators listing — Figma node 1:408. Also serves the hero search
     * (?from=&to=&date=&guests=) by narrowing to operators sailing that route.
     */
    public function index(Request $request): View
    {
        $from = $request->string('from')->trim()->value();
        $to = $request->string('to')->trim()->value();
        $date = $request->date('date');

        // The catalogue lists individual boats, so anything added in the console shows up here.
        $boats = Vessel::query()
            ->active()
            ->with('operator')
            ->whereHas('operator', fn (Builder $q) => $q->active())
            ->when($from || $to, fn (Builder $q) => $q->whereHas(
                'operator.schedules',
                fn (Builder $s) => $s->active()->betweenPorts($from, $to),
            ))
            ->with(['schedules' => fn ($q) => $q->active()])
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('pages.boats', [
            'boats' => $boats,
            'search' => ['from' => $from, 'to' => $to, 'date' => $date?->toDateString(), 'guests' => $request->integer('guests') ?: null],
        ]);
    }

    /**
     * Fast boat schedules — results of the home hero search
     * (?from=&to=&date=&guests=&time=&sort=).
     */
    public function schedules(Request $request): View
    {
        $from = $request->string('from')->trim()->value();
        $to = $request->string('to')->trim()->value();
        $date = $request->date('date') ?? Carbon::today();
        $date = $date->isPast() && ! $date->isToday() ? Carbon::today() : $date->startOfDay();
        $guests = max(1, min(20, $request->integer('guests', 1)));
        $time = in_array($request->query('time'), ['morning', 'afternoon', 'evening'], true) ? $request->query('time') : 'all';
        $sort = $request->query('sort') === 'cheapest' ? 'cheapest' : 'earliest';

        $routeSchedules = Schedule::query()
            ->active()
            ->betweenPorts($from, $to)
            ->whereHas('operator', fn (Builder $q) => $q->active())
            ->where(fn (Builder $q) => $q->whereNull('vessel_id')->orWhereHas('vessel', fn (Builder $v) => $v->active()))
            ->with(['operator', 'vessel', 'fromPort', 'toPort'])
            ->orderBy('departure_time')
            ->get();

        $days = collect(range(-3, 3))->map(function (int $offset) use ($date, $routeSchedules) {
            $day = $date->copy()->addDays($offset);
            $sailing = $routeSchedules->filter(fn (Schedule $s) => $s->operatesOn($day));

            return ['date' => $day, 'from_price' => $sailing->min('price_adult'), 'is_past' => $day->lt(Carbon::today())];
        });

        $sailings = $routeSchedules->filter(fn (Schedule $s) => $s->operatesOn($date));

        $periodOf = fn (Schedule $s): string => match (true) {
            (int) substr($s->departure_time, 0, 2) < 12 => 'morning',
            (int) substr($s->departure_time, 0, 2) < 17 => 'afternoon',
            default => 'evening',
        };

        $periodCounts = $sailings->countBy($periodOf)->put('all', $sailings->count());

        $results = $sailings
            ->when($time !== 'all', fn ($c) => $c->filter(fn (Schedule $s) => $periodOf($s) === $time))
            ->sortBy($sort === 'cheapest' ? 'price_adult' : 'departure_time')
            ->values();

        return view('pages.boat-schedules', [
            'search' => ['from' => $from, 'to' => $to, 'date' => $date, 'guests' => $guests, 'time' => $time, 'sort' => $sort],
            'days' => $days,
            'schedules' => $results,
            'periodCounts' => $periodCounts,
            'fromPrice' => $sailings->min('price_adult'),
        ]);
    }

    /**
     * One boat from the fleet — its own photos, specs and sailings.
     */
    public function vessel(Vessel $vessel): View
    {
        abort_unless($vessel->status === ListingStatus::Active && $vessel->operator->is_active, 404);

        $vessel->load([
            'operator',
            'schedules' => fn ($q) => $q->active()->with(['fromPort', 'toPort']),
            'reviews' => fn ($q) => $q->where('is_published', true),
        ]);

        return view('pages.boat-vessel', ['vessel' => $vessel]);
    }

    /**
     * Order summary — Figma node 1:1923. Reached from the booking widget with
     * ?schedule=&date=&adults=&children=.
     */
    public function order(Request $request, BoatOperator $boat): View
    {
        $schedule = $boat->schedules()->active()->with(['fromPort', 'toPort'])
            ->when($request->filled('schedule'), fn ($q) => $q->whereKey($request->integer('schedule')))
            ->firstOrFail();

        $schedule->setRelation('operator', $boat);

        $quote = BookingQuote::forSchedule(
            $schedule,
            $request->date('date') ?? Carbon::tomorrow(),
            $request->integer('adults', 1),
            $request->integer('children', 0),
        );

        return view('pages.boat-order', [
            'order' => $quote->toOrderDraft() + ['action' => route('boats.book', $boat)],
        ]);
    }
}
