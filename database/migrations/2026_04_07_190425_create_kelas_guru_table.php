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
        Schema::create('kelas_guru', function (Blueprint $table) {
            $table->string('id_kelas_guru', 10)->primary();
            $table->string('kelas_id', 10);
            $table->string('guru_id', 10);
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->cascadeOnDelete();
            $table->foreign('guru_id')->references('id_user')->on('users')->cascadeOnDelete();
            $table->unique(['kelas_id','guru_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_guru');
    }
};
