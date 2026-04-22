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
        Schema::create('kategori_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique(); // MB, BSH, BSB

            $table->double('nilai_l');
            $table->double('nilai_m');
            $table->double('nilai_u');
            $table->double('nilai_crisp');

            $table->double('rentang_min');
            $table->double('rentang_max');

            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_nilai');
    }
};
