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
        if (!Schema::hasColumn('assignments', 'is_enabled_by_admin')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->boolean('is_enabled_by_admin')->default(false)->after('status')->index();
            });
        }

        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('assignments', 'is_enabled_by_admin')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->dropIndex(['is_enabled_by_admin']);
                $table->dropColumn('is_enabled_by_admin');
            });
        }
    }
};
