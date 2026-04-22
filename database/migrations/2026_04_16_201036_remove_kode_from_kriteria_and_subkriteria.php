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
            $table->dropColumn('kode');
        });

        Schema::table('subkriteria', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kriteria', function (Blueprint $table) {
            $table->string('kode', 5)->nullable()->after('id');
        });

        Schema::table('subkriteria', function (Blueprint $table) {
            $table->string('kode', 10)->nullable()->after('kriteria_id');
        });
    }
};
