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
        Schema::table('respondents', function (Blueprint $table) {
            $table->string('family_head_name')->nullable()->after('name');
            $table->string('informant_name')->nullable()->after('family_head_name');
            $table->string('respondent_status')->nullable()->after('informant_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropColumn([
                'family_head_name',
                'informant_name',
                'respondent_status',
            ]);
        });
    }
};
