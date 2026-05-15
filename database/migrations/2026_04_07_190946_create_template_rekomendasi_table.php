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
        Schema::create('template_rekomendasi', function (Blueprint $table) {
            $table->string('id_template', 10)->primary();
            $table->string('subkriteria_id', 10)->nullable();
            $table->foreign('subkriteria_id')->references('id_subkriteria')->on('subkriteria')->nullOnDelete();

            $table->string('kategori', 10);
            $table->text('isi');
            $table->string('prioritas', 20)->default('sedang');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_rekomendasi');
    }
};
