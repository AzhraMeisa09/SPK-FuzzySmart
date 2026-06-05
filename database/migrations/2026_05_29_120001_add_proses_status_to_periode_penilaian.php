<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum periode_penilaian.status untuk menambah nilai 'proses'
        DB::statement("ALTER TABLE periode_penilaian MODIFY COLUMN status ENUM('draft','aktif','proses','final') DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula (tanpa 'proses')
        DB::statement("UPDATE periode_penilaian SET status = 'aktif' WHERE status = 'proses'");
        DB::statement("ALTER TABLE periode_penilaian MODIFY COLUMN status ENUM('draft','aktif','final') DEFAULT 'draft'");
    }
};
