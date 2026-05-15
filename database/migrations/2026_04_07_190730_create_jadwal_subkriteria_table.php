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
        Schema::create('jadwal_subkriteria', function (Blueprint $table) {
            $table->string('id_jadwal_sub', 10)->primary();
            $table->string('minggu_id', 10);
            $table->string('subkriteria_id', 10);
            $table->foreign('minggu_id')->references('id_minggu')->on('minggu_penilaian')->cascadeOnDelete();
            $table->foreign('subkriteria_id')->references('id_subkriteria')->on('subkriteria')->cascadeOnDelete();

            $table->integer('urutan')->nullable();
            $table->boolean('wajib')->default(true);

            $table->unique(['minggu_id','subkriteria_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_subkriteria');
    }
};
