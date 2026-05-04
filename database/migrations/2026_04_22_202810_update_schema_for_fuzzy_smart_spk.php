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
        Schema::table('kriteria', function (Blueprint $table) {
            $table->string('kode', 5)->nullable()->after('id');
        });

        Schema::table('evaluasi', function (Blueprint $table) {
            $table->text('rekomendasi')->nullable()->after('kategori_akhir');
        });

        Schema::table('detail_evaluasi', function (Blueprint $table) {
            $table->double('nilai_normalisasi')->nullable()->after('nilai');
        });

        Schema::table('template_rekomendasi', function (Blueprint $table) {
            $table->foreignId('subkriteria_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriteria', function (Blueprint $table) {
            $table->dropColumn('kode');
        });

        Schema::table('evaluasi', function (Blueprint $table) {
            $table->dropColumn('rekomendasi');
        });

        Schema::table('detail_evaluasi', function (Blueprint $table) {
            $table->dropColumn('nilai_normalisasi');
        });

        Schema::table('template_rekomendasi', function (Blueprint $table) {
            $table->foreignId('subkriteria_id')->nullable(false)->change();
        });
    }
};
