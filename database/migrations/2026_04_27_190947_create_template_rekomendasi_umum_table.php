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
            $table->id();
            $table->string('kategori', 5); // MB, BSH, BSB
            $table->text('isi'); // gunakan placeholder {{aspek}}
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
