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
            if (!Schema::hasColumn('respondents', 'processing_status')) {
                $table->string('processing_status', 30)->default('belum_diterima')->after('progress_status')->index();
            }
            if (!Schema::hasColumn('respondents', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('processing_status');
            }
            if (!Schema::hasColumn('respondents', 'edited_at')) {
                $table->timestamp('edited_at')->nullable()->after('received_at');
            }
            if (!Schema::hasColumn('respondents', 'entered_at')) {
                $table->timestamp('entered_at')->nullable()->after('edited_at');
            }
            if (!Schema::hasColumn('respondents', 'cleaned_at')) {
                $table->timestamp('cleaned_at')->nullable()->after('entered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('respondents', function (Blueprint $table) {
            $table->dropColumn([
                'processing_status',
                'received_at',
                'edited_at',
                'entered_at',
                'cleaned_at',
            ]);
        });
    }
};
