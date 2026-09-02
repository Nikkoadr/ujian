<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('soal_bank_pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soal_id')->constrained('soal')->onDelete('cascade');
            $table->foreignId('bank_pertanyaan_id')->constrained('bank_pertanyaan')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['soal_id', 'bank_pertanyaan_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('soal_bank_pertanyaan');
    }
};
