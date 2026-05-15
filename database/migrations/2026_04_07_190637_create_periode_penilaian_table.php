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
        Schema::create('periode_penilaian', function (Blueprint $table) {
            $table->string('id_periode', 10)->primary();
            $table->string('tahun_ajaran_id', 10);
            $table->foreign('tahun_ajaran_id')->references('id_tahun_ajaran')->on('tahun_ajaran')->cascadeOnDelete();
            
            $table->string('nama_periode', 100);
            $table->string('semester', 20); // varchar(20) as per ERD table
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_aktif')->default(false);
            $table->enum('status', ['draft', 'aktif', 'final'])->default('draft');
            $table->timestamp('finalized_at')->nullable();
            
            $table->string('created_by', 10)->nullable();
            $table->foreign('created_by')->references('id_user')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_penilaian');
    }
};
