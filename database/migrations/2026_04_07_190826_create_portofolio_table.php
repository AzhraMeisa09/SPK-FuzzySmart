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
        Schema::create('portofolio', function (Blueprint $table) {
            $table->string('id_portofolio', 10)->primary();
            $table->string('siswa_id', 10);
            $table->string('guru_id', 10);
            $table->string('minggu_id', 10);
            
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->foreign('guru_id')->references('id_user')->on('users')->cascadeOnDelete();
            $table->foreign('minggu_id')->references('id_minggu')->on('minggu_penilaian')->cascadeOnDelete();

            $table->string('judul', 100);
            $table->text('deskripsi')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portofolio');
    }
};
