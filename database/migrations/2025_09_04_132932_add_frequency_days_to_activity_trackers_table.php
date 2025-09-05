<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activity_trackers', function (Blueprint $table) {
            $table->unsignedInteger('frequency_days')->nullable()->after('last_completed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_trackers', function (Blueprint $table) {
            $table->dropColumn('frequency_days');
        });
    }
};
