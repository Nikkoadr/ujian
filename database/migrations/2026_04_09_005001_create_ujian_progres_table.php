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
        Schema::create('ujian_progres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('mapel_id')->constrained('mapel');
            $table->foreignId('soal_id')->constrained('soal');
            $table->foreignId('jawaban_id')->nullable()->constrained('jawaban'); // null jika ragu/belum isi
            $table->boolean('is_ragu')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_progres');
    }
};
