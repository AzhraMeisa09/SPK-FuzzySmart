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
            $table->string('id_penilaian', 10)->primary();

            $table->string('jadwal_sub_id', 10);
            $table->string('siswa_id', 10);
            $table->string('guru_id', 10);
            $table->string('kategori_id', 10)->nullable();

            $table->foreign('jadwal_sub_id')->references('id_jadwal_sub')->on('jadwal_subkriteria')->cascadeOnDelete();
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('guru_id')->references('id_user')->on('users')->cascadeOnDelete();
            $table->foreign('kategori_id')->references('id_kategori')->on('kategori_nilai')->nullOnDelete();

            $table->double('nilai_l')->nullable();
            $table->double('nilai_m')->nullable();
            $table->double('nilai_u')->nullable();
            $table->double('nilai_crisp')->nullable();

            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('draft');

            $table->timestamps();
            $table->unique(['jadwal_sub_id', 'siswa_id']);
            
            $table->index('siswa_id');
            $table->index('guru_id');
            $table->index('jadwal_sub_id');
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
