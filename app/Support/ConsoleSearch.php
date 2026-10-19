<?php

namespace App\Support;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Schedule;
use App\Models\Vessel;

/**
 * The console header's search: one box over every module.
 *
 * Staff search for the thing they are holding — a booking reference a guest
 * read out, a boat name, a hotel — without first choosing which screen it
 * lives on. So this fans out across the modules and groups what it finds,
 * each row linking to the screen that can act on it.
 */
class ConsoleSearch
{
    /** Rows shown per module before "see all" takes over. */
    public const PER_GROUP = 5;

    public static function run(string $term): array
    {
        $term = trim($term);

        if ($term === '') {
            return [];
        }

        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return array_values(array_filter([
            self::group('Boats', Vessel::query()
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('code', 'like', $like)->orWhere('type', 'like', $like))
                ->orderBy('name')
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Vessel $v) => [
                    'title' => $v->name,
                    'meta' => trim($v->type.' · '.$v->capacity.' seats', ' ·'),
                    'url' => route('admin.boats.edit', $v),
                ])),

            self::group('Schedules', Schedule::query()
                ->with(['fromPort', 'toPort', 'vessel'])
                ->where(fn ($q) => $q
                    ->whereHas('fromPort', fn ($p) => $p->where('name', 'like', $like))
                    ->orWhereHas('toPort', fn ($p) => $p->where('name', 'like', $like))
                    ->orWhereHas('vessel', fn ($v) => $v->where('name', 'like', $like)))
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Schedule $s) => [
                    'title' => $s->fromPort?->name.' → '.$s->toPort?->name,
                    'meta' => trim(($s->vessel?->name ?? '').' · '.$s->departure_label, ' ·'),
                    'url' => route('admin.schedules.edit', $s),
                ])),

            self::group('Activities', Activity::query()
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('location', 'like', $like)->orWhere('category', 'like', $like))
                ->orderBy('name')
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Activity $a) => [
                    'title' => $a->name,
                    'meta' => $a->place_label ?: $a->location,
                    'url' => route('admin.activities.edit', $a),
                ])),

            self::group('Hotels', Hotel::query()
                ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('address', 'like', $like)->orWhere('region', 'like', $like))
                ->orderBy('name')
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Hotel $h) => [
                    'title' => $h->name,
                    'meta' => $h->address ?: $h->region,
                    'url' => route('admin.hotels.edit', $h),
                ])),

            self::group('Articles', Article::query()
                ->where(fn ($q) => $q->where('title', 'like', $like)->orWhere('excerpt', 'like', $like)->orWhere('category', 'like', $like))
                ->latest('created_at')
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Article $a) => [
                    'title' => $a->title,
                    'meta' => $a->category,
                    'url' => route('admin.articles.edit', $a),
                ])),

            self::group('Bookings', Booking::query()
                ->where(fn ($q) => $q->where('reference', 'like', $like)->orWhere('customer_name', 'like', $like)->orWhere('customer_email', 'like', $like))
                ->latest('created_at')
                ->limit(self::PER_GROUP)
                ->get()
                ->map(fn (Booking $b) => [
                    'title' => $b->customer_name.' · '.$b->reference,
                    'meta' => Money::idr($b->total, 'Rp.').' · '.$b->status->label(),
                    'url' => route('admin.report', ['q' => $b->reference]),
                ])),
        ]));
    }

    public static function total(array $groups): int
    {
        return array_sum(array_map(fn (array $group) => count($group['rows']), $groups));
    }

    private static function group(string $label, $rows): ?array
    {
        $rows = $rows->all();

        return $rows === [] ? null : ['label' => $label, 'rows' => $rows];
    }
}
