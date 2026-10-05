<?php

namespace Database\Seeders;

use App\Support\ContentSnapshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

/**
 * Load the committed catalogue (php artisan content:export writes it).
 *
 * This is what gives a teammate the same boats, hotels, activities and
 * articles — with their photos — straight after cloning.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $path = ContentSnapshot::path();

        if (! File::exists($path)) {
            $this->command?->warn('No content snapshot yet; run "php artisan content:export" on the machine that has the data.');

            return;
        }

        // Restoring replaces the catalogue, so anything added since the last
        // export would be lost. Say so before wiping someone's work.
        $existing = ContentSnapshot::rowCount();

        if ($existing > 0 && $this->command && ! $this->command->option('force')
            && ! $this->command->confirm("The catalogue already holds {$existing} rows; restoring the snapshot replaces them. Continue?")) {
            $this->command->warn('Left the catalogue untouched.');

            return;
        }

        $counts = ContentSnapshot::restore(json_decode(File::get($path), true) ?: []);

        $this->command?->info('Catalogue restored: '.collect($counts)
            ->filter()
            ->map(fn (int $rows, string $table) => "{$table} {$rows}")
            ->join(', '));
    }
}
