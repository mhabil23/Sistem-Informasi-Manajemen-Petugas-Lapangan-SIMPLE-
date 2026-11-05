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
        Schema::create('respondent_basts', function (Blueprint $table) {
            $table->id();
            $table->string('bast_number')->unique();
            $table->date('bast_date')->index();
            $table->foreignId('respondent_id')->constrained('respondents')->cascadeOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->nullOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->text('handover_items'); // Rincian berkas / dokumen / barang yang diserahterimakan
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'finalized', 'archived'])->default('finalized')->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('officer_basts', function (Blueprint $table) {
            $table->id();
            $table->string('bast_number')->unique();
            $table->date('bast_date')->index();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('officer_id')->constrained('users')->cascadeOnDelete();
            $table->text('description'); // Keterangan serah terima tugas/berkas
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'finalized', 'archived'])->default('finalized')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officer_basts');
        Schema::dropIfExists('respondent_basts');
    }
};

