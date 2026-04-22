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
        Schema::create('penilaian_mingguan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('jadwal_sub_id')->constrained('jadwal_subkriteria');
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->foreignId('guru_id')->constrained('users');

            $table->foreignId('kategori_id')->constrained('kategori_nilai');

            $table->text('catatan')->nullable();

            $table->enum('status', ['draft','final'])->default('draft');

            $table->timestamps();
            $table->unique(['jadwal_sub_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_mingguan');
    }
};
