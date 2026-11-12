<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('officer_reports', 'st_number')) {
                $table->string('st_number', 150)->nullable()->after('basis_st');
            }
            if (!Schema::hasColumn('officer_reports', 'st_date')) {
                $table->date('st_date')->nullable()->after('st_number');
            }
            if (!Schema::hasColumn('officer_reports', 'obstacles')) {
                $table->text('obstacles')->nullable()->after('work_description');
            }
            if (!Schema::hasColumn('officer_reports', 'solutions')) {
                $table->text('solutions')->nullable()->after('obstacles');
            }
            if (!Schema::hasColumn('officer_reports', 'expense_items')) {
                $table->json('expense_items')->nullable()->after('include_vehicle_statement');
            }
            if (!Schema::hasColumn('officer_reports', 'ppk_name')) {
                $table->string('ppk_name', 150)->nullable()->after('expense_items');
            }
            if (!Schema::hasColumn('officer_reports', 'ppk_nip')) {
                $table->string('ppk_nip', 50)->nullable()->after('ppk_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('officer_reports', function (Blueprint $table) {
            $columns = array_filter(
                ['st_number', 'st_date', 'obstacles', 'solutions', 'expense_items', 'ppk_name', 'ppk_nip'],
                fn ($col) => Schema::hasColumn('officer_reports', $col)
            );
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

