<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            // Simpan rekomendasi sistem asli (sebelum guru override)
            $table->string('kategori_rekomendasi_sistem', 10)->nullable()->after('kategori_akhir');
            // Keputusan akhir guru (bisa sama atau beda dari rekomendasi sistem)
            $table->string('kategori_keputusan_guru', 10)->nullable()->after('kategori_rekomendasi_sistem');
            // Status validasi oleh guru
            $table->enum('status_validasi', ['menunggu_review', 'disetujui_guru'])->default('menunggu_review')->after('catatan_guru');
            // Timestamp validasi
            $table->timestamp('tanggal_validasi')->nullable()->after('status_validasi');
            // Guru yang memvalidasi
            $table->string('id_guru_validator', 10)->nullable()->after('tanggal_validasi');
            $table->foreign('id_guru_validator')->references('id_user')->on('users')->nullOnDelete();
        });

        // Backfill: evaluasi yang sudah is_final = true dianggap sudah disetujui guru
        DB::statement("UPDATE evaluasi SET status_validasi = 'disetujui_guru', kategori_rekomendasi_sistem = kategori_akhir, kategori_keputusan_guru = kategori_akhir WHERE is_final = 1");
    }

    public function down(): void
    {
        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropForeign(['id_guru_validator']);
            $table->dropColumn([
                'kategori_rekomendasi_sistem',
                'kategori_keputusan_guru',
                'status_validasi',
                'tanggal_validasi',
                'id_guru_validator',
            ]);
        });
    }
};
