<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sobat_id')) {
                $table->string('sobat_id', 32)->nullable()->after('nik')->index();
            }
            if (!Schema::hasColumn('users', 'district')) {
                $table->string('district', 100)->nullable()->after('position')->index();
            }
            if (!Schema::hasColumn('users', 'village')) {
                $table->string('village', 100)->nullable()->after('district')->index();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('village');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 10)->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'birth_info')) {
                $table->string('birth_info', 150)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'education')) {
                $table->string('education', 100)->nullable()->after('birth_info');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['sobat_id', 'district', 'village', 'address', 'gender', 'birth_info', 'education'],
                fn ($col) => Schema::hasColumn('users', $col)
            );
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

