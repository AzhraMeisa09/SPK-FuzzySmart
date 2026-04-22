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
            $table->id();
            $table->foreignId('minggu_id')->constrained('minggu_penilaian')->cascadeOnDelete();
            $table->foreignId('subkriteria_id')->constrained('subkriteria')->cascadeOnDelete();

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
