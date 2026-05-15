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
        Schema::create('detail_evaluasi', function (Blueprint $table) {
            $table->string('id_detail_evaluasi', 10)->primary();
            $table->string('evaluasi_id', 10);
            $table->string('subkriteria_id', 10);
            
            $table->foreign('evaluasi_id')->references('id_evaluasi')->on('evaluasi')->cascadeOnDelete();
            $table->foreign('subkriteria_id')->references('id_subkriteria')->on('subkriteria');

            $table->double('nilai_crisp');
            $table->double('nilai_normalisasi')->nullable();
            $table->string('kategori', 10)->nullable();
            $table->text('rekomendasi_detail')->nullable();
            $table->double('bobot_snapshot');

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_evaluasi');
    }
};
