<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Settings the console can edit without a deploy.
 *
 * Contact details used to live only in config/penida.php, so changing the
 * WhatsApp number meant editing .env on every machine and on the server —
 * which needs shell access the team does not all have. A row here overrides
 * the config default at boot; no row means the config default still wins.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
