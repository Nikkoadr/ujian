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
        Schema::create('ujian_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal')->onDelete('cascade');
            $table->enum('status', ['sedang mengerjakan', 'selesai'])->default('sedang mengerjakan');
            $table->integer('pelanggaran')->default(0);
            $table->timestamp('mulai_ujian')->nullable();
            $table->timestamp('selesai_ujian')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'jadwal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_siswa');
    }
};
