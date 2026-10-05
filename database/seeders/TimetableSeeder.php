<?php

namespace Database\Seeders;

use App\Enums\ListingStatus;
use App\Models\BoatOperator;
use App\Models\Port;
use App\Models\Schedule;
use App\Models\Vessel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * The operator's published timetable (database/content/timetable.csv, converted
 * from TimeTable.xlsx).
 *
 * Ports, boats and sailings are matched on what makes them unique rather than
 * inserted blindly, so re-running after an updated timetable refreshes the
 * times instead of duplicating every crossing.
 */
class TimetableSeeder extends Seeder
{
    /** The sheet carries no fares; these stand in until the console sets real ones. */
    private const PRICE_ADULT = 200_000;

    private const PRICE_CHILD = 150_000;

    /** Which island each port belongs to, for the search filters. */
    private const AREAS = [
        'Sanur' => 'Bali',
        'Nusa Penida' => 'Nusa Penida',
        'Nusa Lembongan' => 'Nusa Lembongan',
        'Gili Trawangan' => 'Gili Islands',
        'Gili Meno' => 'Gili Islands',
        'Gili Air' => 'Gili Islands',
        'Bangsal Lombok' => 'Lombok',
    ];

    public function run(): void
    {
        $path = database_path('content/timetable.csv');

        if (! File::exists($path)) {
            $this->command?->warn('No timetable.csv to import.');

            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $rows = array_map(fn (string $line) => str_getcsv($line, ',', '"', '\\'), $lines);
        $header = array_shift($rows);

        $operator = BoatOperator::query()->orderBy('id')->first()
            ?? BoatOperator::query()->create(['name' => 'Penida Gili', 'description' => '', 'is_active' => true]);

        $ports = [];
        $vessels = [];
        $sailings = 0;

        foreach ($rows as $row) {
            $row = array_combine($header, $row);

            foreach ([$row['from'], $row['to']] as $name) {
                $ports[$name] ??= Port::query()->firstOrCreate(
                    ['name' => $name],
                    ['area' => self::AREAS[$name] ?? null],
                );
            }

            if ($row['boat'] !== '' && ! isset($vessels[$row['boat']])) {
                $vessels[$row['boat']] = Vessel::query()->firstOrCreate(
                    ['name' => $row['boat']],
                    [
                        'boat_operator_id' => $operator->id,
                        'code' => Vessel::nextCode(),
                        'type' => 'Boat',
                        'capacity' => 100,
                        'status' => ListingStatus::Active,
                    ],
                );
            }

            Schedule::query()->updateOrCreate(
                [
                    'from_port_id' => $ports[$row['from']]->id,
                    'to_port_id' => $ports[$row['to']]->id,
                    'departure_time' => $row['departure'],
                ],
                [
                    'boat_operator_id' => $operator->id,
                    'vessel_id' => $vessels[$row['boat']]->id ?? null,
                    'arrival_time' => $row['arrival'],
                    'price_adult' => self::PRICE_ADULT,
                    'price_child' => self::PRICE_CHILD,
                    'days' => null,
                    'status' => ListingStatus::Active,
                ],
            );

            $sailings++;
        }

        $this->command?->info(sprintf(
            'Timetable imported: %d sailings across %d ports on %d boats.',
            $sailings, count($ports), count($vessels),
        ));
    }
}
