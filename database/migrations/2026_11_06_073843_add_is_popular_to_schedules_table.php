<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marks a sailing as popular, shown with a thumbs-up on the boat page.
 *
 * The badge used to be positional — whichever row happened to be last — which
 * told travellers nothing. Staff now choose it per schedule in the console.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('is_popular')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_popular');
        });
    }
};
