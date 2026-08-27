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
        Schema::create('bank_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->string('gambar_soal')->nullable();
            $table->enum('jenis_soal', ['pg', 'essay'])->default('pg');
            $table->integer('bobot_nilai')->default(1);
            $table->unsignedBigInteger('kunci_jawaban_id')->nullable();
            $table->timestamps();

            $table->index('bank_soal_id');
            $table->index('jenis_soal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_pertanyaan');
    }
};
