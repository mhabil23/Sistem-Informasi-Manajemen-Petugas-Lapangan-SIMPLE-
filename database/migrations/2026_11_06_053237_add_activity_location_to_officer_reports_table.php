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
        Schema::table('officer_reports', function (Blueprint $table) {
            $table->string('activity_location')->nullable()->after('activity_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_reports', function (Blueprint $table) {
            $table->dropColumn('activity_location');
        });
    }
};
