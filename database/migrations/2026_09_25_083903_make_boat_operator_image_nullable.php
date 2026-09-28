<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Records without a photo fall back to the neutral placeholder, so no image is required. */
    public function up(): void
    {
        Schema::table('boat_operators', function (Blueprint $table) {
            $table->string('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('boat_operators', function (Blueprint $table) {
            $table->string('image')->nullable(false)->change();
        });
    }
};
