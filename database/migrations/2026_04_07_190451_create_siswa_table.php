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
        Schema::create('siswa', function (Blueprint $table) {
            $table->string('id_siswa', 10)->primary();
            $table->string('kelas_id', 10);
            $table->string('wali_murid_id', 10)->nullable();
            $table->foreign('kelas_id')->references('id_kelas')->on('kelas')->cascadeOnDelete();
            $table->foreign('wali_murid_id')->references('id_user')->on('users')->nullOnDelete();

            $table->string('kode', 10)->nullable();
            $table->string('name', 50);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L','P']);

            $table->string('nama_orang_tua', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp_orang_tua', 20)->nullable();

            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
