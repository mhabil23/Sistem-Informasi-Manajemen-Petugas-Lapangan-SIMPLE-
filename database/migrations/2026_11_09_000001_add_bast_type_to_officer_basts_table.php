<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->string('bast_type')->default('kantor_ke_petugas')->after('officer_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->dropColumn('bast_type');
        });
    }
};

