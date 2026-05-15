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
        Schema::create('laporan_evaluasi', function (Blueprint $table) {
            $table->string('id_laporan', 10)->primary();
            $table->string('evaluasi_id', 10);
            $table->string('siswa_id', 10);
            $table->string('kelas_id', 10);
            $table->string('tahun_ajaran_id', 10);

            $table->foreign('evaluasi_id')->references('id_evaluasi')->on('evaluasi');
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa');
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas');
            $table->foreign('tahun_ajaran_id')->references('id_tahun_ajaran')->on('tahun_ajaran');

            $table->string('semester', 20);
            $table->string('file_path', 255)->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_evaluasi');
    }
};
