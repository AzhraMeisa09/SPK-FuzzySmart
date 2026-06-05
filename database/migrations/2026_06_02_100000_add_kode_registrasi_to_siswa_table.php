<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('kode_registrasi', 10)->nullable()->unique()->after('kode');
        });

        // Auto-generate kode registrasi untuk siswa yang sudah ada
        $siswaList = DB::table('siswa')->whereNull('kode_registrasi')->get();

        foreach ($siswaList as $siswa) {
            $kode = null;
            do {
                $random = strtoupper(Str::random(2)) . rand(0, 9) . strtoupper(Str::random(1)) . rand(0, 9);
                $kode = 'TKP-' . $random;
            } while (DB::table('siswa')->where('kode_registrasi', $kode)->exists());

            DB::table('siswa')
                ->where('id_siswa', $siswa->id_siswa)
                ->update(['kode_registrasi' => $kode]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['kode_registrasi']);
            $table->dropColumn('kode_registrasi');
        });
    }
};
