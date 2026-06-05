<?php
$directories = ['app', 'config', 'database', 'routes'];
$errors = [];
$checked = 0;

foreach ($directories as $dir) {
    $path = __DIR__ . '/../' . $dir;
    if (!is_dir($path)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getRealPath();
            $output = [];
            $returnVar = 0;
            exec("php -l " . escapeshellarg($filePath), $output, $returnVar);
            $checked++;
            if ($returnVar !== 0) {
                $errors[] = [
                    'file' => $filePath,
                    'error' => implode("\n", $output)
                ];
            }
        }
    }
}

echo "Checked $checked files.\n";
if (empty($errors)) {
    echo "SUCCESS: No PHP syntax errors found!\n";
} else {
    echo "FAILED: Found " . count($errors) . " files with syntax errors:\n";
    foreach ($errors as $error) {
        echo "File: " . $error['file'] . "\n";
        echo $error['error'] . "\n\n";
    }
}
