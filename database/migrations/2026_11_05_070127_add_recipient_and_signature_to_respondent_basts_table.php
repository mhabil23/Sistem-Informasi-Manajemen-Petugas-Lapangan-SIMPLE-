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
        Schema::table('respondent_basts', function (Blueprint $table) {
            $table->enum('recipient_type', ['krt', 'representative'])->default('krt')->after('handover_items');
            $table->string('recipient_name')->nullable()->after('recipient_type');
            $table->string('recipient_relation')->nullable()->after('recipient_name');
            $table->string('signature_path')->nullable()->after('recipient_relation');
            $table->string('officer_signature_path')->nullable()->after('signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondent_basts', function (Blueprint $table) {
            $table->dropColumn([
                'recipient_type',
                'recipient_name',
                'recipient_relation',
                'signature_path',
                'officer_signature_path',
            ]);
        });
    }
};
