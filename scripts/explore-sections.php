<?php
$files = glob(__DIR__ . '/../data/fwd-pages/*.htm');
$keys = [];
foreach (array_slice($files, 0, 10) as $file) {
    $html = file_get_contents($file);
    if (!preg_match('/id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $m)) continue;
    $data = json_decode($m[1], true);
    $sections = $data['props']['pageProps']['product']['sections'] ?? [];
    $slug = basename($file, '.htm');
    echo "=== $slug ===\n";
    foreach ($sections as $s) {
        $k = $s['key'] ?? '?';
        $keys[$k] = ($keys[$k] ?? 0) + 1;
        $props = array_keys($s['props'] ?? []);
        echo "  $k: " . implode(', ', array_slice($props, 0, 8)) . "\n";
    }
}
echo "\nAll section keys:\n";
print_r($keys);
