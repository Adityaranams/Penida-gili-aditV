<?php

namespace App\Models;

use App\Enums\ListingStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

#[Fillable([
    'boat_operator_id', 'vessel_id', 'from_port_id', 'to_port_id', 'departure_time', 'arrival_time',
    'price_adult', 'price_child', 'price_foreign', 'price_foreign_child', 'days', 'status',
])]
class Schedule extends Model
{
    use HasFactory;

    public const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

    protected function casts(): array
    {
        return [
            'days' => 'array',
            'status' => ListingStatus::class,
            'price_adult' => 'integer',
            'price_child' => 'integer',
            'price_foreign' => 'integer',
            'price_foreign_child' => 'integer',
        ];
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(BoatOperator::class, 'boat_operator_id');
    }

    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    public function fromPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'from_port_id');
    }

    public function toPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'to_port_id');
    }

    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', ListingStatus::Active);
    }

    /** Match a port by name, slug or free text on either end of the route. */
    #[Scope]
    protected function betweenPorts(Builder $query, ?string $from, ?string $to): Builder
    {
        $portMatches = fn (Builder $q, string $term) => $q
            ->where('name', 'like', "%{$term}%")
            ->orWhere('area', 'like', "%{$term}%")
            ->orWhere('slug', 'like', '%'.str($term)->slug().'%');

        return $query
            ->when(filled($from), fn (Builder $q) => $q->whereHas('fromPort', fn (Builder $p) => $portMatches($p, trim($from))))
            ->when(filled($to), fn (Builder $q) => $q->whereHas('toPort', fn (Builder $p) => $portMatches($p, trim($to))));
    }

    /** "Daily", or the weekdays it sails in calendar order ("Mon, Wed, Fri"). */
    protected function daysLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $week = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $days = array_values(array_intersect($week, $this->days ?? []));

            return $days === [] || count($days) === 7 ? 'Daily' : implode(', ', $days);
        });
    }

    /** The first date from `$from` (today by default) this schedule actually sails. */
    public function nextDate(?Carbon $from = null): Carbon
    {
        $date = ($from ?? Carbon::today())->copy();

        for ($i = 0; $i < 7 && ! $this->operatesOn($date); $i++) {
            $date->addDay();
        }

        return $date;
    }

    /** Whether the schedule runs on the given date (null days = daily). */
    public function operatesOn(Carbon $date): bool
    {
        return empty($this->days) || in_array($date->format('D'), $this->days, true);
    }

    /*
     * Aliases used by the booking-widget rows (from / to / departure / arrival / price).
     */
    protected function from(): Attribute
    {
        return Attribute::get(fn () => $this->fromPort->name);
    }

    protected function to(): Attribute
    {
        return Attribute::get(fn () => $this->toPort->name);
    }

    protected function departure(): Attribute
    {
        return Attribute::get(fn () => $this->departure_label);
    }

    protected function arrival(): Attribute
    {
        return Attribute::get(fn () => $this->arrival_label);
    }

    protected function price(): Attribute
    {
        return Attribute::get(fn () => $this->price_label);
    }

    protected function departureLabel(): Attribute
    {
        return Attribute::get(fn () => Carbon::parse($this->departure_time)->format('h:i A'));
    }

    protected function arrivalLabel(): Attribute
    {
        return Attribute::get(fn () => Carbon::parse($this->arrival_time)->format('h:i A'));
    }

    protected function durationMinutes(): Attribute
    {
        return Attribute::get(fn () => Carbon::parse($this->departure_time)->diffInMinutes(Carbon::parse($this->arrival_time)));
    }

    protected function routeLabel(): Attribute
    {
        return Attribute::get(fn () => $this->fromPort->name.' - '.$this->toPort->name);
    }

    protected function priceLabel(): Attribute
    {
        return Attribute::get(fn () => Money::idr($this->price_adult, 'Rp.'));
    }
}
