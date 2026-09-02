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
        Schema::create('progres_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jadwal_id')->constrained('jadwal')->cascadeOnDelete();
            $table->foreignId('bank_pertanyaan_id')->constrained('bank_pertanyaan')->cascadeOnDelete();
            $table->foreignId('bank_jawaban_id')->nullable()->constrained('bank_jawaban')->nullOnDelete();
            $table->boolean('is_ragu')->default(false);
            $table->timestamps();
            $table->unique(['user_id', 'jadwal_id', 'bank_pertanyaan_id'], 'progres_user_jadwal_soal_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progres_siswa');
    }
};
