<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_basts', function (Blueprint $table) {
            $table->foreignId('officer_id')->nullable()->change();
            $table->string('document_title')->nullable()->after('description');
            $table->string('activity_subtitle')->nullable()->after('document_title');
            $table->string('institution_name')->nullable()->after('activity_subtitle');
            $table->string('document_place')->nullable()->after('institution_name');
            $table->string('maker_name')->nullable()->after('document_place');
            $table->string('maker_nip', 100)->nullable()->after('maker_name');
        });

        Schema::create('officer_bast_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('officer_bast_id')->constrained('officer_basts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recipient_name');
            $table->string('position_unit')->nullable()->default('Mitra');
            $table->string('origin_role')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_bast_recipients');

        Schema::table('officer_basts', function (Blueprint $table) {
            $table->dropColumn([
                'document_title',
                'activity_subtitle',
                'institution_name',
                'document_place',
                'maker_name',
                'maker_nip',
            ]);
            $table->foreignId('officer_id')->nullable(false)->change();
        });
    }
};
