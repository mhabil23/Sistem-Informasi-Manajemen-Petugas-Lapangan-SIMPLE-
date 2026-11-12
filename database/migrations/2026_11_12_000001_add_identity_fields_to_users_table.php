<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 32)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'rank')) {
                $table->string('rank', 100)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 150)->nullable()->after('rank');
            }
        });

        // Isi NIK & Jabatan default untuk akun petugas contoh yang sudah ada agar langsung terisi otomatis
        DB::table('users')
            ->where('username', 'petugas1')
            ->whereNull('nik')
            ->update([
                'nik' => '7408011505950001',
                'rank' => '-',
                'position' => 'Mitra BPS Kabupaten Kolaka Utara',
            ]);

        DB::table('users')
            ->where('username', 'petugas2')
            ->whereNull('nik')
            ->update([
                'nik' => '7408022008970002',
                'rank' => '-',
                'position' => 'Mitra BPS Kabupaten Kolaka Utara',
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(['nik', 'rank', 'position'], fn ($col) => Schema::hasColumn('users', $col));
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
