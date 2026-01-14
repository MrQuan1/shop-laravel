<?php
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$lines = 0;

foreach ($rii as $file) {
    if (!$file->isDir() && in_array($file->getExtension(), ['php', 'js', 'css'])) {
        $lines += count(file($file->getPathname()));
    }
}

echo "Tổng dòng code: $lines";
