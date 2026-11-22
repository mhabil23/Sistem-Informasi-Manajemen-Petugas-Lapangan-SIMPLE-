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
        Schema::table('training_events', function (Blueprint $table) {
            if (!Schema::hasColumn('training_events', 'document_number')) {
                $table->string('document_number')->nullable()->after('title');
            }
            if (!Schema::hasColumn('training_events', 'document_title')) {
                $table->string('document_title')->nullable()->after('document_number');
            }
            if (!Schema::hasColumn('training_events', 'activity_subtitle')) {
                $table->string('activity_subtitle')->nullable()->after('document_title');
            }
            if (!Schema::hasColumn('training_events', 'institution_name')) {
                $table->string('institution_name')->nullable()->after('activity_subtitle');
            }
            if (!Schema::hasColumn('training_events', 'document_place')) {
                $table->string('document_place')->nullable()->after('location');
            }
            if (!Schema::hasColumn('training_events', 'maker_name')) {
                $table->string('maker_name')->nullable()->after('document_place');
            }
            if (!Schema::hasColumn('training_events', 'maker_nip')) {
                $table->string('maker_nip')->nullable()->after('maker_name');
            }
            if (!Schema::hasColumn('training_events', 'maker_signature_path')) {
                $table->string('maker_signature_path')->nullable()->after('maker_nip');
            }
            if (!Schema::hasColumn('training_events', 'notes')) {
                $table->text('notes')->nullable()->after('maker_signature_path');
            }
            if (!Schema::hasColumn('training_events', 'assignment_id')) {
                $table->foreignId('assignment_id')->nullable()->after('created_by')->constrained('assignments')->nullOnDelete();
            }
        });

        Schema::table('training_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('training_participants', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('attendance_status');
            }
            if (!Schema::hasColumn('training_participants', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('signature_path');
            }
            if (!Schema::hasColumn('training_participants', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('signed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_participants', function (Blueprint $table) {
            $cols = ['signature_path', 'signed_at', 'sort_order'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('training_participants', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('training_events', function (Blueprint $table) {
            if (Schema::hasColumn('training_events', 'assignment_id')) {
                $table->dropForeign(['assignment_id']);
                $table->dropColumn('assignment_id');
            }
            $cols = [
                'document_number',
                'document_title',
                'activity_subtitle',
                'institution_name',
                'document_place',
                'maker_name',
                'maker_nip',
                'maker_signature_path',
                'notes',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('training_events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
