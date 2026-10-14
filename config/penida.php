<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default admin account
    |--------------------------------------------------------------------------
    |
    | Seeded by AdminUserSeeder. Override the credentials in .env before the
    | first deploy; the password is hashed on save.
    |
    */

    'admin' => [
        'name' => env('ADMIN_NAME', 'Penida Gili Admin'),
        'email' => env('ADMIN_EMAIL', 'admin@penidagili.com'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking
    |--------------------------------------------------------------------------
    */

    'booking' => [
        // Where booking confirmations are BCC'd so the operations team sees new reservations.
        'notify_email' => env('BOOKING_NOTIFY_EMAIL'),
        'whatsapp' => env('BOOKING_WHATSAPP', '628213148787'),
        // "Book With Email" opens the guest's mail client addressed here.
        'email' => env('BOOKING_EMAIL', 'penidagili@gmail.com'),
        'max_party' => 20,
        // Hotel rooms include this many adults; each extra adult is surcharged (per stay).
        'hotel_included_adults' => 2,
        'hotel_extra_adult_price' => 180_000,
    ],

];
