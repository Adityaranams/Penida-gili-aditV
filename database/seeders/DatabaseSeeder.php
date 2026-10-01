<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the catalogue with the content the Figma frames were designed around.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            PortSeeder::class,
            BoatOperatorSeeder::class,
            // HotelSeeder, ActivitySeeder and ArticleSeeder are left out on
            // purpose: those listings are written in the console, and seeding
            // would bring the demo entries back every time.
            BookingSeeder::class,
        ]);
    }
}
