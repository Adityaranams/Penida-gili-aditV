<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Booking;
use App\Models\HotelRoom;
use App\Models\Schedule;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

/**
 * The one query behind the Booking Report and the dashboard's "Recent Transactions",
 * so both consoles always show the same reservations under the same filters.
 */
final class BookingReport
{
    /** "Recent Transactions" product filter → the bookable it maps to. */
    public const PRODUCTS = [
        'boat' => Schedule::class,
        'activity' => Activity::class,
        'hotel' => HotelRoom::class,
    ];

    /**
     * Newest first, filtered by whichever of q / type / vessel / status / date the request carries.
     */
    public static function filtered(Request $request): Builder
    {
        $type = $request->string('type')->value();

        return Booking::query()
            ->with(['bookable' => fn (MorphTo $morph) => $morph->morphWith([
                Schedule::class => ['operator', 'vessel', 'fromPort', 'toPort'],
                HotelRoom::class => ['hotel'],
            ])])
            // "Search Route" (Figma 1:10428) also matches the passenger / reference; "Sanur to Nusa Penida"
            // matches both ports, a single word matches either end.
            ->when($request->filled('q'), function (Builder $q) use ($request): void {
                $term = $request->string('q')->value();
                [$from, $to] = array_pad(preg_split('/\s+(?:to|-|→)\s+/iu', $term, 2), 2, null);

                $q->where(fn (Builder $w) => $w
                    ->search($term)
                    ->orWhereHasMorph('bookable', [Schedule::class], fn (Builder $s) => $s
                        ->whereHas('fromPort', fn (Builder $p) => $p->where('name', 'like', "%{$from}%"))
                        ->when($to, fn (Builder $x) => $x->whereHas('toPort', fn (Builder $p) => $p->where('name', 'like', "%{$to}%"))))
                    ->when(! $to, fn (Builder $x) => $x->orWhereHasMorph('bookable', [Schedule::class], fn (Builder $s) => $s
                        ->whereHas('toPort', fn (Builder $p) => $p->where('name', 'like', "%{$from}%")))));
            })
            ->when(isset(self::PRODUCTS[$type]), fn (Builder $q) => $q->where('bookable_type', self::PRODUCTS[$type]))
            ->when($request->filled('vessel'), fn (Builder $q) => $q->whereHasMorph('bookable', [Schedule::class], fn (Builder $s) => $s->where('vessel_id', $request->integer('vessel'))))
            ->when($request->filled('status'), fn (Builder $q) => $q->where('status', $request->string('status')->value()))
            ->when($request->filled('date'), fn (Builder $q) => $q->whereDate('travel_date', $request->date('date')))
            ->latest();
    }
}
