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
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->string('first_party_position')->nullable()->after('maker_nip');
            $table->text('first_party_address')->nullable()->after('first_party_position');
            $table->string('second_party_nik', 30)->nullable()->after('first_party_address');
            $table->text('second_party_address')->nullable()->after('second_party_nik');
            $table->string('contract_number')->nullable()->after('second_party_address');
            $table->date('contract_date')->nullable()->after('contract_number');
            $table->string('doc_count', 100)->nullable()->after('contract_date');
            $table->text('activity_work_name')->nullable()->after('doc_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->dropColumn([
                'first_party_position',
                'first_party_address',
                'second_party_nik',
                'second_party_address',
                'contract_number',
                'contract_date',
                'doc_count',
                'activity_work_name',
            ]);
        });
    }
};
