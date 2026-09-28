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
