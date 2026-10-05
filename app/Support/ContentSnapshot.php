<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The catalogue as a file that travels with the repository.
 *
 * Each developer runs their own database, so content added in one console is
 * invisible everywhere else. Exporting it to JSON and committing that file —
 * together with the uploaded images under storage/app/public/uploads — lets a
 * teammate clone, seed, and see exactly the same site.
 */
class ContentSnapshot
{
    /** Parents first: the order rows have to be written in to satisfy foreign keys. */
    public const TABLES = [
        'ports',
        'boat_operators',
        'routes',
        'vessels',
        'schedules',
        'hotels',
        'hotel_rooms',
        'activities',
        'authors',
        'articles',
        'reviews',
    ];

    public static function path(): string
    {
        return database_path('content/content.json');
    }

    /** Tables that exist in this database, in write order. */
    public static function tables(): array
    {
        return array_values(array_filter(self::TABLES, fn (string $table) => Schema::hasTable($table)));
    }

    /** How many catalogue rows the database currently holds. */
    public static function rowCount(): int
    {
        return array_sum(array_map(fn (string $table) => DB::table($table)->count(), self::tables()));
    }

    /**
     * Every content row in the database, keyed by table.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public static function collect(): array
    {
        $data = [];

        foreach (self::tables() as $table) {
            $data[$table] = DB::table($table)->orderBy('id')->get()
                ->map(fn ($row) => (array) $row)
                ->all();
        }

        return $data;
    }

    /**
     * Replace the catalogue with the snapshot on disk.
     *
     * Bookings and accounts are left alone: they belong to whoever is running
     * the site, not to the content being shared.
     */
    public static function restore(array $data): array
    {
        $counts = [];

        Schema::withoutForeignKeyConstraints(function () use ($data, &$counts): void {
            foreach (array_reverse(self::tables()) as $table) {
                DB::table($table)->delete();
            }

            foreach (self::tables() as $table) {
                $rows = $data[$table] ?? [];

                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table($table)->insert($chunk);
                }

                $counts[$table] = count($rows);
            }
        });

        return $counts;
    }
}
