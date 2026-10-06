<?php

namespace App\Support;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The islands offered in the hero "Where To?" search. Activities have no
 * destination column, so each island is matched by keywords in the activity's
 * location (Bali activities name the regency, e.g. "Uluwatu, Badung").
 */
class Destinations
{
    /**
     * @return Collection<string, array{name: string, tagline: string, description: string, terms: list<string>}>
     */
    public static function all(): Collection
    {
        return collect([
            'nusa-penida' => [
                'name' => 'Nusa Penida',
                'tagline' => 'Cliffs, mantas & Kelingking Beach',
                'description' => 'Dramatic limestone cliffs, crystal bays and the famous T-Rex shaped Kelingking Beach — the wild sister island of Bali.',
                'terms' => ['Nusa Penida', 'Penida', 'Manta Bay', 'Kelingking', 'Crystal Bay'],
            ],
            'nusa-lembongan' => [
                'name' => 'Nusa Lembongan',
                'tagline' => 'Mangroves, reefs & sunset bars',
                'description' => 'A laid-back island of mangrove forests, clear reefs and the Devil\'s Tear coastline, just a short hop from Penida.',
                'terms' => ['Nusa Lembongan', 'Lembongan', 'Ceningan', 'Devil\'s Tear', 'Jungut Batu'],
            ],
            'gili-trawangan' => [
                'name' => 'Gili Trawangan',
                'tagline' => 'Turtles, bikes & beach nights',
                'description' => 'Car-free streets, sea turtles off the shore and sunsets over Bali\'s Mount Agung.',
                'terms' => ['Gili Trawangan', 'Gili T', 'Trawangan', 'Gili Air', 'Gili Meno', 'Gili'],
            ],
            'bali' => [
                'name' => 'Bali',
                'tagline' => 'Temples, dances & rice terraces',
                'description' => 'Cultural shows, mountain villages and beach clubs across the Island of the Gods.',
                'terms' => ['Bali', 'Badung', 'Gianyar', 'Bangli', 'Tabanan', 'Buleleng', 'Denpasar', 'Karangasem', 'Klungkung', 'Jembrana', 'Ubud', 'Uluwatu', 'Kuta', 'Seminyak', 'Canggu', 'Sanur', 'Bedugul', 'Penglipuran', 'Batubulan'],
            ],
        ]);
    }

    /**
     * Find the destination a free-text search refers to, by slug or name.
     *
     * @return array{slug: string, name: string, tagline: string, description: string, terms: list<string>}|null
     */
    public static function find(?string $search): ?array
    {
        $needle = str((string) $search)->trim()->lower()->value();

        if ($needle === '') {
            return null;
        }

        foreach (self::all() as $slug => $destination) {
            if ($needle === $slug || $needle === mb_strtolower($destination['name'])) {
                return ['slug' => $slug, ...$destination];
            }
        }

        return null;
    }

    /**
     * Narrow an activity query to one destination's keywords.
     *
     * @param  list<string>  $terms
     */
    public static function scope(Builder $query, array $terms): Builder
    {
        return $query->where(function (Builder $query) use ($terms) {
            foreach ($terms as $term) {
                $query->orWhere('location', 'like', "%{$term}%")
                    ->orWhere('place_label', 'like', "%{$term}%");
            }
        });
    }
}
