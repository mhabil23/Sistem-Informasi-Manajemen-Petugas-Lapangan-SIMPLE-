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
            $table->string('id_type', 20)->default('NIK')->after('officer_nip');
            $table->text('recommendations')->nullable()->after('work_description');
            $table->string('signature_path', 500)->nullable()->after('signing_date');
            $table->boolean('include_vehicle_statement')->default(true)->after('signature_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_reports', function (Blueprint $table) {
            $table->dropColumn([
                'id_type',
                'recommendations',
                'signature_path',
                'include_vehicle_statement',
            ]);
        });
    }
};

