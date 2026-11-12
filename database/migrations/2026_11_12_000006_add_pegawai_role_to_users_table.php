<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'pegawai') NOT NULL DEFAULT 'petugas'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::table('users')->where('role', 'pegawai')->update(['role' => 'petugas']);
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas') NOT NULL DEFAULT 'petugas'");
        }
    }
};

