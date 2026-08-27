<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_ujian_id')->constrained('periode_ujian')->onDelete('cascade');
            $table->string('kode_ujian')->unique();
            $table->string('nama_ujian');
            $table->date('tanggal_ujian');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('durasi');
            $table->foreignId('tingkat_id')->constrained('tingkat');
            $table->foreignId('kompetensi_keahlian_id')->nullable()->constrained('kompetensi_keahlian');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('token', 6)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ujian');
    }
};
