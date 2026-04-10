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
        Schema::create('mapel', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_ujian');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            $table->time('durasi');
            $table->string('token');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('tingkat_id')->constrained('tingkat')->onDelete('cascade');
            $table->foreignId('kompetensi_keahlian_id')->nullable()->constrained('kompetensi_keahlian')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel');
    }
};
