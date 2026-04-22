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
        Schema::create('subkriteria', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kriteria_id')
                ->constrained('kriteria')
                ->cascadeOnDelete();

            $table->string('kode', 10);
            $table->string('nama', 250);

            $table->text('rubrik_mb');
            $table->text('rubrik_bsh');
            $table->text('rubrik_bsb');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subkriteria');
    }
};
