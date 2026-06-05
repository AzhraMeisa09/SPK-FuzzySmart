<?php
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    print_r($tables);
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
