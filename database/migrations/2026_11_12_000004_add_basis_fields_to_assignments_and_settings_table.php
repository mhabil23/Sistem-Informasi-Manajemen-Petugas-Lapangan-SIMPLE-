<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->text('basis_dipa')->nullable()->after('location');
            $table->string('st_number')->nullable()->after('basis_dipa');
            $table->date('st_date')->nullable()->after('st_number');
            $table->text('basis_st')->nullable()->after('st_date');
        });

        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->longText('value')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');

        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['basis_dipa', 'st_number', 'st_date', 'basis_st']);
        });
    }
};
