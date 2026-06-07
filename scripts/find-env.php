<?php
$raw = file_get_contents(__DIR__ . '/next-data-stroke.json');
preg_match_all('/"(NEXT_PUBLIC_[A-Z0-9_]+)"\s*:\s*"([^"]*)"/', $raw, $m, PREG_SET_ORDER);
foreach ($m as $row) {
    echo $row[1] . ' = ' . $row[2] . "\n";
}
preg_match_all('/"([A-Z_]*(?:API|URL|HOST|BFF|QUOTE)[A-Z_]*)"\s*:\s*"([^"]+)"/', $raw, $m2, PREG_SET_ORDER);
foreach (array_slice($m2, 0, 30) as $row) {
    echo $row[1] . ' = ' . $row[2] . "\n";
}
