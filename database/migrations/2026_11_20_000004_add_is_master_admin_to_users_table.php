<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_master_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_master_admin')->default(false)->after('can_switch_role');
            });
        }

        // Set Administrator SIMPLE as Master Admin
        DB::table('users')
            ->where('username', 'admin')
            ->orWhere('email', 'admin@simple.go.id')
            ->orWhere('name', 'Administrator SIMPLE')
            ->update([
                'is_master_admin' => true,
                'role' => 'admin',
                'status' => 'active',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_master_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_master_admin');
            });
        }
    }
};
