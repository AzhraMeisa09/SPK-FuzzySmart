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
        Schema::create('kelas', function (Blueprint $table) {
            $table->string('id_kelas', 10)->primary();
            $table->string('tahun_ajaran_id', 10);
            $table->foreign('tahun_ajaran_id')->references('id_tahun_ajaran')->on('tahun_ajaran')->cascadeOnDelete();
            $table->string('nama_kelas', 50);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
