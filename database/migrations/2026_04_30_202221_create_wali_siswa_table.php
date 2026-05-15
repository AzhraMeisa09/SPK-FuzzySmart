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
        Schema::create('wali_siswa', function (Blueprint $table) {
            $table->string('id_wali_siswa', 10)->primary();
            $table->string('user_id', 10);
            $table->string('siswa_id', 10);
            $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
            $table->foreign('siswa_id')->references('id_siswa')->on('siswa')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_siswa');
    }
};
