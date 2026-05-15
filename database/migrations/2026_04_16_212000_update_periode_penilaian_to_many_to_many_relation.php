<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel pivot periode_kelas jika belum ada
        if (!Schema::hasTable('periode_kelas')) {
            Schema::create('periode_kelas', function (Blueprint $table) {
                $table->string('id_periode_kelas', 10)->primary();
                $table->string('periode_id', 10);
                $table->string('kelas_id', 10);
                $table->foreign('periode_id')->references('id_periode')->on('periode_penilaian')->onDelete('cascade');
                $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['periode_id', 'kelas_id']);
            });
        }

        // 2. Modifikasi tabel periode_penilaian (Bersihkan constraint lama jika masih ada)
        Schema::table('periode_penilaian', function (Blueprint $table) {
            if (Schema::hasColumn('periode_penilaian', 'kelas_id')) {
                $table->dropForeign(['kelas_id']);
                $table->dropColumn('kelas_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_kelas');
    }
};
