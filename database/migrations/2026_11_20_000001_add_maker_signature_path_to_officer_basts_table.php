<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            if (!Schema::hasColumn('officer_basts', 'maker_signature_path')) {
                $table->string('maker_signature_path')->nullable()->after('maker_nip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->dropColumn('maker_signature_path');
        });
    }
};
