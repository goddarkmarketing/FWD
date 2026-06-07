<?php
$raw = file_get_contents(__DIR__ . '/next-data-stroke.json');
// Remove BOM
$raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);

if (preg_match('/"riders"\s*:\s*\[/', $raw, $m, PREG_OFFSET_CAPTURE)) {
    $pos = $m[0][1];
    echo substr($raw, $pos, 3000) . "\n";
}

preg_match_all('/https?:\\\/\\\/[^"\\\\]+/', $raw, $urls);
$urls = array_unique($urls[0]);
foreach ($urls as $u) {
    $u = stripcslashes($u);
    if (stripos($u, 'api') !== false || stripos($u, 'quote') !== false || stripos($u, 'premium') !== false) {
        echo $u . "\n";
    }
}
