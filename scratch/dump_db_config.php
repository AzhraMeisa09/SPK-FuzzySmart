<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Default DB connection: " . config('database.default') . "\n";
echo "SQLite DB path: " . config('database.connections.sqlite.database') . "\n";
echo "MySQL DB path/host: " . config('database.connections.mysql.host') . "\n";
echo "MySQL Database: " . config('database.connections.mysql.database') . "\n";
