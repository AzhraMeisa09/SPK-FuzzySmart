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
        Schema::create('template_rekomendasi_umum', function (Blueprint $table) {
            $table->string('id_template_umum', 10)->primary();
            $table->string('kategori', 10); // MB, BSH, BSB
            $table->text('isi'); 
            $table->string('prioritas', 20)->default('biasa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_rekomendasi_umum');
    }
};
