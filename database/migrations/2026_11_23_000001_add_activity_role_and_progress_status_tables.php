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
        Schema::table('assignment_officers', function (Blueprint $table) {
            if (!Schema::hasColumn('assignment_officers', 'activity_role')) {
                $table->string('activity_role', 30)->default('PCL')->after('specific_task')->index();
            }
        });

        Schema::table('respondents', function (Blueprint $table) {
            if (!Schema::hasColumn('respondents', 'progress_status')) {
                $table->string('progress_status', 30)->default('belum_didata')->after('purpose')->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignment_officers', function (Blueprint $table) {
            if (Schema::hasColumn('assignment_officers', 'activity_role')) {
                $table->dropColumn('activity_role');
            }
        });

        Schema::table('respondents', function (Blueprint $table) {
            if (Schema::hasColumn('respondents', 'progress_status')) {
                $table->dropColumn('progress_status');
            }
        });
    }
};
