<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_attachments', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('caption');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->float('accuracy')->nullable()->after('longitude');
            $table->string('location_address')->nullable()->after('accuracy');
            $table->timestamp('captured_at')->nullable()->after('location_address');
        });
    }

    public function down(): void
    {
        Schema::table('report_attachments', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'accuracy',
                'location_address',
                'captured_at',
            ]);
        });
    }
};
