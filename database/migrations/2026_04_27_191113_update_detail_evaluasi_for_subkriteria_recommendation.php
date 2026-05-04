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
        Schema::table('detail_evaluasi', function (Blueprint $table) {
            // Drop kriteria_id if it's there
            if (Schema::hasColumn('detail_evaluasi', 'kriteria_id')) {
                $table->dropForeign(['kriteria_id']);
                $table->dropColumn('kriteria_id');
            }
            
            if (!Schema::hasColumn('detail_evaluasi', 'subkriteria_id')) {
                $table->foreignId('subkriteria_id')->after('evaluasi_id')->constrained('subkriteria');
            }

            if (!Schema::hasColumn('detail_evaluasi', 'kategori')) {
                $table->string('kategori', 5)->after('nilai'); // MB, BSH, BSB
            }

            if (!Schema::hasColumn('detail_evaluasi', 'rekomendasi_detail')) {
                $table->text('rekomendasi_detail')->nullable()->after('kategori');
            }
            
            if (Schema::hasColumn('detail_evaluasi', 'nilai') && !Schema::hasColumn('detail_evaluasi', 'nilai_crisp')) {
                $table->renameColumn('nilai', 'nilai_crisp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_evaluasi', function (Blueprint $table) {
            $table->renameColumn('nilai_crisp', 'nilai');
            $table->dropColumn(['subkriteria_id', 'kategori', 'rekomendasi_detail']);
            $table->foreignId('kriteria_id')->nullable()->constrained('kriteria');
        });
    }
};
