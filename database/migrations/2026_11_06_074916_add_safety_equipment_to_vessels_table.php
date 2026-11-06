<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Safety gear listed on the boat page, entered as chips in the console.
 *
 * Kept apart from `facilities`, which is a fixed tick-list of comforts. Safety
 * equipment differs per boat and is worded by the operator, so it is free text.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->json('safety_equipment')->nullable()->after('facilities');
        });
    }

    public function down(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->dropColumn('safety_equipment');
        });
    }
};
