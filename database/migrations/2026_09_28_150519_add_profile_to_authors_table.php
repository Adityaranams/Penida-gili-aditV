<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** The byline and the author card on an article are filled from these. */
    public function up(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('role');
            $table->string('credential')->nullable()->after('photo');
            $table->text('bio')->nullable()->after('credential');
        });
    }

    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn(['photo', 'credential', 'bio']);
        });
    }
};
