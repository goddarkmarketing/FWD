<?php
$raw = file_get_contents(__DIR__ . '/next-data-stroke.json');
preg_match_all('/https?:[^"\\s]{10,120}/', $raw, $m);
$urls = array_unique($m[0]);
foreach ($urls as $u) {
    $u = str_replace('\\/', '/', $u);
    if (preg_match('/(api|bff|quote|premium|graphql|lambda)/i', $u)) {
        echo $u . "\n";
    }
}

// search bff literal
$pos = stripos($raw, 'bff');
while ($pos !== false && $pos < strlen($raw)) {
    echo substr($raw, max(0, $pos - 80), 200) . "\n---\n";
    $pos = stripos($raw, 'bff', $pos + 4);
    static $count = 0;
    if (++$count > 8) break;
}
