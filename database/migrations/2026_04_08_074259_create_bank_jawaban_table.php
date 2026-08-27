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
        Schema::create('bank_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_pertanyaan_id')->constrained('bank_pertanyaan')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->text('teks_jawaban')->nullable();
            $table->string('gambar_jawaban')->nullable();
            $table->boolean('jawaban_benar')->default(false);
            $table->timestamps();

            $table->index('bank_pertanyaan_id');
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_jawaban');
    }
};
