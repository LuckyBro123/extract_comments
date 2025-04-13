<?php

$pathes = ["\\app\\", "\\public\\"];
$types = ["html", "js", "php"];
$outputFile = "comments.txt";

function extractRussianComments($filePath) {
    $content = file_get_contents($filePath);
    $comments = [];
    
    // HTML комментарии <!-- ... -->
    preg_match_all('/<!--[\s\S]*?-->/u', $content, $htmlComments);
    
    // JS/PHP однострочные // ...
    preg_match_all('/\/\/[ ]*[А-Яа-яЁё].*$/mu', $content, $singleLine);
    
    // JS/PHP многострочные /* ... */
    preg_match_all('/\/\*\s*[А-Яа-яЁё][\s\S]*?\*\//u', $content, $multiLine);
    
    return array_merge($htmlComments[0], $singleLine[0], $multiLine[0]);
}

$allComments = [];

foreach ($pathes as $path) {
    $fullPath = __DIR__ . $path;
    if (!is_dir($fullPath)) continue;
    
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullPath));
    
    foreach ($files as $file) {
        if (in_array($file->getExtension(), $types)) {
            $comments = extractRussianComments($file->getPathname());
            $allComments = array_merge($allComments, $comments);
        }
    }
}

file_put_contents($outputFile, implode("\n", $allComments));

echo "Комментарии собраны в $outputFile";