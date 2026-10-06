<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('periode_ujian', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->nullable()->after('nama_periode');
            $table->string('semester', 20)->nullable()->after('tahun_ajaran');
        });

        // Backfill periode lama dari tanggalnya: tahun = tahun mulai/selesai,
        // bulan mulai Jul-Des = Ganjil.
        foreach (DB::table('periode_ujian')->whereNull('tahun_ajaran')->get() as $periode) {
            $mulai = (int) date('Y', strtotime($periode->tanggal_mulai));
            $selesai = (int) date('Y', strtotime($periode->tanggal_selesai));
            $bulan = (int) date('n', strtotime($periode->tanggal_mulai));

            DB::table('periode_ujian')
                ->where('id', $periode->id)
                ->update([
                    'tahun_ajaran' => $mulai.'/'.$selesai,
                    'semester' => $bulan >= 7 ? 'Ganjil' : 'Genap',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_ujian', function (Blueprint $table) {
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });
    }
};
