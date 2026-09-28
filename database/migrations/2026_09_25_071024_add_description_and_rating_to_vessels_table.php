<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each boat carries its own blurb and rating so the catalogue card never
     * falls back to whatever the operator happens to say about its fleet.
     */
    public function up(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->text('description')->nullable()->after('type');
            $table->decimal('rating', 2, 1)->nullable()->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->dropColumn(['description', 'rating']);
        });
    }
};
