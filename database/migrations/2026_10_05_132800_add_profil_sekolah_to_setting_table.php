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
        Schema::table('setting', function (Blueprint $table) {
            $table->string('nama_sekolah', 255)->nullable()->after('anti_nyontek');
            $table->string('alamat_sekolah', 255)->nullable()->after('nama_sekolah');
            $table->string('nama_kepala_sekolah', 255)->nullable()->after('alamat_sekolah');
            $table->string('nama_wakakurikulum', 255)->nullable()->after('nama_kepala_sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting', function (Blueprint $table) {
            $table->dropColumn(['nama_sekolah', 'alamat_sekolah', 'nama_kepala_sekolah', 'nama_wakakurikulum']);
        });
    }
};
