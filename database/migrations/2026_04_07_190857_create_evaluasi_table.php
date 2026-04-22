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
        Schema::create('evaluasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periode_id')->constrained('periode_penilaian');
            $table->foreignId('siswa_id')->constrained('siswa');

            $table->double('nilai_akhir');
            $table->string('kategori_akhir', 5);

            $table->boolean('is_final')->default(false);

            $table->timestamp('created_at')->useCurrent();
            $table->unique(['periode_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluasi');
    }
};
