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
        Schema::table('officer_bast_recipients', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('origin_role');
            $table->timestamp('signed_at')->nullable()->after('signature_path');
            $table->boolean('goods_received')->default(false)->after('signed_at');
            $table->timestamp('goods_received_at')->nullable()->after('goods_received');
            $table->text('verification_note')->nullable()->after('goods_received_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('officer_bast_recipients', function (Blueprint $table) {
            $table->dropColumn([
                'signature_path',
                'signed_at',
                'goods_received',
                'goods_received_at',
                'verification_note',
            ]);
        });
    }
};

