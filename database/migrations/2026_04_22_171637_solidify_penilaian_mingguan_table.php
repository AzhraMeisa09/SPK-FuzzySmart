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
        Schema::table('penilaian_mingguan', function (Blueprint $table) {
            // 1. Tambahkan kolom Skor Denormalisasi (Section #5 Blueprint)
            // Menggunakan double agar presisi untuk perhitungan Fuzzy SMART
            if (!Schema::hasColumn('penilaian_mingguan', 'nilai_l')) {
                $table->double('nilai_l')->nullable()->after('kategori_id');
                $table->double('nilai_m')->nullable()->after('nilai_l');
                $table->double('nilai_u')->nullable()->after('nilai_m');
                $table->double('nilai_crisp')->nullable()->after('nilai_u');
            }

            // 2. Tambahkan Index untuk Performa SPK (Penyempurnaan Poin #4)
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
        Schema::table('penilaian_mingguan', function (Blueprint $table) {
            $table->dropIndex(['siswa_id']);
            $table->dropIndex(['guru_id']);
            $table->dropIndex(['jadwal_sub_id']);
            $table->dropColumn(['nilai_l', 'nilai_m', 'nilai_u', 'nilai_crisp']);
        });
    }
};
