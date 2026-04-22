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
        Schema::table('periode_penilaian', function (Blueprint $table) {
            $table->enum('status', ['draft', 'aktif', 'final'])->default('draft')->after('is_aktif');
            $table->timestamp('finalized_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periode_penilaian', function (Blueprint $table) {
            $table->dropColumn(['status', 'finalized_at']);
        });
    }
};
