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
        Schema::create('portofolio_images', function (Blueprint $table) {
            $table->string('id_portofolio_images', 10)->primary();
            $table->string('portofolio_id', 10);
            $table->foreign('portofolio_id')->references('id_portofolio')->on('portofolio')->cascadeOnDelete();
            $table->string('file_path', 255);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portofolio_images');
    }
};
