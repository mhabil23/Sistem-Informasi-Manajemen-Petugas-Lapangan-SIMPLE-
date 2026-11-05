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
            $table->string('officer_name')->nullable()->after('user_id');
            $table->string('officer_nip')->nullable()->after('officer_name');
            $table->string('officer_rank')->nullable()->after('officer_nip');
            $table->string('officer_position')->nullable()->after('officer_rank');
            $table->text('basis_dipa')->nullable()->after('officer_position');
            $table->text('basis_st')->nullable()->after('basis_dipa');
            $table->text('destination_purpose')->nullable()->after('basis_st');
            $table->string('duration_text')->nullable()->default('Selama 1 (satu) Hari')->after('destination_purpose');
            $table->string('signing_city')->nullable()->default('Lasusua')->after('duration_text');
            $table->date('signing_date')->nullable()->after('signing_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_reports', function (Blueprint $table) {
            $table->dropColumn([
                'officer_name',
                'officer_nip',
                'officer_rank',
                'officer_position',
                'basis_dipa',
                'basis_st',
                'destination_purpose',
                'duration_text',
                'signing_city',
                'signing_date',
            ]);
        });
    }
};

