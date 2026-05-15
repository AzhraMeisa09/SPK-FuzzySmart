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
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->string('id_evaluasi', 10)->primary();

            $table->string('periode_id', 10);
            $table->string('siswa_id', 10);
            $table->string('template_umum_id', 10)->nullable();

            $table->foreign('periode_id')->references('id_periode')->on('periode_penilaian');
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa');
            $table->foreign('template_umum_id')->references('id_template_umum')->on('template_rekomendasi_umum')->nullOnDelete();

            $table->double('nilai_akhir');
            $table->string('kategori_akhir', 10);

            $table->text('rekomendasi')->nullable();
            $table->text('catatan_guru')->nullable();

            $table->boolean('is_final')->default(false);

            $table->timestamp('created_at')->useCurrent();
            $table->unique(['periode_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};
