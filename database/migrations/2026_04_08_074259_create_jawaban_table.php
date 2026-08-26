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
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal')->onDelete('cascade');
            $table->integer('urutan')->default(0); // 0=A, 1=B, 2=C, 3=D
            $table->text('teks_jawaban')->nullable();
            $table->string('gambar_jawaban')->nullable();
            $table->boolean('jawaban_benar')->default(false);
            $table->timestamps();

            // Index untuk performa
            $table->index('soal_id');
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};
