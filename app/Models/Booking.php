<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

#[Fillable([
    'bookable_type', 'bookable_id', 'customer_name', 'customer_email', 'dial_code', 'phone', 'nationality',
    'travel_date', 'check_out', 'adults', 'children', 'nights', 'rooms', 'unit_price_adult', 'unit_price_child',
    'total', 'currency', 'status', 'payment_status', 'notes', 'confirmed_at',
])]
class Booking extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'check_out' => 'date',
            'confirmed_at' => 'datetime',
            'status' => BookingStatus::class,
            'payment_status' => PaymentStatus::class,
            'total' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking): void {
            $booking->reference ??= static::generateReference();
        });
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'PG-'.Str::upper(Str::random(6));
        } while (static::query()->where('reference', $ref)->exists());

        return $ref;
    }

    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    #[Scope]
    protected function confirmed(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::Confirmed);
    }

    #[Scope]
    protected function search(Builder $query, ?string $term): Builder
    {
        return $query->when(filled($term), fn (Builder $q) => $q->where(fn (Builder $w) => $w
            ->where('reference', 'like', "%{$term}%")
            ->orWhere('customer_name', 'like', "%{$term}%")
            ->orWhere('customer_email', 'like', "%{$term}%")));
    }

    public function confirm(): void
    {
        $this->forceFill(['status' => BookingStatus::Confirmed, 'confirmed_at' => now()])->save();
    }

    public function cancel(): void
    {
        $this->forceFill(['status' => BookingStatus::Cancelled])->save();
    }

    /**
     * Row shape consumed by the console transactions tables.
     *
     * @return array<string, mixed>
     */
    public function toReportRow(): array
    {
        [$from, $to, $vessel] = match (true) {
            $this->bookable instanceof Schedule => [
                $this->bookable->fromPort->name,
                $this->bookable->toPort->name,
                $this->bookable->vessel?->name ?? $this->bookable->operator->name,
            ],
            $this->bookable instanceof HotelRoom => [$this->bookable->hotel->name, $this->bookable->name, 'Hotel stay'],
            $this->bookable instanceof Activity => [$this->bookable->place_label ?? $this->bookable->location, $this->bookable->name, 'Activity'],
            default => ['—', '—', '—'],
        };

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'initials' => $this->customer_initials,
            'name' => $this->customer_name,
            // The order form collects a phone number, not an email; show whichever is on file.
            'email' => $this->customer_email ?: trim($this->dial_code.' '.$this->phone),
            'from' => $from,
            'to' => $to,
            'vessel' => $vessel,
            'date' => $this->travel_date->format('M d, Y'),
            'time' => $this->bookable instanceof Schedule ? $this->bookable->departure_label : $this->created_at->format('h:i A'),
            'amount' => Money::idr($this->total, 'Rp.'),
            'status' => $this->status->label(),
            'status_value' => $this->status->value,
        ];
    }

    /**
     * Pre-filled WhatsApp text for the confirmation page, mirroring what the
     * guest entered on the order form.
     */
    public function whatsappMessage(): string
    {
        $lines = [
            'Hi, I already booked this.',
            '',
            'Booking Reference: '.$this->reference,
            'Full Name: '.$this->customer_name,
            'Nationality: '.$this->nationality,
            'Phone Number: '.$this->dial_code.' '.$this->phone,
            'Order Notes: '.($this->notes ?: '-'),
            '',
        ];

        $guests = $this->adults + $this->children;

        $lines = array_merge($lines, match (true) {
            $this->bookable instanceof HotelRoom => [
                'Hotel: '.$this->bookable->hotel->name,
                'Room Type: '.$this->bookable->name.($this->rooms > 1 ? ' x '.$this->rooms : ''),
                'Dates: '.$this->travel_date->format('j F Y').' - '.$this->check_out?->format('j F Y'),
                'Guest: '.$guests,
            ],
            $this->bookable instanceof Schedule => [
                'Operator: '.$this->bookable->operator->name,
                'Route: '.$this->bookable->fromPort->name.' - '.$this->bookable->toPort->name,
                'Departure: '.$this->travel_date->format('j F Y').' '.$this->bookable->departure_label,
                'Passengers: '.$this->adults.' adult(s), '.$this->children.' child(ren)',
            ],
            $this->bookable instanceof Activity => [
                'Activity: '.$this->bookable->name,
                'Date: '.$this->travel_date->format('j F Y'),
                'Participants: '.$this->adults.' adult(s), '.$this->children.' child(ren)',
            ],
            default => [],
        });

        $lines[] = 'Total: '.Money::idr($this->total, 'Rp');

        return implode("\n", $lines);
    }

    protected function totalLabel(): Attribute
    {
        return Attribute::get(fn () => Money::idr($this->total, $this->currency));
    }

    protected function customerInitials(): Attribute
    {
        return Attribute::get(fn () => collect(explode(' ', $this->customer_name))
            ->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode(''));
    }

    protected function guestsLabel(): Attribute
    {
        return Attribute::get(fn () => trim(
            $this->adults.' Adult'.($this->adults > 1 ? 's' : '')
            .($this->children ? ', '.$this->children.' Child'.($this->children > 1 ? 'ren' : '') : '')
        ));
    }

    /** Human label for the reserved product regardless of type. */
    protected function productLabel(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->bookable instanceof Schedule => $this->bookable->operator->name,
            $this->bookable instanceof HotelRoom => $this->bookable->hotel->name.' — '.$this->bookable->name.($this->rooms > 1 ? ' × '.$this->rooms.' rooms' : ''),
            $this->bookable instanceof Activity => $this->bookable->name,
            default => '—',
        });
    }
}
