<?php

$files = [
    fopen('file1.txt', 'r'),	
    fopen('file2.txt', 'r'),	
];

function executeSync(array $files)
{
    echo "Executando sincrono" . PHP_EOL;
    $startTime = microtime(true);

    foreach ($files as $file) {
        echo fgets($file) . PHP_EOL;
    }

    $endTime = microtime(true);
    echo "Tempo total: " . ($endTime - $startTime) . PHP_EOL;
}

executeSync($files);