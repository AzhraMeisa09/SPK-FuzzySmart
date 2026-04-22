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
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('evaluasi_id')->constrained('evaluasi');
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('subkriteria_id')->constrained('subkriteria');

            $table->foreignId('template_id')->constrained('template_rekomendasi');

            $table->string('kategori_hasil', 5);

            $table->text('catatan_guru')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};
