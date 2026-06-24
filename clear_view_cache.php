<?php
// Script untuk clear Blade view cache
$dir = __DIR__ . '/storage/framework/views';
$count = 0;
foreach (glob($dir . '/*.php') as $file) {
    unlink($file);
    $count++;
}
echo "Deleted $count cached view files.\n";
