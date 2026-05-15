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
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->string('id_rekomendasi', 10)->primary();

            $table->string('evaluasi_id', 10);
            $table->string('siswa_id', 10);
            $table->string('subkriteria_id', 10);
            $table->string('template_id', 10);

            $table->foreign('evaluasi_id')->references('id_evaluasi')->on('evaluasi')->cascadeOnDelete();
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa');
            $table->foreign('subkriteria_id')->references('id_subkriteria')->on('subkriteria');
            $table->foreign('template_id')->references('id_template')->on('template_rekomendasi');

            $table->string('kategori_hasil', 10);
            $table->text('catatan_guru')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
