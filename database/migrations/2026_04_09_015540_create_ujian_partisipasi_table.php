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
        Schema::create('ujian_partisipasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapel')->onDelete('cascade');
            $table->enum('status', ['sedang mengerjakan', 'selesai'])->default('sedang mengerjakan');
            $table->timestamp('mulai_ujian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_partisipasi');
    }
};
