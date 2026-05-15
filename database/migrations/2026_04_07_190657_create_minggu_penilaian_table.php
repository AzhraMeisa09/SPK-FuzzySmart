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
        Schema::create('minggu_penilaian', function (Blueprint $table) {
            $table->string('id_minggu', 10)->primary();
            $table->string('periode_id', 10);
            $table->foreign('periode_id')->references('id_periode')->on('periode_penilaian')->cascadeOnDelete();
            
            $table->integer('minggu_ke');
            $table->string('tema', 100)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->unique(['periode_id', 'minggu_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minggu_penilaian');
    }
};
