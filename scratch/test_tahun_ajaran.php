<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TahunAjaran;

try {
    $name = "2026/2027";
    echo "Checking if $name exists...\n";
    $exists = TahunAjaran::where('nama', $name)->exists();
    if ($exists) {
        echo "Error: $name already exists!\n";
    } else {
        echo "Creating $name...\n";
        TahunAjaran::create([
            'nama' => $name,
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2027-07-01',
            'is_aktif' => false
        ]);
        echo "Success!\n";
    }
} catch (\Exception $e) {
    echo "Caught: " . $e->getMessage() . "\n";
}
