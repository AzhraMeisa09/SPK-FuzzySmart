<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
DB::reconnect();

use App\Models\User;

$users = User::all();
echo "Total Users: " . $users->count() . "\n";
foreach ($users as $u) {
    echo "- Name: {$u->nama_lengkap}, Email: {$u->email}, Role: {$u->role}\n";
}
