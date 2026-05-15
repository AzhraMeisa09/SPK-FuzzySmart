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
        Schema::create('subkriteria', function (Blueprint $table) {
            $table->string('id_subkriteria', 10)->primary();
            $table->string('kriteria_id', 10);
            $table->foreign('kriteria_id')->references('id_kriteria')->on('kriteria')->cascadeOnDelete();

            $table->string('nama_subkriteria', 255);

            $table->text('rubrik_mb');
            $table->text('rubrik_bsh');
            $table->text('rubrik_bsb');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkriteria');
    }
};
