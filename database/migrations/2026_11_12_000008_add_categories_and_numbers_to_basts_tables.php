<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->string('receipt_category', 20)->nullable()->after('bast_type'); // 'ATK' | 'KUES'
        });

        Schema::table('respondent_basts', function (Blueprint $table) {
            $table->unsignedInteger('respondent_number')->nullable()->after('respondent_id');
            $table->string('handover_category', 20)->nullable()->default('KUES')->after('respondent_number'); // 'ATK' | 'KUES'
        });
    }

    public function down(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->dropColumn('receipt_category');
        });

        Schema::table('respondent_basts', function (Blueprint $table) {
            $table->dropColumn(['respondent_number', 'handover_category']);
        });
    }
};
