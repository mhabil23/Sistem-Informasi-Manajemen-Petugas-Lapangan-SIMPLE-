<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('training_events', function (Blueprint $table) {
            if (!Schema::hasColumn('training_events', 'public_token')) {
                $table->string('public_token', 64)->nullable()->unique()->after('status');
            }
        });

        // Generate unique tokens for existing training events
        $events = DB::table('training_events')->whereNull('public_token')->get();
        foreach ($events as $event) {
            DB::table('training_events')
                ->where('id', $event->id)
                ->update(['public_token' => Str::random(24)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_events', function (Blueprint $table) {
            if (Schema::hasColumn('training_events', 'public_token')) {
                $table->dropColumn('public_token');
            }
        });
    }
};
