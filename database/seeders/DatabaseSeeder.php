<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the admin account and the catalogue snapshot committed to the repo.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            // The catalogue comes from the committed snapshot, so every machine
            // shows the same content. The demo seeders (PortSeeder,
            // BoatOperatorSeeder, HotelSeeder, ActivitySeeder, ArticleSeeder,
            // BookingSeeder) stay available to call by hand when a throwaway
            // database needs filling.
            ContentSeeder::class,
            // Refreshes the published crossings from database/content/timetable.csv.
            TimetableSeeder::class,
        ]);
    }
}
